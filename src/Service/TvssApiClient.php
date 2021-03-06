<?php

namespace CascadePublicMedia\PbsApiExplorer\Service;

use CascadePublicMedia\PbsApiExplorer\Entity\Feed;
use CascadePublicMedia\PbsApiExplorer\Entity\Headend;
use CascadePublicMedia\PbsApiExplorer\Entity\Listing;
use CascadePublicMedia\PbsApiExplorer\Entity\ScheduleProgram;
use CascadePublicMedia\PbsApiExplorer\Entity\Setting;
use CascadePublicMedia\PbsApiExplorer\Utils\ApiValueProcessor;
use CascadePublicMedia\PbsApiExplorer\Utils\FieldMapper;
use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class TvssApiClient
 *
 * @package CascadePublicMedia\PbsApiExplorer\Service
 */
class TvssApiClient extends PbsApiClientBase
{
    /**
     * @var array
     */
    protected $requiredSettings = [
        'tvss_base_uri' => 'Endpoint',
        'tvss_call_sign' => 'Call Sign',
        'tvss_api_key' => 'API Key',
    ];

    /**
     * TvssApiClient constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param FieldMapper $fieldMapper
     * @param ApiValueProcessor $apiValueProcessor
     */
    public function __construct(EntityManagerInterface $entityManager, FieldMapper $fieldMapper, ApiValueProcessor $apiValueProcessor)
    {
        parent::__construct($entityManager, $fieldMapper, $apiValueProcessor);

        if ($this->isConfigured()) {
            /** @var Setting[] $settings */
            $settings = $entityManager
                ->getRepository(Setting::class)
                ->findByIdPrefix('tvss');

            $this->createClient([
                'base_uri' => $settings['tvss_base_uri']->getValue(),
                'headers' => [
                    'X-PBSAUTH' => $settings['tvss_api_key']->getValue()
                ],
            ]);
        }
    }

    /**
     * Update Headends information from the TVSS API.
     *
     * @return array
     *   Stats information from the update() method.
     *
     * @see TvssApiClient::update()
     */
    public function updateHeadends() {
        // Remove all existing Headend instances.
        $this->entityManager
            ->createQuery('delete from ' . Headend::class)
            ->execute();
        $this->entityManager->flush();
        $this->entityManager->clear();

        $config = self::createQueryConfig(['dataKey' => 'headends']);
        $items = $this->query(
            $this->getSetting('tvss_call_sign') . '/channels',
            $config
        );
        $stats = $this->processItems(Headend::class, $items, $config);
        $this->entityManager->flush();
        return $stats;
    }

    /**
     * Update Listings information from the TVSS API by date.
     *
     * @param string $date
     *   Date of listings to retrieve in the format YYYYMMDD.
     *
     * @return array
     *   Stats information from the update() method.
     *
     * @see TvssApiClient::update()
     */
    public function updateListings($date) {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->delete(Listing::class, 'l')
            ->where('l.date = :date')
            ->setParameter('date', $date)
            ->getQuery();
        $query->execute();
        $this->entityManager->flush();
        $this->entityManager->clear();

        $config = self::createQueryConfig(['dataKey' => 'feeds']);
        $items = $this->query(
            $this->getSetting('tvss_call_sign') . '/day/' . $date,
            $config
        );

        // Listings are returned organized by feed, so each feed is processed
        // separately and the actual listings array sent through processItems().
        $stats = ['add' => 0, 'update' => 0, 'noop' => 0];
        $config['extraProps'] = [
            'date' => DateTime::createFromFormat('Ymd', $date),
        ];
        foreach ($items as $item) {
            $feed = $this->entityManager
                ->getRepository(Feed::class)
                ->find($item->cid);

            // Create the Feed instance if one does not exist.
            if (!$feed) {
                $feed = new Feed();
                $feed->setId($item->cid);
                $feed->setExternalId($item->external_id);
                $feed->setShortName($item->short_name);
                $feed->setFullName($item->full_name);
                $feed->setTimezone($item->timezone);
                $feed->setAnalogChannel($item->analog_channel);
                $feed->setDigitalChannel($item->digital_channel);
                $this->entityManager->merge($feed);
            }

            $config['extraProps']['feed'] = $feed;

            $items_stats = $this->processItems(
                Listing::class,
                $item->listings,
                $config
            );

            $stats['update'] += $items_stats['update'];
        }

        $this->entityManager->flush();
        return $stats;
    }

    /**
     * Update Listings information from the TVSS API by month.
     *
     * @param string $month
     *   Date of listings to retrieve in the format YYYYMM.
     *
     * @return array
     *   Stats information from the update() method.
     *
     * @throws \Exception
     *
     * @see TvssApiClient::update()
     */
    public function updateListingsByMonth($month) {
        $stats = ['add' => 0, 'update' => 0, 'noop' => 0];
        $start = DateTimeImmutable::createFromFormat('Ymd', $month . '01');
        if (!$start) {
            throw new BadRequestHttpException('Invalid month designation');
        }

        $interval = new DateInterval('P1D');
        $end = $start->add(new DateInterval('P1M'));
        $period = new DatePeriod($start, $interval, $end);
        /** @var DateTime $date */
        foreach ($period as $date) {
            $update_stats = $this->updateListings($date->format('Ymd'));
            foreach ($update_stats as $key => $count) {
                $stats[$key] += $count;
            }
        }

        return $stats;
    }

    /**
     * Update Programs information from the TVSS API.
     *
     * @return array
     *   Stats information from the update() method.
     *
     * @see TvssApiClient::update()
     */
    public function updatePrograms() {
        $this->entityManager
            ->createQuery('delete from ' . ScheduleProgram::class)
            ->execute();
        $this->entityManager->flush();
        $this->entityManager->clear();

        $config = self::createQueryConfig(['dataKey' => 'programs']);
        $items = $this->query(ScheduleProgram::ENDPOINT, $config);
        $stats = $this->processItems(ScheduleProgram::class, $items, $config);
        $this->entityManager->flush();
        return $stats;
    }

    /**
     * Query the TVSS API and return the result.
     *
     * @param string $url
     *   API URL to query.
     * @param array $config
     *   Query configuration options.
     *
     * @return array
     */
    private function query($url, array $config) {
        $response = $this->client->get($url, [
            'query' => $config['queryParameters'],
        ]);
        if ($response->getStatusCode() != 200) {
            throw new HttpException($response->getStatusCode());
        }
        $json = json_decode($response->getBody());
        if (isset($config['dataKey'])) {
            if (!isset($json->{$config['dataKey']})) {
                throw new BadRequestHttpException('Configured data key 
                not found in response.');
            }
            else {
                $items = $json->{$config['dataKey']};
            }
        }
        else {
            $items = $json;
        }
        return $items;
    }

    /**
     * Process items returned from a TVSS API query.
     *
     * @param $entityClass
     *   The entity to process items for.
     * @param $items
     *   Items returns from the query.
     * @param $config
     *   Query config options.
     *
     * @return array
     *   Stats about add/update/noop entities (all "update" in this case).
     */
    private function processItems($entityClass, $items, $config) {
        $stats = ['add' => 0, 'update' => 0, 'noop' => 0];

        // The TVSS API does not provide "last updated" information for any of
        // its items and object comparision will be inefficient in most
        // situations. All sync operations assume new records will be inserted.
        foreach ($items as $item) {
            $entity = new $entityClass;
            $this->propertyAccessor->setValue($entity, 'id', $item->cid);

            // Add any supplied extra properties.
            foreach ($config['extraProps'] as $property => $value) {
                $this->propertyAccessor->setValue(
                    $entity,
                    $property,
                    $value
                );
            }

            // Iterate and update all entity attributes from the API record.
            foreach ($item as $field_name => $value) {

                // The TVSS API provides the item ID at the same level as the
                // item attributes. The ID is handled above, so it is ignored
                // during processing here.
                if ($field_name == 'cid') {
                    continue;
                }

                $this->apiValueProcessor->process(
                    $entity,
                    $field_name,
                    $value
                );
            }

            // Merge changes to the entity.
            $this->entityManager->merge($entity);
            $stats['update']++;
        }

        return $stats;
    }
}

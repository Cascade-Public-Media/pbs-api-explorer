<?php

namespace CascadePublicMedia\PbsApiExplorer\Controller;

use CascadePublicMedia\PbsApiExplorer\Entity\Station;
use CascadePublicMedia\PbsApiExplorer\Service\StationManagerApiClient;
use CascadePublicMedia\PbsApiExplorer\Service\StationManagerPublicApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StationManagerController
 *
 * @package CascadePublicMedia\PbsApiExplorer\Controller
 */
class StationManagerController extends ControllerBase
{
    private static $notConfigured = 'The Station Manager API has not been configured. Visit Settings to configure it.';

    /**
     * @Route("/station-manager", name="station_manager")
     * @Security("is_granted('ROLE_USER')")
     */
    public function index()
    {
        return $this->render('station_manager/index.html.twig', [
            'controller_name' => 'StationManagerController',
        ]);
    }

    /**
     * @Route("/station-manager/stations", name="station_manager_stations")
     * @Security("is_granted('ROLE_USER')")
     *
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function stations(EntityManagerInterface $entityManager) {
        $entities = $entityManager->getRepository(Station::class)->findAll();
        return $this->render('datatable.html.twig', [
            'title' => 'Stations',
            'properties' => [
                'fullCommonName' => 'Name',
                'shortCommonName' => 'Name (short)',
                'callSign' => 'Call sign',
                'pdp' => 'PDP',
                'passportEnabled' => 'Passport',
                'updated' => 'Updated',
            ],
            'entities' => $entities,
            'update_route' => 'station_manager_stations_update',
        ]);
    }

    /**
     * @Route("/station-manager/stations/update", name="station_manager_stations_update")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param StationManagerApiClient $apiClient
     *
     * @return RedirectResponse
     */
    public function stations_update(StationManagerApiClient $apiClient) {
        if (!$apiClient->isConfigured()) {
            throw new NotFoundHttpException(self::$notConfigured);
        }
        $this->updateAll($apiClient, Station::class);
        return $this->redirectToRoute('station_manager_stations');
    }

    /**
     * @Route("/station-manager/stations/public/update", name="station_manager_stations_public_update")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param StationManagerPublicApiClient $apiClient
     *
     * @return RedirectResponse
     */
    public function stations_public_update(StationManagerPublicApiClient $apiClient) {
        $this->updateAll($apiClient, Station::class);
        return $this->redirectToRoute('station_manager_stations');
    }
}

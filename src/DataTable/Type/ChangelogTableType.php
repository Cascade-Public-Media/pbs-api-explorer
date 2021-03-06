<?php

namespace CascadePublicMedia\PbsApiExplorer\DataTable\Type;

use CascadePublicMedia\PbsApiExplorer\Entity;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\DataTableTypeInterface;

class ChangelogTableType extends DataTableTypeBase implements DataTableTypeInterface
{
    /**
     * @param DataTable $dataTable
     * @param array $options
     */
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable
            ->add('activity', TextColumn::class, [
                'label' => 'Change',
                'data' => function($context, $value) {
                    return $this->renderChangelogEntryLink($context, $value);
                },
                'raw' => TRUE,
            ])
            ->add('type', TextColumn::class, ['label' => 'Entity type'])
            ->add('resourceId', TextColumn::class, [
                'label' => 'Entity',
                'data' => function($context, $value)  {
                    return $this->renderChangelogEntity($context, $value);
                },
                'raw' => TRUE,
            ])
            ->add('updatedFields', TextColumn::class, [
                'label' => 'Updated fields',
                'data' => function($context, $value) {
                    return $this->renderChangelogUpdatedFields($context, $value);
                },
                'raw' => TRUE,
            ])
            ->add('timestamp', DateTimeColumn::class, [
                'label' => 'Timestamp (UTC)',
                'format' => 'Y-m-d H:i:s',
            ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Entity\ChangelogEntry::class
            ])
            ->addOrderBy('timestamp', DataTable::SORT_DESCENDING);
    }

    /**
     * Create a string value for a locally synced resource in a Changelog.
     *
     * @param Entity\ChangelogEntry $context
     *   Changelog entity data
     * @param string $value
     *   Resource ID from the Changelog entry.
     *
     * @return string
     *   An entity title if the entity is available locally, the resource ID
     *   otherwise.
     */
    private function renderChangelogEntity(Entity\ChangelogEntry $context, $value) {
        $str = $value;
        if ($value) {
            $type = $context->getType();
            if ($type == 'remoteasset') {
                $type = 'RemoteAsset';
            }
            else {
                $type = ucfirst($type);
            }
            $class = sprintf(
                'CascadePublicMedia\PbsApiExplorer\Entity\%s',
                $type
            );
            if (class_exists($class)) {
                $entity = $this->entityManager->getRepository($class)->find($value);
                if ($entity) {

                    switch ($class) {
                        case Entity\Franchise::class:
                            $str = $this->renderFranchiseLink($entity, $value);
                            break;
                        case Entity\Show::class:
                            $str = $this->renderShowLink($entity, $value);
                            break;
                        case Entity\Season::class:
                            $str = $this->renderSeasonLink($entity, $value);
                            break;
                        case Entity\Episode::class:
                            $str = $this->renderEpisodeLink($entity, $value);
                            break;
                        case Entity\Asset::class:
                            $str = $this->renderAssetLink($entity, $value);
                            break;
                        case Entity\Image::class:
                            $str = $this->renderImageLink($entity, $value);
                            break;
                        default:
                            $str = sprintf(
                                '<strong>%s</strong><br/><code>%s</code>',
                                (string) $entity,
                                $value
                            );
                            break;
                    }
                }
            }
        }
        return $str;
    }

    /**
     * Create a list out of an array of updated fields names for a Changelog.
     *
     * @param Entity\ChangelogEntry $context
     *   Changelog entity data
     * @param array $value
     *   Value of the "updated_fields" Changelog entry.
     *
     * @return string
     *   An HTML list of all array values, empty string otherwise.
     */
    private function renderChangelogUpdatedFields(Entity\ChangelogEntry $context, array $value) {
        if (empty($value)) {
            return '';
        }

        $str = '<ul>';
        foreach ($value as $field) {
            $str .= sprintf('<li><code>%s</code></li>', $field);
        }
        $str .= '</ul>';

        return $str;
    }
}

<?php

namespace CascadePublicMedia\PbsApiExplorer\Controller;

use CascadePublicMedia\PbsApiExplorer\Entity\Franchise;
use CascadePublicMedia\PbsApiExplorer\Entity\Genre;
use CascadePublicMedia\PbsApiExplorer\Entity\Show;
use CascadePublicMedia\PbsApiExplorer\Service\MediaManagerApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MediaManagerController
 *
 * @package CascadePublicMedia\PbsApiExplorer\Controller
 */
class MediaManagerController extends AbstractController
{
    /**
     * @Route("/media-manager", name="media_manager")
     * @return Response
     */
    public function index()
    {
        return $this->render('media_manager/index.html.twig', [
            'controller_name' => 'MediaManagerController',
        ]);
    }

    /**
     * @Route("/media-manager/genres", name="media_manager_genres")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function genres(EntityManagerInterface $entityManager) {
        $entities = $entityManager->getRepository(Genre::class)->findAll();
        return $this->render('datatable.html.twig', [
            'properties' => [
                'id' => 'ID',
                'slug' => 'Slug',
                'title' => 'Title',
            ],
            'entities' => $entities,
        ]);
    }

    /**
     * @Route("/media-manager/genres/update", name="media_manager_genres_update")
     * @param MediaManagerApiClient $apiClient
     * @return Response
     */
    public function genres_update(MediaManagerApiClient $apiClient) {
        return $this->render('entity_dumper.html.twig', [
            'entity_class' => 'Genre',
            'entities' => $apiClient->updateAndGetByEntityClass(Genre::class),
        ]);
    }


    /**
     * @Route("/media-manager/franchises", name="media_manager_franchises")
     * @param MediaManagerApiClient $apiClient
     * @return Response
     */
    public function franchises(MediaManagerApiClient $apiClient) {
        return $this->render('entity_dumper.html.twig', [
            'entity_class' => 'Franchise',
            'entities' => $apiClient->updateAndGetByEntityClass(Franchise::class),
        ]);
    }

    /**
     * @Route("/media-manager/shows", name="media_manager_shows")
     * @param MediaManagerApiClient $apiClient
     * @return Response
     */
    public function shows(MediaManagerApiClient $apiClient) {
        return $this->render('entity_dumper.html.twig', [
            'entity_class' => 'Show',
            'entities' => $apiClient->updateAndGetByEntityClass(Show::class),
        ]);
    }
}
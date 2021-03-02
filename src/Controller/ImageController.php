<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{

    /**
     * @var ImageRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ImageRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/image", name="image")
     * @return Response
     */
    public function index(): Response
    {
        $images = $this->repository->findAllVisible();
        return $this->render('image/index.html.twig', [
            'controller_name' => 'ImageController',
            'images' => $images,
        ]);
    }

    /**
     * @Route("/image/{slug}-{id}", name="image.show", requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function show($slug, $id): Response
    {
        $image = $this->repository->find($id);
        $images = $this->repository->findAllVisible();
        return $this->render('image/show.html.twig', [
            'image' => $image,
            'images' => $images,
        ]);
    }
}

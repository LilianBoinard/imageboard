<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/users", name="user")
     * @return Response
     */
    public function index(): Response
    {
        $users = $this->repository->findAllVisible();
        return $this->render('user/index.html.twig', [
            'controller_name' => 'ImageController',
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/{slug}-{id}", name="user.show", requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function show($slug, $id): Response
    {
        $user = $this->repository->find($id);
        $images = $user->getImages();
        $profile_image = $user->getProfileImage();
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'images' => $images,
            'profile_image' => $profile_image,
        ]);
    }
}

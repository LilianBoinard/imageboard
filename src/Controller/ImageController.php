<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

//    /**
//     * @Route("/comment", name="comment_create")
//     * @return Response
//     */
//    public function newComment (Request $request): Response
//    {
//        $em = $this->getDoctrine()->getManager();
//        $comment = new Comment();
//        $user = $this->get('security.token_storage')->getToken()->getUser();
//        $comment->setUser($user);
//        $form = $this->createForm(CommentType::class, $comment);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid())
//        {
//            $em->persist($comment);
//            $em->flush();
//        }
//
//        $comments = $em->getRepository('App:Comment')->findAll();
//
//        return $this->render('image/show.html.twig', [
//            'comments' => $comments,
//            'form' => $form->createView(),
//        ]);
//    }

    /**
     * @Route("/image/{slug}-{id}", name="image.show", requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function show($slug, $id, Request $request): Response
    {
        $image = $this->repository->find($id);
        $user = $image->getAuthor();
        $username = strtolower($user->getUsername());
        $comments = $image->getComment();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        return $this->render('image/show.html.twig', [
            'username' => $username,
            'image' => $image,
            'user' => $user,
            'comments' => $comments,
            'form' => $form,
        ]);
    }
}

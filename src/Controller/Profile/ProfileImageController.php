<?php
namespace App\Controller\Profile;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileImageController extends AbstractController
{
    /**
     * @var PropertyRepository
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
     * @Route("/profile", name="profile.image.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $images = $this->repository->findAll();
        return $this->render('profile/image/index.html.twig', compact('images'));
    }

    /**
     * @Route("/profile/image/create", name="profile.image.new", methods="GET|POST")
     */
    public function new(Request $request)
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($image);
            $this->em->flush();
            $this->addFlash('success', 'Succes !');
            return $this->redirectToRoute('profile.image.index');
        }

        return $this->render('profile/image/new.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/image/{id}", name="profile.image.delete", methods="DELETE")
     */
    public function delete(Image $image, Request $request) {
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->get('_token')))
        {
            $this->em->remove($image);
            $this->em->flush();
            $this->addFlash('success', 'Succes !');
        }
        return $this->redirectToRoute('profile.image.index');
    }

    /**
     * @Route("/profile/{id}", name="profile.image.edit")
     */
    public function edit(Image $image, Request $request)
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Succes !');
            return $this->redirectToRoute('profile.image.index');
        }

        return $this->render('profile/image/edit.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }
}

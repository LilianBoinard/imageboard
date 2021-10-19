<?php
namespace App\Controller\Profile;

use App\Entity\Image;
use App\Entity\User;
use App\Form\ImageType;
use App\Form\RegisterType;
use App\Repository\ImageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class ProfileImageController extends AbstractController
{
    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ImageRepository $imageRepository, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->imageRepository = $imageRepository;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    /**
     * @Route("/profile", name="profile.image.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Security $security)
    {
        /** @var User $user */
        $user = $security->getUser();
        if ($user->getAdmin()) {
            $images = $this->imageRepository->findAll();
            $users = $this->userRepository->findAll();
            return $this->render('profile/image/index.html.twig', compact('images', 'users'));
        }
        $images = $this->imageRepository->findBy([
            'author' => $user->getId(),
        ]);
        return $this->render('profile/image/index.html.twig', compact('images'));
    }

    /**
     * @Route("/profile/image/create", name="profile.image.new", methods="GET|POST")
     */
    public function newImage(Request $request, Security $security)
    {
        /** @var User $user */
        $user = $security->getUser();
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($upload = $form->get('upload')->getData())
            {
                $file = md5(uniqid()) . '.' . $user->getId() . '.' . $upload->guessExtension();
                $upload->move($this->getParameter('images_directory'), $file);
                $image->setUrl($file);
            }

            $user->addImage($image);
            $this->em->persist($user);
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
    public function deleteImage(Image $image, Request $request) {
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->get('_token')))
        {
            $this->em->remove($image);
            $this->em->flush();
            $this->addFlash('success', 'Succes !');
        }
        return $this->redirectToRoute('profile.image.index');
    }

    /**
     * @Route("/profile/image/{id}", name="profile.image.edit")
     */
    public function editImage(Image $image, Request $request, Security $security)
    {
        /** @var User $user */
        $user = $security->getUser();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($upload = $form->get('upload')->getData())
            {
                $file = md5(uniqid()) . '.' . $user->getId() . '.' . $upload->guessExtension();
                $upload->move($this->getParameter('images_directory'), $file);
                $image->setUrl($file);
            }

            $this->em->flush();
            $this->addFlash('success', 'Succes !');
            return $this->redirectToRoute('profile.image.index');
        }

        return $this->render('profile/image/edit.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/user/edit/{id}", name="profile.user.edit")
     */
    public function editUser(User $user, Request $request)
    {
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Succes !');
            return $this->redirectToRoute('profile.image.index');
        }

        return $this->render('profile/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/user/delete/{id}", name="profile.user.delete", methods="DELETE")
     */
    public function deleteUser(User $user, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->get('_token')))
        {
            $this->em->remove($user);
            $this->em->flush();
            $this->addFlash('success', 'Succes !');
        }
        return $this->redirectToRoute('profile.image.index');
    }


}

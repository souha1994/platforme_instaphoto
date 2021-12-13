<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Image;
use App\Form\CommentaireType;
use App\Form\Image1Type;
use App\Repository\ImageRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Time;

#[Route('/image')]
class ImageController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }
    #[Route('/', name: 'image_index', methods: ['GET'])]
    public function index(ImageRepository $imageRepository): Response
    {
        
        return $this->render('image/index.html.twig', [
            'images' => $imageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'image_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $image = new Image();
        $form = $this->createForm(Image1Type::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image->setCreateDate(new DateTimeImmutable());
            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('image_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('image/new.html.twig', [
            'image' => $image,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'image_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Image $image,  EntityManagerInterface $entityManager, ImageRepository $imageRepository): Response
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $commentaire->setImage($image);
            $user = $this->security->getUser();
            $commentaire->setUser($user);
            $commentaire->setPublierAt(new DateTimeImmutable());
            // dd($commentaire);
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('image_show', ['id' => $image->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('image/show.html.twig', [
            'image' => $image,
            'form' => $form,
            'images' => $imageRepository->findBy(array('creator' => $image->getCreator()->id)),
        ]);
    }

    #[Route('/{id}/edit', name: 'image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Image $image, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Image1Type::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('image_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('image/edit.html.twig', [
            'image' => $image,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'image_delete', methods: ['POST'])]
    public function delete(Request $request, Image $image, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('image_index', [], Response::HTTP_SEE_OTHER);
    }
}

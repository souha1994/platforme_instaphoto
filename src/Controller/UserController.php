<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ContactUsType;
use App\Form\UserType;
use App\Form\UserType1;
use App\Repository\ImageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;


#[Route('/users')]
class UserController extends AbstractController
{
    private $security;
    public function __construct(Security $security)
    {
       $this->security = $security;
    }
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if ($this->isGranted('ROLE_ADMIN') === false){
            $user = new User();
            $form = $this->createForm(UserType1::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $req = $request->request->get('user_type1');
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $req['password']
                );
                $user->setEmail($req['email']);
                $user->setRoles(['ROLE_USER']);
                $user->setPassword($hashedPassword);
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
            }
        }else{
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $requestss = $request->request->get('user');
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $requestss['password']
                );
                $user->setPassword($hashedPassword);
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'image_show', methods: ['GET', 'POST'])]
    // public function show(Request $request, Image $image,  EntityManagerInterface $entityManager): Response
    // {
    //     $commentaire = new Commentaire();
    //     $form = $this->createForm(CommentaireType::class, $commentaire);
        
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
            
    //         $commentaire->setImage($image);
    //         $user = $this->security->getUser();
    //         $commentaire->setUser($user);
    //         $commentaire->setPublierAt(new DateTimeImmutable());
    //         // dd($commentaire);
    //         $entityManager->persist($commentaire);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('image_show', ['id' => $image->getId()], Response::HTTP_SEE_OTHER);
    //     }
    //     return $this->renderForm('image/show.html.twig', [
    //         'image' => $image,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'user_show', methods: ['GET', 'POST'])]
    public function show(Request $request, User $user, MailerInterface $mailer, ImageRepository $imageRepository): Response
    {
        $userr = $this->security->getUser();
        // dd($userr);
        $is_user_auth = $userr->id == $user->getId() ? true : false;
        $form = $this->createForm(ContactUsType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $requestss = $request->request->get('contact_us');
            // dd($requestss);
            $html = '<h1>email from '.$userr->email.'</h1>';
            $html.= '<p>'.$requestss['body'].'</p>';
            $email = (new Email())
                ->from('h.salhi@lakeview-studio.com')
                ->to($user->email)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                ->replyTo($userr->email)
                //->priority(Email::PRIORITY_HIGH)
                ->subject($requestss['subject'])
                ->html($html);

            $mailer->send($email);
        }
        // dd($user);
        return $this->renderForm('user/show.html.twig', [
            'user' => $user,
            'form' => $form,
            'images' => $imageRepository->findBy(array('creator' => $user->getId())),
            'is_user_auth' => $is_user_auth,
        ]);
    }

    #[Route('/admin/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        
        $userr = $this->security->getUser();
        // dd($userr);
        // $is_user_auth = $userr->id == $user->getId() ? true : false;
        $form = $this->createForm(ContactUsType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $requestss = $request->request->get('contact_us');
            // dd($requestss);
            $html = '<h1>email from '.$userr->email.'</h1>';
            $html.= '<p>'.$requestss['body'].'</p>';
            $email = (new Email())
                ->from('h.salhi@lakeview-studio.com')
                ->to('h.salhi@lakeview-studio.com')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                ->replyTo($userr->email)
                //->priority(Email::PRIORITY_HIGH)
                ->subject($requestss['subject'])
                ->html($html);

            $mailer->send($email);
        }
        // dd($user);
        return $this->renderForm('user/contact_admin.html.twig', [
            // 'user' => $user,
            'form' => $form,
            // 'images' => $imageRepository->findBy(array('creator' => $user->getId())),
            // 'is_user_auth' => $is_user_auth,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, ImageRepository $imageRepository): Response
    {
        $userr = $this->security->getUser();
        // dd($userr);
        if (in_array('ROLE_ADMIN', $userr->getRoles())){
            $ff = UserType::class;
        }else{
            $ff = UserType1::class;
        }
        $form = $this->createForm($ff, $user);
        $form->handleRequest($request);
        $id_user = $request->attributes->get('id');
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'images' => $imageRepository->findBy(array('creator' => $id_user)),
        ]);
    }

    #[Route('/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}

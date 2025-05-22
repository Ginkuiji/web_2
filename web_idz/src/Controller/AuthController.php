<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'login_form', methods: ['GET'])]
    public function loginForm(): Response
    {
        return $this->render('login.twig');
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(
        Request $request,
        UserRepository $userRepository,
        SessionInterface $session,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $username = $request->request->get('user_login');
        $password = $request->request->get('user_password');

        $user = $userRepository->findByLogin($username);

        if ($user && $passwordHasher->isPasswordValid($user, $password)) {
            $session->set('user_id', $user->getId());
            $session->set('user_login', $user->getLogin());
            $session->set('user_role', $user->getRoles());

            $roles = $user->getRoles();

            if (in_array('admin', $roles, true)) {
                return $this->redirectToRoute('admin_menu');
            }

            if (in_array('user', $roles, true)) {
                return $this->redirectToRoute('user_reports');
            }

            return $this->redirectToRoute('login_form');

        }

        return new Response('Неверный логин или пароль.', 401);
    }

    #[Route('/register', name: 'register_form', methods: ['GET'])]
    public function registerForm(): Response
    {
        return $this->render('register.twig');
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $username = $request->request->get('user_login');
            $password = $request->request->get('user_password');
            $confirm = $request->request->get('confirm_password');

            if ($password !== $confirm) {
                return new Response('Пароли не совпадают.');
            }

            $user = new User();
            $user->setLogin($username);

            $hashedPassword = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);

            $user->setRoles(['user']);

            $em->persist($user);
            $em->flush();

            return new Response('Пользователь зарегистрирован! <a href="/login">Войти</a>');
        }

        return $this->render('register.twig');
    }

    #[Route('/logout', name: 'logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->clear();
        return $this->redirectToRoute('login_form');
    }
}

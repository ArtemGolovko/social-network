<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/profile", name="app_current_user_profile", host="%domain%")
     */
    public function currentUserProfile(): Response
    {
        return $this->render('account/current_user_profile.html.twig');
    }

    /**
     * @Route("/profile", name="app_mobile_current_user_profile", host="m.%domain%")
     */
    public function mobileCurrentUserProfile(): Response
    {
        return $this->render('mobile/not_implemented.html.twig');
    }

    /**
     * @Route("/profile/{username}", name="app_user_profile", host="%domain%")
     */
    public function userProfile(User $user): Response
    {
        if ($user == $this->getUser()) {
            return $this->redirectToRoute('app_current_user_profile');
        }
        return $this->render('account/user_profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profile/{username}", name="app_mobile_user_profile", host="m.%domain%")
     */
    public function MobileUserProfile(User $user): Response
    {
        if ($user == $this->getUser()) {
            return $this->redirectToRoute('app_current_user_profile');
        }
        return $this->render('mobile/not_implemented.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/{id}/posts", name="app_user_post", methods={"POST"})
     */
    public function userPosts(User $user, Request $request, PostRepository $postRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        $renderedPosts = [];

        $posts = $postRepository->findByUserWithPagination($user, $data['startIndex'], $data['maxResult']);

        foreach ($posts as $post) {
            $renderedPosts[] = $this->renderView('partial/render_post.html.twig', [
                'post' => $post
            ]);
        }

        return $this->json($renderedPosts);
    }

    /**
     * @Route("/user/{id}/subscribe", name="app_user_subscribe", methods={"POST"})
     */
    public function subscribe(User $user): Response
    {
        if ($user == $this->getUser()) {
            return $this->json([
                'message' => 'You can\'t subscribe to yourself'
            ], 409);
        }
        $user->addSubscriber($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->json([]);
    }

    /**
     * @Route("/user/{id}/unsubscribe", name="app_user_unsubscribe", methods={"POST"})
     */
    public function unsubscribe(User $user): Response
    {
        if ($user == $this->getUser()) {
            return $this->json([
                'message' => 'You can\'t unsubscribe from yourself'
            ], 409);
        }
        $user->removeSubscriber($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->json([]);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/homepage.html.twig', [
            'posts' => $postRepository->findLatestPublished()
        ]);
    }

    /**
     * @Route("/{username}/{index}", name="app_post_show")
     */
    public function show(User $user, $index): Response
    {
        return $this->render('post/show.html.twig', [
            'post' =>  $user->getPosts()->get($index)
        ]);
    }
}

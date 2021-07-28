<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    /**
     * @Route("/posts/{id}/url", name="app_post_url", methods={"POST"})
     */
    public function url(Post $post, UrlGeneratorInterface $urlGenerator): Response
    {
        $index = $post->getAuthor()->getPosts()->indexOf($post);
        return $this->json([
            'url' => $urlGenerator->generate('app_post_show', [
                'username' => $post->getAuthor()->getUsername(),
                'index' => $index
            ])
        ]);
    }
}

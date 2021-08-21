<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostLikeController extends AbstractController
{
    /**
     * @Route("/posts/{id}/like", name="app_post_like", methods={"POST"})
     */
    public function like(Post $post): Response
    {
        $post->addLike($this->getUser());

        $this->getDoctrine()->getManager()->flush();
        return $this->json([
            'likesCount' => $post->getLikes()->count()
        ]);
    }

    /**
     * @Route("/posts/{id}/dislike", name="app_post_dislike", methods={"POST"})
     */
    public function dislike(Post $post): Response
    {
        $post->removeLike($this->getUser());

        $this->getDoctrine()->getManager()->flush();
        return $this->json([
            'likesCount' => $post->getLikes()->count()
        ]);
    }
}

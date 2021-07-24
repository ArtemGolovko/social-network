<?php

namespace App\Controller;

use App\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PostLikeController extends AbstractController
{
    private UrlGeneratorInterface $urlGenerator;

    /**
     * PostLikeController constructor.
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }


    /**
     * @Route("/posts/{id}/like", name="app_post_like", methods={"POST"})
     */
    public function like(Post $post): Response
    {
        if (!$this->isGranted("IS_AUTHENTICATED_REMEMBERED")) {
            return $this->json([
                'redirectUrl' => $this->urlGenerator->generate('app_login')
            ], 403);
        }

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
        if (!$this->isGranted("IS_AUTHENTICATED_REMEMBERED")) {
            return $this->json([
                'redirectUrl' => $this->urlGenerator->generate('app_login')
            ], 403);
        }

        $post->removeLike($this->getUser());

        $this->getDoctrine()->getManager()->flush();
        return $this->json([
            'likesCount' => $post->getLikes()->count()
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

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
     * @Route("/{username}/{index<\d+>}", name="app_post_show")
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

    /**
     * @Route("/posts/create", name="app_post_create", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function create(Request $request, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $data = json_decode($request->getContent(), true);

        $token = new CsrfToken('authenticate', $data['_csrf_token']);

        if (!$csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $post = (new Post())
            ->setAuthor($this->getUser())
            ->setBody($data['postBody'])
        ;

        $em = $this->getDoctrine()->getManager();

        $em->persist($post);
        $em->flush();

        return $this->json([
            'html' => $this->renderView('partial/render_post.html.twig', ['post' => $post])
        ]);
    }



}

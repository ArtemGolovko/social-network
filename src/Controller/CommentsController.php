<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CommentsController extends AbstractController
{
    private CommentRepository $commentRepository;
    private CsrfTokenManagerInterface $csrfTokenManager;

    /**
     * CommentsController constructor.
     */
    public function __construct(CommentRepository $commentRepository, CsrfTokenManagerInterface $csrfTokenManager) {
        $this->commentRepository = $commentRepository;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @Route("/posts/{id}/comments", name="app_post_comments", methods={"POST"})
     */
    public function comments(Request $request, Post $post): Response
    {
        $data = json_decode($request->getContent(), true);

        $comments = $this->commentRepository->findLatestByPostWithPagination($post, $data['maxResult'], $data['startIndex']);

        $isMoreAvailable = $this->commentRepository->isMoreCommentsAvailable($post, $data['startIndex'] + $data['maxResult']);

        $html = '';

        foreach ($comments as $comment) {
            $html .= $this->renderView('partial/render_comment.html.twig', [
                'comment' => $comment,
                'hasReplays' => !$comment->getReplays()->isEmpty()
            ]);
        }

        if ($isMoreAvailable) {
            $html .= $this->renderView('partial/view_more_comments_button.html.twig', [
                'totalLoaded' => $data['startIndex'] + $data['maxResult'],
                'commentsUrl' => $request->getRequestUri()
            ]);
        }

        return new Response($html);
    }

    /**
     * @Route("/comments/{id}/replays", name="app_comment_replays")
     */
    public function replays(Comment $comment, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $replays = $this->commentRepository->findLatestReplaysWithPagination($comment, $data['maxResult'], $data['startIndex']);

        $isMoreAvailable = $this->commentRepository->isMoreReplaysAvailable($comment, $data['startIndex'] + $data['maxResult']);

        $html = '';

        foreach ($replays as $replay) {
            $html .= $this->renderView('partial/render_replay.html.twig', [
                'replay' => $replay,
                'hasReplays' => !$replay->getReplays()->isEmpty()
            ]);
        }

        if ($isMoreAvailable) {
            $html .= $this->renderView('partial/view_more_replays_button.html.twig', [
                'totalLoaded' => $data['startIndex'] + $data['maxResult'],
                'replaysUrl' => $request->getRequestUri()
            ]);
        }
        return new Response($html);
    }

    /**
     * @Route("/posts/{id}/comments/create", name="app_post_comments_create", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function createComment(Post $post, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $token = new CsrfToken('authenticate', $data['_csrf_token']);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $comment = (new Comment())
            ->setAuthor($this->getUser())
            ->setBody($data['commentBody'])
            ->setPost($post)
        ;

        $em = $this->getDoctrine()->getManager();

        $em->persist($comment);
        $em->flush();

        return $this->json([
            'html' => $this->renderView('partial/render_comment.html.twig', [
                'comment' => $comment,
                'hasReplays' => false
            ])
        ]);
    }

    /**
     * @Route("/comments/{id}/replays/create", name="app_comments_replays_create", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function createReplay(Comment $comment, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $token = new CsrfToken('authenticate', $data['_csrf_token']);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $replay = (new Comment())
            ->setAuthor($this->getUser())
            ->setBody($data['commentBody'])
            ->setPost($comment->getPost())
            ->setReplayTo($comment)
        ;

        $em = $this->getDoctrine()->getManager();

        $em->persist($replay);
        $em->flush();

        return $this->json([
            'html' => $this->renderView('partial/render_replay.html.twig', [
                'replay' => $replay,
                'hasReplays' => false
            ])
        ]);
    }

    /**
     * @Route("/posts/{id}/make-comments-block", name="app_post_make_comment_block", methods={"POST"})
     */
    public function loadMakeCommentBlock($id, UrlGeneratorInterface $urlGenerator): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return new Response('');
        }
        return $this->render('partial/make_comment_block.html.twig', [
            'createCommentUrl' => $urlGenerator->generate('app_post_comments_create', ['id' => $id])
        ]);
    }

    /**
     * @Route("/commnts/{id}/make-replays-block", name="app_comment_make_replay_block", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function loadMakeReplayBlock($id, UrlGeneratorInterface $urlGenerator): Response
    {
        return $this->render('partial/make_replay_block.html.twig', [
            'createReplayUrl' => $urlGenerator->generate('app_comments_replays_create', ['id' => $id])
        ]);
    }
}

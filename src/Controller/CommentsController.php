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

class CommentsController extends AbstractController
{
    private CommentRepository $commentRepository;

    /**
     * CommentsController constructor.
     */
    public function __construct(CommentRepository $commentRepository) {
        $this->commentRepository = $commentRepository;
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
                'hasAnswers' => !$comment->getAnswers()->isEmpty()
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
     * @Route("/comments/{id}/answers", name="app_comment_answers")
     */
    public function answers(Comment $comment, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $answers = $this->commentRepository->findLatestAnswersWithPagination($comment, $data['maxResult'], $data['startIndex']);

        $isMoreAvailable = $this->commentRepository->isMoreAnswersAvailable($comment, $data['startIndex'] + $data['maxResult']);

        $html = '';

        foreach ($answers as $answer) {
            $html .= $this->renderView('partial/render_answer.html.twig', [
                'answer' => $answer,
                'hasAnswers' => !$answer->getAnswers()->isEmpty()
            ]);
        }

        if ($isMoreAvailable) {
            $html .= $this->renderView('partial/view_more_answers_button.html.twig', [
                'totalLoaded' => $data['startIndex'] + $data['maxResult'],
                'answersUrl' => $request->getRequestUri()
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
                'hasAnswers' => false
            ])
        ]);
    }

    /**
     * @Route("/comments/{id}/answers/create", name="app_comments_answers_create", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function createAnswer(Comment $comment, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $answer = (new Comment())
            ->setAuthor($this->getUser())
            ->setBody($data['commentBody'])
            ->setPost($comment->getPost())
            ->setAnswerTo($comment)
        ;

        $em = $this->getDoctrine()->getManager();

        $em->persist($answer);
        $em->flush();

        return $this->json([
            'html' => $this->renderView('partial/render_answer.html.twig', [
                'answer' => $answer,
                'hasAnswers' => false
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
     * @Route("/commnts/{id}/make-answers-block", name="app_comment_make_answer_block", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function loadMakeAnswerBlock($id, UrlGeneratorInterface $urlGenerator): Response
    {
        return $this->render('partial/make_answer_block.html.twig', [
            'createAnswerUrl' => $urlGenerator->generate('app_comments_answers_create', ['id' => $id])
        ]);
    }
}

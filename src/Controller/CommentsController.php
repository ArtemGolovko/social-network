<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}

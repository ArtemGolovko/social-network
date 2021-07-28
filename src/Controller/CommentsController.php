<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CommentsController extends AbstractController
{
    private CommentRepository $commentRepository;
    private Packages $packages;
    private UrlGeneratorInterface $urlGenerator;

    /**
     * CommentsController constructor.
     */
    public function __construct(CommentRepository $commentRepository, Packages $packages, UrlGeneratorInterface $urlGenerator)
    {
        $this->commentRepository = $commentRepository;
        $this->packages = $packages;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/posts/{id}/comments", name="app_post_comments", methods={"POST"})
     */
    public function comments(Request $request, Post $post): Response
    {
        $data = json_decode($request->getContent(), true);

        $comments = $this->commentRepository->findLatestByPostWithPagination($post, $data['maxResult'], $data['startIndex']);

        $isMoreAvailable = $this->commentRepository->isMoreCommentsAvailable($post, $data['startIndex'] + $data['maxResult']);

        $responseData = [
            'comments' => [],
            'isMoreAvailable' => $isMoreAvailable
        ];

        foreach ($comments as $comment) {
            $responseData['comments'][] = [
                'author' => [
                    'avatar' => $this->packages->getUrl($comment->getAuthor()->getAvatarUrl()),
                    'username' => $comment->getAuthor()->getUsername(),
                ],
                'createdAt' => Carbon::make($comment->getCreatedAt())->locale('ru')->diffForHumans(),
                'body' => $comment->getBody(),
                'hasAnswers' => !$comment->getAnswers()->isEmpty(),
                'answersUrl' => $this->urlGenerator->generate(
                    'app_comment_answers',
                    ['id' => $comment->getId()]
                )
            ];
        }
        return $this->json($responseData);
    }

    /**
     * @Route("/comments/{id}/answers", name="app_comment_answers")
     */
    public function answers(Comment $comment, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $answers = $this->commentRepository->findLatestAnswersWithPagination($comment, $data['maxResult'], $data['startIndex']);

        $isMoreAvailable = $this->commentRepository->isMoreAnswersAvailable($comment, $data['startIndex'] + $data['maxResult']);

        $responseData = [
            'answers' => [],
            'isMoreAvailable' => $isMoreAvailable
        ];

        foreach ($answers as $answer) {
            $responseData['answers'][] = [
                'author' => [
                    'avatar' => $this->packages->getUrl($answer->getAuthor()->getAvatarUrl()),
                    'username' => $answer->getAuthor()->getUsername(),
                ],
                'createdAt' => Carbon::make($answer->getCreatedAt())->locale('ru')->diffForHumans(),
                'body' => $answer->getBody(),
                'hasAnswers' => !$answer->getAnswers()->isEmpty(),
                'answersUrl' => $this->urlGenerator->generate(
                    'app_comment_answers',
                    ['id' => $answer->getId()]
                )
            ];
        }
        return $this->json($responseData);
    }
}

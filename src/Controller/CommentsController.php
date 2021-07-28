<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\CommentRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{
    private CommentRepository $commentRepository;
    private Packages $packages;

    /**
     * CommentsController constructor.
     */
    public function __construct(CommentRepository $commentRepository, Packages $packages)
    {
        $this->commentRepository = $commentRepository;
        $this->packages = $packages;
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
                'body' => $comment->getBody()
            ];
        }
        return $this->json($responseData);
    }
}

<?php

namespace App\Twig;

use App\Entity\Post;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PostUrlGeneratorExtension extends AbstractExtension
{
    private UrlGeneratorInterface $urlGenerator;


    /**
     * PostUrlGeneratorExtension constructor.
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('post_url', [$this, 'generatePostUrl']),
        ];
    }

    public function generatePostUrl(Post $post): string
    {
        $index = $post->getAuthor()->getPosts()->indexOf($post);
        return $this->urlGenerator->generate('app_post_show', [
            'username' => $post->getAuthor()->getUsername(),
            'index' => $index
        ]);
    }
}

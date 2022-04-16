<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TextProcessorExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('process', [$this, 'processText'], ['is_safe' => ['html']]),
        ];
    }

    public function processText($text)
    {
        $text = htmlspecialchars($text);
        $linkPattern = "@(http(s)?://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@";
        $text = preg_replace(
            $linkPattern,
            '<a href="http$2://$3" target="_blank" rel="noopener noreferrer">$0</a>',
            $text
        );

        return $text;
    }
}

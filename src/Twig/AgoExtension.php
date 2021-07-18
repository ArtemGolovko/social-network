<?php

namespace App\Twig;

use Carbon\Carbon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AgoExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'getDiff']),
        ];
    }

    public function getDiff($date): string
    {
        return Carbon::make($date)->locale('ru')->diffForHumans();
    }
}

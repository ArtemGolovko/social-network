<?php

namespace App\Twig;

use App\Service\DateTimeDifferInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AgoExtension extends AbstractExtension
{
    private DateTimeDifferInterface $dateTimeDiffer;

    public function __construct(DateTimeDifferInterface $dateTimeDiffer)
    {
        $this->dateTimeDiffer = $dateTimeDiffer;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this->dateTimeDiffer, 'getDiff']),
        ];
    }
}

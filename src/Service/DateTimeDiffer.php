<?php

namespace App\Service;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\RequestStack;

class DateTimeDiffer implements DateTimeDifferInterface
{
    private string $locale;

    public function __construct(RequestStack $requestStack)
    {
        $this->locale = $requestStack->getCurrentRequest()->getLocale();
    }

    public function getDiff($dateTime): string
    {
        return Carbon::make($dateTime)->locale($this->locale)->diffForHumans();
    }
}
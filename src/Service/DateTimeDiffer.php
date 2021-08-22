<?php

namespace App\Service;

use Carbon\Carbon;

class DateTimeDiffer implements DateTimeDifferInterface
{
    private string $locale;

    public function __construct(GetLocaleInterface $getLocale)
    {
        $this->locale = $getLocale->getLocale();
    }

    public function getDiff($dateTime): string
    {
        return Carbon::make($dateTime)->locale($this->locale)->diffForHumans();
    }
}

<?php

namespace App\Service;

interface DateTimeDifferInterface
{
    /**
     * @param mixed $dateTime
     * @return string
     */
    public function getDiff($dateTime): string;
}
<?php

namespace App\Twig;

use App\Service\GetLocaleInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class LocaleExtension extends AbstractExtension implements GlobalsInterface
{

    private string $locale;

    public function __construct(GetLocaleInterface $getLocale)
    {

        $this->locale = $getLocale->getLocale();
    }

    public function getGlobals(): array
    {
        return [
            'locale' => $this->locale
        ];
    }
}

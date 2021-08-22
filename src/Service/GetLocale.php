<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class GetLocale implements GetLocaleInterface
{
    private string $locale;

    public function __construct(RequestStack $requestStack, ContainerInterface $container)
    {
        $this->locale = $requestStack->getCurrentRequest() ? $requestStack->getCurrentRequest()->getLocale() : 'ru';
        $locales = (array)$container->getParameter('locales');
        $this->locale = (in_array($this->locale, $locales)) ? $this->locale : 'ru';
    }

    public function getLocale(): string
    {
        return $this->locale;
    }
}
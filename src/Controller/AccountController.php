<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/profile", name="app_current_user_profile", host="%domain%")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function currentUserProfile(): Response
    {
        return $this->render('account/current_user_profile.html.twig');
    }

    /**
     * @Route("/profile", name="app_mobile_current_user_profile", host="m.%domain%")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function mobileCurrentUserProfile(): Response
    {
        return $this->render('mobile/not_implemented.html.twig');
    }

    /**
     * @Route("/profile/{username}", name="app_user_profile", host="%domain%")
     */
    public function userProfile(User $user): Response
    {
        return $this->render('account/user_profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profile/{username}", name="app_mobile_user_profile", host="m.%domain%")
     */
    public function MobileUserProfile(User $user): Response
    {
        return $this->render('mobile/not_implemented.html.twig', [
            'user' => $user,
        ]);
    }
}

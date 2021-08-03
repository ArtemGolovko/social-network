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
     * @Route("/profile", name="app_current_user_profile")
     * @IsGranted("IS_AUTHENTICATED_REMEMBERED")
     */
    public function currentUserProfile(): Response
    {
        return $this->render('account/current_user_profile.html.twig');
    }

    /**
     * @Route("/profile/{username}", name="app_user_profile")
     */
    public function userProfile(User $user): Response
    {
        return $this->render('account/user_profile.html.twig', [
            'user' => $user,

        ]);
    }
}

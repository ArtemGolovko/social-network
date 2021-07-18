<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixtures
{
    private UserPasswordEncoderInterface $passwordEncoder;


    /**
     * UserFixtures constructor.
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function loadData(ObjectManager $manager)
    {
        $this->createMany(User::class, 10, function (User $user) {
             $user
                 ->setUsername($this->faker->userName)
                 ->setEmail($this->faker->email)
                 ->setName($this->faker->name)
                 ->setPassword($this->passwordEncoder->encodePassword($user, 'qwerty'))
             ;
        });
    }
}

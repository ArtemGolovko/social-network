<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends BaseFixtures implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Post::class, 100, function (Post $post) {
            $post
                 ->setBody($this->faker->paragraphs(3, true))
                 ->setAuthor($this->getRandomReference(User::class))
                 ->setCreatedAt($this->faker->dateTimeBetween('-500 day', '-1 day'))
             ;

            for ($i = 0; $i < $this->faker->numberBetween(0, 5); ++$i) {
                $post->addLike($this->getRandomReference(User::class));
            }
        });
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}

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
                 ->setLikes($this->faker->numberBetween(-10, 10))
                 ->setAuthor($this->getRandomReference(User::class))
             ;

             if ($this->faker->boolean(70)) {
                 $post->setPublishedAt($this->faker->dateTimeBetween('-60 days', '-1 day'));
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

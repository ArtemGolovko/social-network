<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CommentFixtures extends BaseFixtures implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Comment::class, 100, function (Comment $comment) {
            $comment
                ->setCreatedAt($this->faker->dateTimeBetween('-60 days', '-30 days'))
                ->setAuthor($this->getRandomReference(User::class))
                ->setPost($this->getRandomReference(Post::class))
                ->setBody($this->faker->paragraph)
            ;
        });

        $this->createMany(Comment::class, 100, function (Comment $comment) {
            $comment
                ->setCreatedAt($this->faker->dateTimeBetween('-30 days', '-1 day'))
                ->setAuthor($this->getRandomReference(User::class))
                ->setAnswerTo($this->getRandomReference(Comment::class))
                ->setBody($this->faker->paragraph)
            ;
        }, false);
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            PostFixtures::class
        ];
    }
}

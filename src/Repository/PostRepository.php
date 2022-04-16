<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findLatestPublishedWithPagination(int $firstResult, int $maxResults)
    {
        $query = $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'a')
            ->addSelect('a')
            ->leftJoin('p.likes', 'l')
            ->addSelect('l')
            ->leftJoin('p.comments', 'c')
            ->addSelect('c')
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
        ;

        return new Paginator($query);
    }

    public function findByUserWithPagination(User $user, int $firstResult, int $maxResults)
    {
        $query = $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'a')
            ->addSelect('a')
            ->leftJoin('p.likes', 'l')
            ->addSelect('l')
            ->leftJoin('p.comments', 'c')
            ->addSelect('c')
            ->orderBy('p.createdAt', 'DESC')
            ->andWhere('p.author = :user')
            ->setParameter('user', $user)
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
        ;

        return new Paginator($query);
    }

    public function findByUserSubscribedWithPagination(int $firstResult, int $maxResults, User $user)
    {
        $query = $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'a')
            ->addSelect('a')
            ->leftJoin('p.likes', 'l')
            ->addSelect('l')
            ->leftJoin('p.comments', 'c')
            ->addSelect('c')
            ->andWhere(':user MEMBER OF a.subscribers')
            ->setParameter('user', $user)
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
        ;

        return new Paginator($query);
    }
}

<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

     /**
      * @return Comment[] Returns an array of Comment objects
      */
    public function findLatestByPostWithPagination(Post $post, int $maxResult, int $startIndex = 0)
    {
        $query = $this->createQueryBuilder('c')
            ->leftJoin('c.answers', 'a')
            ->addSelect('a')
            ->innerJoin('c.author', 'u')
            ->addSelect('u')
            ->andWhere('c.post = :post')
            ->setParameter('post', $post)
            ->andWhere('c.answerTo IS NULL')
            ->orderBy('c.createdAt', 'DESC')
            ->setFirstResult($startIndex)
            ->setMaxResults($maxResult)
        ;

        return new Paginator($query);
    }

    public function isMoreCommentsAvailable(Post $post, int $totalLoaded): bool
    {
        return $this->createQueryBuilder('c')
            ->select('count(c)')
            ->andWhere('c.post = :post')
            ->setParameter('post', $post)
            ->andWhere('c.answerTo IS NULL')
            ->getQuery()
            ->getSingleScalarResult() > $totalLoaded;
    }

    /**
     * @return Comment[] Returns an array of Comment objects
     */
    public function findLatestAnswersWithPagination(Comment $comment, int $maxResult, int $startIndex = 0)
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.answerTo = :comment')
            ->setParameter('comment', $comment)
            ->innerJoin('c.author', 'u')
            ->addSelect('u')
            ->leftJoin('c.answers', 'a')
            ->addSelect('a')
            ->orderBy('c.createdAt', 'DESC')
            ->setFirstResult($startIndex)
            ->setMaxResults($maxResult)
        ;

        return new Paginator($query);
    }

    public function isMoreAnswersAvailable(Comment $comment, int $totalLoaded): bool
    {
        return $this->createQueryBuilder('c')
                ->select('count(c)')
                ->andWhere('c.answerTo = :comment')
                ->setParameter('comment', $comment)
                ->getQuery()
                ->getSingleScalarResult() > $totalLoaded;
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

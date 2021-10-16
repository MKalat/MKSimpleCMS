<?php
namespace App\Repository;


use App\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;
use Doctrine\Common\Persistence\ManagerRegistry;

class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }
    
    public function findAllBlogPostsByLang($lang,  $order, $currentPage = 1, $limit = 5)
    {
        $pagecount = $currentPage - 1;
        $offset = $limit * $pagecount;
        
         $query = $this->createQueryBuilder('bp')
            ->where('bp.lang = :lang')
            ->setParameter('lang', $lang)
            ->orderBy('bp.timestamp', $order)
            ->setFirstResult($offset) // Offset
            ->setMaxResults($limit) // Limit
            ->getQuery();
         
            
         $paginator = new Paginator($query);
         return $paginator;
    }
    
    
}


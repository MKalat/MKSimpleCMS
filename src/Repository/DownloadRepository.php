<?php
namespace App\Repository;

use App\Entity\Download;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;
use Doctrine\Common\Persistence\ManagerRegistry;

class DownloadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Download::class);
    }
    
    public function findAllDownloadsByLang($lang, $order, $currentPage = 1, $limit = 5)
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

    public function findAllDownloadsBySearch($lang,$order,$search_term)
    {
        $query = $this->createQueryBuilder('bp')
            ->where('bp.lang = :lang')
            ->AndWhere('bp.dl_name LIKE :search_term')
            ->setParameter('lang', $lang)
            ->setParameter('search_term', '%'.$search_term.'%')
            ->orderBy('bp.timestamp', $order)
            ->getQuery();
        $result = $query->execute();
        return $result;
    }
    
    
    
    
    
}


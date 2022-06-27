<?php


namespace App\Controller;

use App\Entity\Links;
use App\Entity\Download;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\DownloadRepository;

class DownloadController extends AbstractController
{
    private $repo;
    
    public function __construct(DownloadRepository $repo)
    {
        $this->repo = $repo;
    }
    
    /**
     * @Route("/dls/{_locale}/page_no/{current_page}", name="downloads", defaults={"_locale" = "en", "current_page" = "1"}, requirements={"_locale" = "en|pl"})
     */
    public function index(Request $request, EntityManagerInterface $em, $current_page)
    {
        $_locale = $request->getLocale();

        $current_page = filter_var($current_page, FILTER_SANITIZE_STRING);
        
        if ($current_page == "") {
            $current_page = 1;
        } else {
            $current_page = intval($current_page);
        }
        
        $posts = $this->repo->findAllDownloadsByLang($_locale, "DESC", $current_page);

        $entityManager = $em;
        $query = $entityManager->createQuery(
            "SELECT l
            FROM App:Links l
            WHERE l.lang = :lang AND l.link != '0'
            ORDER BY l.pozycja ASC"
        )->setParameter('lang', $_locale);

        $links = $query->getResult();
        
        if ($current_page ==1) {
            $previous_page = 1;
        } else {
            $previous_page = $current_page - 1;
        }
        $num_pages = round(count($posts) / 5);
        if ($num_pages == 0) {
            $num_pages = 1;
        }
        if (($current_page + 1) >= $num_pages) {
            $next_page = $num_pages;
        } else {
            $next_page = $current_page + 1;
        }
        $last_page = $num_pages;

        return $this->render('default/download.html.twig', array(
            'links' => $links,
            'lang' => $_locale,
            'posts' => $posts,
            'next_page' => $next_page,
            'previous_page' => $previous_page,
            'last_page' => $last_page,
            'currentPage' => $current_page,

        ));
    }
    
    /**
     * @Route("/dls/{_locale}/download/{dlItem}", name="downloadItem", defaults={"_locale" = "en", "dlItem" = ""}, requirements={"_locale" = "en|pl"})
     */
    public function donwloadItem(Request $request, EntityManagerInterface $em, $dlItem)
    {
        $_locale = $request->getLocale();
        
        $dlItem = filter_var($dlItem, FILTER_SANITIZE_STRING);
        
        if ($dlItem == "") {
            $dlItem = 1;
        } else {
            $dlItem = intval($dlItem);
        }
        
        $download = $this->repo->find($dlItem);
        //var_dump($download);
        
        $entityManager = $em;
        $query = $entityManager->createQuery(
            "SELECT l
            FROM App:Links l
            WHERE l.lang = :lang AND l.link != '0'
            ORDER BY l.pozycja ASC"
        )->setParameter('lang', $_locale);
            
        $links = $query->getResult();
           
        return $this->render('default/download-item.html.twig', array(
                'links' => $links,
                'lang' => $_locale,
                'posts' => $download,
                
            ));
    }

    /**
     * @Route("/dls/{_locale}/search", name="downloads-search", defaults={"_locale" = "en"}, requirements={"_locale" = "en|pl"})
     */
    public function search(Request $request, EntityManagerInterface $em)
    {
        $_locale = $request->getLocale();
        $search_term = $request->get('search_term');



        $search_term = filter_var($search_term, FILTER_SANITIZE_STRING);



        $posts = $this->repo->findAllDownloadsBySearch($_locale, "DESC", $search_term);

        $entityManager = $em;
        $query = $entityManager->createQuery(
            "SELECT l
            FROM App:Links l
            WHERE l.lang = :lang AND l.link != '0'
            ORDER BY l.pozycja ASC"
        )->setParameter('lang', $_locale);

        $links = $query->getResult();



        return $this->render('default/download.html.twig', array(
            'links' => $links,
            'lang' => $_locale,
            'posts' => $posts,
            'searchTerm' => $search_term
        ));
    }
}

<?php

namespace App\Controller;

use App\Entity\Links;
use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\BlogPostRepository;

class BlogController extends AbstractController
{
    private $repo;
    
    public function __construct(BlogPostRepository $repo)
    {
        $this->repo = $repo;
    }
    
    /**
     * @Route("/posts/{_locale}/{current_page}", name="blogposts", defaults={"_locale" = "en", "current_page" = "1"}, requirements={"_locale" = "en|pl"})
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

        
        
        $posts = $this->repo->findAllBlogPostsByLang($_locale, "DESC", $current_page);

        

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

        return $this->render('default/blogposts.html.twig', array(
            'links' => $links,
            'lang' => $_locale,
            'posts' => $posts,
            'next_page' => $next_page,
            'previous_page' => $previous_page,
            'last_page' => $last_page,
            'currentPage' => $current_page
        ));
    }
}

<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Links;
use App\Entity\Pages;
use App\Entity\LoginLogs;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index_redir")
     */
    public function indexRedirAction(Request $request)
    {
        return $this->redirectToRoute("index");
    }
    
    /**
     * @Route("/{_locale}/", name="index", defaults={"_locale" = "pl"}, requirements={"_locale" = "en|pl"})
     */
    public function indexAction(Request $request, EntityManagerInterface $em)
    {
        $_locale = $request->getLocale();
        if ($_locale == 'pl') {
            $linksRepo = $em->getRepository('App:Links');

            $links = $linksRepo->findBy(
                array(
                "lang" => 'pl'),
                array("pozycja" => "ASC")
            );

            $frontPageRepo = $em->getRepository('App:Pages');

            $frontPage = $frontPageRepo->findOneBy(array(
                "lang" => 'pl',
                "link" => '0',

            ));
        } else {
            $_locale = 'en';
            $linksRepo = $em->getRepository('App:Links');

            $links = $linksRepo->findBy(
                array(
                "lang" => 'en'),
                array(
                "pozycja" =>  "ASC")
            );

            $frontPageRepo = $em->getRepository('App:Pages');

            $frontPage = $frontPageRepo->findOneBy(array(
                "lang" => 'en',
                "link" => '0',
            ));
        }
        if ($frontPage) {
            $content = $frontPage->getContent();
        } else {
            $content = '';
        }

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'lang' => $_locale,
            'links' => $links,
            'content' => $content,
        ));
    }
    /**
     * @Route("/page/{_locale}/{link}/", name="viewpage", defaults={"_locale" = "en"}, requirements={"_locale" = "en|pl"})
     */
    public function viewpageAction(Request $request, EntityManagerInterface $em, $link)
    {
        $_locale = $request->getLocale();

        $pagesRepo = $em->getRepository('App:Pages');

        $frontPage = $pagesRepo->findOneBy(array(
            "link" => $link
        ));
        $lang = 'en';
        if ($_locale == 'pl') {
            $lang = 'pl';
        } else {
            $lang = 'en';
        }


        $entityManager = $em;
        $query = $entityManager->createQuery(
            "SELECT l
            FROM App:Links l
            WHERE l.lang = :lang AND l.link != '0'
            ORDER BY l.pozycja ASC"
        )->setParameter('lang', $lang);

        $links = $query->getResult();


        return $this->render('default/viewpage.html.twig', array(
            'lang' => $_locale,
            'links' => $links,
            'content' => $frontPage->getContent(),
        ));
    }
        /**
         * @Route("/wykonane-projekty-www/", name="projekty-www")
         */
    public function projektywwwAction()
    {
        return $this->render('default/projekty.html.twig', array(
            'lang' => 'pl'
        ));
    }
}

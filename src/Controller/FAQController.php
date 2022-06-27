<?php

namespace App\Controller;

use App\Entity\Links;
use App\Entity\FAQ;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class FAQController extends AbstractController
{
    /**
     * @Route("/faq/{_locale}/{title}", name="faq", defaults={"_locale" = "en", "title" = "0"}, requirements={"_locale" = "en|pl"})
     */
    public function index(Request $request, EntityManagerInterface $em, $title)
    {
        $_locale = $request->getLocale();

        $title = filter_var($title, FILTER_SANITIZE_STRING);

        $faqRepo = $em->getRepository('App:FAQ');

        $faq = '';

        if (!$title) {
            $faq = $faqRepo->findBy(
                array(
                "lang" => $_locale),
                array(
                    "id" => "ASC")
            );
        } else {
            $faq = $faqRepo->findOneBy(array(
                "lang" => $_locale,
                "title" => $title,
            ));
        }

        $query = $em->createQuery(
            "SELECT l
            FROM App:Links l
            WHERE l.lang = :lang AND l.link != '0'
            ORDER BY l.pozycja ASC"
        )->setParameter('lang', $_locale);

        $links = $query->getResult();

        return $this->render('default/faq.html.twig', array(
            'links' => $links,
            'lang' => $_locale,
            'faq' => $faq,
        ));
    }
}

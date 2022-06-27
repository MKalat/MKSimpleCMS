<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Entity\Download;
use App\Entity\Links;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\LoginLogs;
use App\Entity\Pages;
use App\Entity\FAQ;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\DataTablesSupport;
use Doctrine\DBAL\Connection;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin", name="admin-index")
     */
    public function indexAction(EntityManagerInterface $em)
    {

            $repo = $em->getRepository('App:LoginLogs');


            $logs_records = $repo->findBy(array(), array('czas'=>'DESC'), 0, 1);

        if ($logs_records) {
            $last_log_record = $logs_records[0];

            $name = $last_log_record['login'];
        } else {
            $name = '';
        }
            $user = $this->getUser();

            $loginLogs = new LoginLogs();
            $loginLogs->setIp($_SERVER['REMOTE_ADDR']);
            $loginLogs->setCzas(date('Y-m-d H:i:s'), time());
            $loginLogs->setLogin($user->getUsername());
            $loginLogs->setRanga(implode(",", $user->getRoles()));
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $loginLogs->setStatus($_SERVER['HTTP_X_FORWARDED_FOR']);
        } else {
            $loginLogs->setStatus('no proxy ?');
        }
            $entityManager = $em;
            $entityManager->persist($loginLogs);
            $entityManager->flush();

            return $this->render('admin/index.html.twig', array('lastadmin' => $name));
    }

    /**
     * @Route("/admin/loginlogs", name="admin-loginlogs")
     */
    public function loginlogsAction(Request $request)
    {


        return $this->render('admin/loginlogs.html.twig');
    }

   

    /**
     * @Route("/admin/loginlogsAjax", name="admin_adminlogsAjax")
     */
    public function loginlogsAjaxAction(Request $request, Connection $connection, DataTablesSupport $dtSupp)
    {

        $cols = array(
            array( "db" => "ID", "dt" => "0" ),
            array( "db" => "ip", "dt" => "1" ),
            array( "db" => "login", "dt" => "2" ),
            array( "db" => "czas", "dt" => "3" ),
            array( "db" => "status", "dt" => "4" ),
            array( "db" => "ranga", "dt" => "5" ),
        );

        $limitSql = $dtSupp->limitData($request->query->all());
        $orderSql = $dtSupp->orderData($request->query->all(), $cols);
        $filterSql = $dtSupp->filterData($request->query->all(), $cols);
        
        $logs_total = $connection->fetchAllAssociative("SELECT id, ip, login, czas, status, ranga FROM loginlogs ");

        $logs_raw = $connection->fetchAllAssociative("SELECT id, ip, login, czas, status, ranga FROM loginlogs ".$filterSql.' '.$orderSql.' '.$limitSql);

        $response = array();
        $response['draw'] = intval($request->query->get('draw'));
        $response["recordsTotal"] = count($logs_total);
        $response["recordsFiltered"] = count($logs_total);
        $response['data'] = array();

        foreach ($logs_raw as $item) {
            $response['data'][] = [$item['id'], $item['ip'], $item['login'], $item['czas'], $item['status'], $item['ranga']];
        }

        return new Response(json_encode($response));
    }

    /**
     * @Route("/admin/links", name="admin-links")
     */
    public function linksAction()
    {


            return $this->render('admin/links.html.twig');
    }


    
    /**
     * @Route("/admin/linksdelete", name="admin_links_delete")
     */
    public function linksDeleteAction(Request $request, EntityManagerInterface $em)
    {
        $data = $request->get('dataTables');
        $ids  = $data['actions'];

        $qb = $em->createQueryBuilder();
        $qb->select('l');
        $qb->from('AppBundle:Links', 'l');
        $qb->where($qb->expr()->in('l.id', $ids));

        //ArrayCollection
        $result = $qb->getQuery()->getResult();
        
        if ($result) {
            foreach ($result as $link) {
                $em->remove($link);
                $em->flush();
            }
        }
        return $this->redirectToRoute("admin-links");
    }

    /**
     * @Route("/admin/linksAjax", name="admin_linksAjax")
     */
    public function linksAjaxAction(Request $request, Connection $connection, DataTablesSupport $dtSupp)
    {

        $cols = array(
            array( "db" => "ID", "dt" => "0" ),
            array( "db" => "pozycja", "dt" => "1" ),
            array( "db" => "etykieta", "dt" => "2" ),
            array( "db" => "link", "dt" => "3" ),
            array( "db" => "strona", "dt" => "4" ),
            array( "db" => "lang", "dt" => "5" ),
        );

        $limitSql = $dtSupp->limitData($request->query->all());
        $orderSql = $dtSupp->orderData($request->query->all(), $cols);
        $filterSql = $dtSupp->filterData($request->query->all(), $cols);
        
        $logs_total = $connection->fetchAllAssociative("SELECT id, pozycja, etykieta, link, strona, lang FROM links ");

        $logs_raw = $connection->fetchAllAssociative("SELECT id, pozycja, etykieta, link, strona, lang FROM links ".$filterSql.' '.$orderSql.' '.$limitSql);

        $response = array();
        $response['draw'] = intval($request->query->get('draw'));
        $response["recordsTotal"] = count($logs_total);
        $response["recordsFiltered"] = count($logs_total);
        $response['data'] = array();

        foreach ($logs_raw as $item) {
            $response['data'][] = [$item['id'], $item['pozycja'], $item['etykieta'], $item['link'], $item['strona'], $item['lang']];
        }

        return new Response(json_encode($response));
    }

    /**
     * @Route("/admin/linksdetails/{id}", name="admin_links_details")
     */
    public function linksdetailsAction(Request $request, EntityManagerInterface $em, $id)
    {

            //$id = $request->query->get('id');
            $action = $request->request->get('Action');
            $repository = $em->getRepository('App:Links');

            $linkDetails = $repository->findOneBy(array('id' => $id));


        if ($request->getRealMethod() == 'POST') {
            if (!$linkDetails) {
                $linkDetails = new Links();
            }
            if ($action == 'Save') {
                $linkDetails->setPozycja($request->request->get('pozycja'));
                $linkDetails->setEtykieta($request->request->get('etykieta'));
                $linkDetails->setLink($request->request->get('link'));
                $linkDetails->setStrona($request->request->get('strona'));
                $linkDetails->setLang($request->request->get('lang'));

                $entityManager = $em;
                $entityManager->persist($linkDetails);
                $entityManager->flush();
            } elseif ($action == 'Delete') {
                if ($linkDetails) {
                     $em->remove($linkDetails);
                     $em->flush();
                }
            }
            return $this->redirectToRoute("admin-links");
        }
        if (!$linkDetails) {
            $linkDetails = '';
        }

            return $this->render('admin/adminlinksdetails.html.twig', array(
                'logs' => $linkDetails,

            ));
    }

    /**
     * @Route("/admin/linksnew", name="new-link")
     */
    public function linkNewAction(Request $request, EntityManagerInterface $em)
    {

        if ($request->getRealMethod() == 'POST') {
            $linkDetails = new Links();
            $linkDetails->setPozycja($request->request->get('pozycja'));
            $linkDetails->setEtykieta($request->request->get('etykieta'));
            $linkDetails->setLink($request->request->get('link'));
            $linkDetails->setStrona($request->request->get('strona'));
            $linkDetails->setLang($request->request->get('lang'));

            $entityManager = $em;
            $entityManager->persist($linkDetails);
            $entityManager->flush();

            return $this->render('admin/adminlinksdetails.html.twig', array(
                'logs' => $linkDetails

            ));
        } else {
            return $this->render('admin/adminlinksdetails.html.twig', array(
                'logs' => null

            ));
        }
    }

    /**
     * @Route("/admin/pages", name="admin-pages")
     */
    public function pagesAction()
    {
            return $this->render('admin/pages.html.twig');
    }

    

    /**
     * @Route("/admin/pagesdelete", name="admin_pages_delete")
     */
    public function pagesDeleteAction(Request $request, EntityManagerInterface $em)
    {

        
        $data = $request->get('dataTables');
        $ids  = $data['actions'];

        $qb = $em->createQueryBuilder();
        $qb->select('p');
        $qb->from('App:Pages', 'p');
        $qb->where($qb->expr()->in('p.id', $ids));

        //ArrayCollection
        $result = $qb->getQuery()->getResult();
        
        if ($result) {
            foreach ($result as $page) {
                $em->remove($page);
                $em->flush();
            }
        }
         return $this->redirectToRoute("admin-pages");
    }
    
    /**
     * @Route("/admin/pagesAjax", name="admin_pagesAjax")
     */
    public function pagesAjaxAction(Request $request, Connection $connection, DataTablesSupport $dtSupp)
    {

        $cols = array(
            array( "db" => "ID", "dt" => "0" ),
            array( "db" => "etykieta", "dt" => "1" ),
            array( "db" => "link", "dt" => "2" ),
            array( "db" => "lang", "dt" => "3" ),

        );

        $limitSql = $dtSupp->limitData($request->query->all());
        $orderSql = $dtSupp->orderData($request->query->all(), $cols);
        $filterSql = $dtSupp->filterData($request->query->all(), $cols);
        
        $logs_total = $connection->fetchAllAssociative("SELECT id, etykieta, link, lang FROM pages ");

        $logs_raw = $connection->fetchAllAssociative("SELECT id, etykieta, link, lang FROM pages ".$filterSql.' '.$orderSql.' '.$limitSql);

        $response = array();
        $response['draw'] = intval($request->query->get('draw'));
        $response["recordsTotal"] = count($logs_total);
        $response["recordsFiltered"] = count($logs_total);
        $response['data'] = array();

        foreach ($logs_raw as $item) {
            $response['data'][] = [$item['id'], $item['etykieta'], $item['link'], $item['lang']];
        }

        return new Response(json_encode($response));
    }

    /**
     * @Route("/admin/pagesdetails/{id}", name="admin_pages_details")
     */
    public function pagesdetailsAction(Request $request, EntityManagerInterface $em, $id)
    {

            //$id = $request->query->get('id');
            $action = $request->request->get('Action');
            $repository = $em->getRepository('App:Pages');

            $pageDetails = $repository->findOneBy(array('id' => $id));


        if ($request->getRealMethod() == 'POST') {
            if (!$pageDetails) {
                $pageDetails = new Pages();
            }
            if ($action == 'Save') {
                $pageDetails->setEtykieta($request->request->get('etykieta'));
                $pageDetails->setLink($request->request->get('link'));
                $pageDetails->setLang($request->request->get('lang'));
                $pageDetails->setContent($request->request->get('content'));

                $entityManager = $em;
                $entityManager->persist($pageDetails);
                $entityManager->flush();
            } elseif ($action == 'Delete') {
                if ($pageDetails) {
                    $em->remove($pageDetails);
                    $em->flush();
                }
            }
            return $this->redirectToRoute("admin-pages");
        }

        if (!$pageDetails) {
            $pageDetails = '';
        }

            return $this->render('admin/adminpagesdetails.html.twig', array(
                'logs' => $pageDetails,

            ));
    }

    /**
     * @Route("/admin/pagesnew", name="new-page")
     */
    public function pageNewAction(Request $request, EntityManagerInterface $em)
    {
        if ($request->getRealMethod() == 'POST') {
            $pageDetails = new Pages();
            $pageDetails->setEtykieta($request->request->get('etykieta'));
            $pageDetails->setLink($request->request->get('link'));
            $pageDetails->setLang($request->request->get('lang'));
            $pageDetails->setContent($request->request->get('content'));

            $entityManager = $em;
            $entityManager->persist($pageDetails);
            $entityManager->flush();

            return $this->render('admin/adminpagesdetails.html.twig', array(
                'logs' => $pageDetails

            ));
        } else {
            return $this->render('admin/adminpagesdetails.html.twig', array(
                'logs' => null

            ));
        }
    }

    /**
     * @Route("/admin/posts", name="admin-posts")
     */
    public function postsAction()
    {


        return $this->render('admin/posts.html.twig');
    }

    

    /**
     * @Route("/admin/postsAjax", name="admin_postsAjax")
     */
    public function postsAjaxAction(Request $request, Connection $connection, DataTablesSupport $dtSupp)
    {

        $cols = array(
            array( "db" => "ID", "dt" => "0" ),
            array( "db" => "title", "dt" => "1" ),
            array( "db" => "category", "dt" => "2" ),
            array( "db" => "lang", "dt" => "3" ),

        );

        $limitSql = $dtSupp->limitData($request->query->all());
        $orderSql = $dtSupp->orderData($request->query->all(), $cols);
        $filterSql = $dtSupp->filterData($request->query->all(), $cols);
        
        $logs_total = $connection->fetchAllAssociative("SELECT id, title, category, lang FROM blogposts ");

        $logs_raw = $connection->fetchAllAssociative("SELECT id, title, category, lang FROM blogposts ".$filterSql.' '.$orderSql.' '.$limitSql);

        $response = array();
        $response['draw'] = intval($request->query->get('draw'));
        $response["recordsTotal"] = count($logs_total);
        $response["recordsFiltered"] = count($logs_total);
        $response['data'] = array();

        foreach ($logs_raw as $item) {
            $response['data'][] = [$item['id'], $item['title'], $item['category'], $item['lang']];
        }

        return new Response(json_encode($response));
    }

    /**
     * @Route("/admin/postsdetails/{id}", name="admin_posts_details")
     */
    public function postsdetailsAction(Request $request, EntityManagerInterface $em, $id)
    {

        //$id = $request->query->get('id');
        $action = $request->request->get('Action');
        $repository = $em->getRepository('App:BlogPost');

        $postDetails = $repository->findOneBy(array('id' => $id));


        if ($request->getRealMethod() == 'POST') {
            if (!$postDetails) {
                $postDetails = new BlogPost();
            }
            if ($action == 'Save') {
                $postDetails->setTitle($request->request->get('title'));
                $postDetails->setCategory($request->request->get('category'));
                $postDetails->setLang($request->request->get('lang'));
                $postDetails->setDate($request->request->get('date'));
                $postDetails->setContent($request->request->get('content'));

                $entityManager = $em;
                $entityManager->persist($postDetails);
                $entityManager->flush();
            } elseif ($action == 'Delete') {
                if ($postDetails) {
                    $em->remove($postDetails);
                    $em->flush();
                }
            }
            return $this->redirectToRoute("admin-posts");
        }
        if (!$postDetails) {
            $postDetails = '';
        }

        return $this->render('admin/adminpostsdetails.html.twig', array(
            'logs' => $postDetails,

        ));
    }

    /**
     * @Route("/admin/postsnew", name="new-post")
     */
    public function postNewAction(Request $request, EntityManagerInterface $em)
    {

        if ($request->getRealMethod() == 'POST') {
            $postDetails = new BlogPost();
            $postDetails->setTitle($request->request->get('title'));
            $postDetails->setCategory($request->request->get('category'));
            $postDetails->setLang($request->request->get('lang'));
            $postDetails->setDate($request->request->get('date'));
            $postDetails->setContent($request->request->get('content'));

            $entityManager = $em;
            $entityManager->persist($postDetails);
            $entityManager->flush();

            return $this->render('admin/adminpostsdetails.html.twig', array(
                'logs' => $postDetails

            ));
        } else {
            return $this->render('admin/adminpostsdetails.html.twig', array(
                'logs' => null

            ));
        }
    }

    /**
     * @Route("/admin/postsdelete", name="admin_posts_delete")
     */
    public function postsDeleteAction(Request $request, EntityManagerInterface $em)
    {


        $data = $request->get('dataTables');
        $ids  = $data['actions'];

        $qb = $em->createQueryBuilder();
        $qb->select('l');
        $qb->from('App:BlogPost', 'l');
        $qb->where($qb->expr()->in('l.id', $ids));

        //ArrayCollection
        $result = $qb->getQuery()->getResult();

        if ($result) {
            foreach ($result as $link) {
                $em->remove($link);
                $em->flush();
            }
        }
        return $this->redirectToRoute("admin-posts");
    }

    /**
     * @Route("/admin/faq", name="admin-faq")
     */
    public function faqAction()
    {


        return $this->render('admin/faq.html.twig');
    }

    /**
     * @Route("/admin/faqAjax", name="admin_faqAjax")
     */
    public function faqAjaxAction(Request $request, Connection $connection, DataTablesSupport $dtSupp)
    {

        $cols = array(
            array( "db" => "ID", "dt" => "0" ),
            array( "db" => "title", "dt" => "1" ),
            array( "db" => "category", "dt" => "2" ),
            array( "db" => "lang", "dt" => "3" ),

        );

        $limitSql = $dtSupp->limitData($request->query->all());
        $orderSql = $dtSupp->orderData($request->query->all(), $cols);
        $filterSql = $dtSupp->filterData($request->query->all(), $cols);
        
        $logs_total = $connection->fetchAllAssociative("SELECT id, title, category, lang FROM faq ");

        $logs_raw = $connection->fetchAllAssociative("SELECT id, title, category, lang FROM faq ".$filterSql.' '.$orderSql.' '.$limitSql);

        $response = array();
        $response['draw'] = intval($request->query->get('draw'));
        $response["recordsTotal"] = count($logs_total);
        $response["recordsFiltered"] = count($logs_total);
        $response['data'] = array();

        foreach ($logs_raw as $item) {
            $response['data'][] = [$item['id'], $item['title'], $item['category'], $item['lang']];
        }

        return new Response(json_encode($response));
    }

    /**
     * @Route("/admin/faqdetails/{id}", name="admin_faq_details")
     */
    public function faqdetailsAction(Request $request, EntityManagerInterface $em, $id)
    {

        //$id = $request->query->get('id');
        $action = $request->request->get('Action');
        $repository = $em->getRepository('App:FAQ');

        $faqDetails = $repository->findOneBy(array('id' => $id));


        if ($request->getRealMethod() == 'POST') {
            if (!$faqDetails) {
                $faqDetails = new FAQ();
            }
            if ($action == 'Save') {
                $faqDetails->setTitle($request->request->get('title'));
                $faqDetails->setCategory($request->request->get('category'));
                $faqDetails->setLang($request->request->get('lang'));
                $faqDetails->setContent($request->request->get('content'));

                $entityManager = $em;
                $entityManager->persist($faqDetails);
                $entityManager->flush();
            } elseif ($action == 'Delete') {
                if ($faqDetails) {
                    $em->remove($faqDetails);
                    $em->flush();
                }
            }
            return $this->redirectToRoute("admin-faq");
        }
        if (!$faqDetails) {
            $faqDetails = '';
        }

        return $this->render('admin/adminfaqdetails.html.twig', array(
            'logs' => $faqDetails,

        ));
    }

    /**
     * @Route("/admin/faqnew", name="new-faq")
     */
    public function faqNewAction(Request $request, EntityManagerInterface $em)
    {

        if ($request->getRealMethod() == 'POST') {
            $faqDetails = new FAQ();
            $faqDetails->setTitle($request->request->get('title'));
            $faqDetails->setCategory($request->request->get('category'));
            $faqDetails->setLang($request->request->get('lang'));
            $faqDetails->setContent($request->request->get('content'));

            $entityManager = $em;
            $entityManager->persist($faqDetails);
            $entityManager->flush();

            return $this->render('admin/adminfaqdetails.html.twig', array(
                'logs' => $faqDetails

            ));
        } else {
            return $this->render('admin/adminfaqdetails.html.twig', array(
                'logs' => null

            ));
        }
    }

    /**
     * @Route("/admin/download", name="admin-downloads")
     */
    public function downloadsAction()
    {


        return $this->render('admin/download.html.twig');
    }

    /**
     * @Route("/admin/downloadAjax", name="admin_downloadsAjax")
     */
    public function downloadsAjaxAction(Request $request, Connection $connection, DataTablesSupport $dtSupp)
    {

        $cols = array(
            array( "db" => "ID", "dt" => "0" ),
            array( "db" => "dl_name", "dt" => "1" ),
            array( "db" => "category", "dt" => "2" ),
            array( "db" => "lang", "dt" => "3" ),
            array( "db" => "version", "dt" => "4" ),


        );

        $limitSql = $dtSupp->limitData($request->query->all());
        $orderSql = $dtSupp->orderData($request->query->all(), $cols);
        $filterSql = $dtSupp->filterData($request->query->all(), $cols);

        $logs_total = $connection->fetchAllAssociative("SELECT id FROM download ");

        $logs_raw = $connection->fetchAllAssociative("SELECT id, dl_name, version, author, lang, category, dl_size, license, download_path, imagepath, description FROM download ".$filterSql.' '.$orderSql.' '.$limitSql);

        $response = array();
        $response['draw'] = intval($request->query->get('draw'));
        $response["recordsTotal"] = count($logs_total);
        $response["recordsFiltered"] = count($logs_total);
        $response['data'] = array();

        foreach ($logs_raw as $item) {
            $response['data'][] = [$item['id'], $item['dl_name'], $item['category'], $item['lang'], $item['version']];
        }

        return new Response(json_encode($response));
    }

    /**
     * @Route("/admin/downloaddetails/{id}", name="admin_download_details")
     */
    public function downloaddetailsAction(Request $request, EntityManagerInterface $em, $id)
    {

        //$id = $request->query->get('id');
        $action = $request->request->get('Action');
        $repository = $em->getRepository('App:Download');

        $dlDetails = $repository->findOneBy(array('id' => $id));


        if ($request->getRealMethod() == 'POST') {
            if (!$dlDetails) {
                $dlDetails = new Download();
            }
            if ($action == 'Save') {
                $dlDetails->setDlName($request->request->get('dl_name'));
                $dlDetails->setCategory($request->request->get('category'));
                $dlDetails->setLang($request->request->get('lang'));
                $dlDetails->setDate($request->request->get('date'));
                $dlDetails->setAuthor($request->request->get('author'));
                $dlDetails->setDownloadPath($request->request->get('download_path'));
                $dlDetails->setImagepath($request->request->get('imagepath'));
                $dlDetails->setLicense($request->request->get('license'));
                $dlDetails->setDlSize($request->request->get('dl_size'));
                $dlDetails->setVersion($request->request->get('version'));
                $dlDetails->setDescription($request->request->get('description'));

                $entityManager = $em;
                $entityManager->persist($dlDetails);
                $entityManager->flush();
            } elseif ($action == 'Delete') {
                if ($dlDetails) {
                    $em->remove($dlDetails);
                    $em->flush();
                }
            }
            return $this->redirectToRoute("admin-downloads");
        }
        if (!$dlDetails) {
            $dlDetails = '';
        }

        return $this->render('admin/admindownloaddetails.html.twig', array(
            'logs' => $dlDetails,

        ));
    }

    /**
     * @Route("/admin/downloadnew", name="new-download")
     */
    public function downloadNewAction(Request $request, EntityManagerInterface $em)
    {

        if ($request->getRealMethod() == 'POST') {
            $dlDetails = new Download();
            $dlDetails->setDlName($request->request->get('dl_name'));
            $dlDetails->setCategory($request->request->get('category'));
            $dlDetails->setLang($request->request->get('lang'));
            $dlDetails->setDate($request->request->get('date'));
            $dlDetails->setAuthor($request->request->get('author'));
            $dlDetails->setDownloadPath($request->request->get('download_path'));
            $dlDetails->setImagepath($request->request->get('imagepath'));
            $dlDetails->setLicense($request->request->get('license'));
            $dlDetails->setDlSize($request->request->get('dl_size'));
            $dlDetails->setVersion($request->request->get('version'));
            $dlDetails->setDescription($request->request->get('description'));

            $entityManager = $em;
            $entityManager->persist($dlDetails);
            $entityManager->flush();

            return $this->render('admin/admindownloaddetails.html.twig', array(
                'logs' => $dlDetails

            ));
        } else {
            return $this->render('admin/admindownloaddetails.html.twig', array(
                'logs' => null

            ));
        }
    }

    /**
     * @Route("/admin/users", name="admin-users")
     */
    public function usersAction()
    {


        return $this->render('admin/user.html.twig');
    }

    /**
     * @Route("/admin/usersAjax", name="admin_usersAjax")
     */
    public function usersAjaxAction(Request $request, Connection $connection, DataTablesSupport $dtSupp)
    {

        $cols = array(
            array( "db" => "ID", "dt" => "0" ),
            array( "db" => "username", "dt" => "1" ),
            array( "db" => "password", "dt" => "2" ),
            array( "db" => "email", "dt" => "3" ),
        );

        $limitSql = $dtSupp->limitData($request->query->all());
        $orderSql = $dtSupp->orderData($request->query->all(), $cols);
        $filterSql = $dtSupp->filterData($request->query->all(), $cols);

        $logs_total = $connection->fetchAllAssociative("SELECT id FROM users ");

        $logs_raw = $connection->fetchAllAssociative("SELECT id, username, password, email FROM users ".$filterSql.' '.$orderSql.' '.$limitSql);

        $response = array();
        $response['draw'] = intval($request->query->get('draw'));
        $response["recordsTotal"] = count($logs_total);
        $response["recordsFiltered"] = count($logs_total);
        $response['data'] = array();

        foreach ($logs_raw as $item) {
            $response['data'][] = [$item['id'], $item['username'], $item['password'], $item['email']];
        }

        return new Response(json_encode($response));
    }

    /**
     * @Route("/admin/userdetails/{id}", name="admin_user_details")
     */
    public function userdetailsAction(Request $request, EntityManagerInterface $em, $id)
    {

        //$id = $request->query->get('id');
        $action = $request->request->get('Action');
        $repository = $em->getRepository('App:User');

        $userDetails = $repository->findOneBy(array('id' => $id));


        if ($request->getRealMethod() == 'POST') {
            if (!$userDetails) {
                $userDetails = new User();
            }
            if ($action == 'Save') {
                $userDetails->setUsername($request->request->get('username'));
                $userDetails->setPassword($request->request->get('password'));
                $userDetails->setEmail($request->request->get('email'));


                $entityManager = $em;
                $entityManager->persist($userDetails);
                $entityManager->flush();
            } elseif ($action == 'Delete') {
                if ($userDetails) {
                    $em->remove($userDetails);
                    $em->flush();
                }
            }
            return $this->redirectToRoute("admin-users");
        }
        if (!$userDetails) {
            $userDetails = '';
        }

        return $this->render('admin/adminuserdetails.html.twig', array(
            'logs' => $userDetails,

        ));
    }

    /**
     * @Route("/admin/usernew", name="new-user")
     */
    public function userNewAction(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordEncoder)
    {
        if ($request->getRealMethod() == 'POST') {
            $userDetails = new User();
            $userDetails->setUsername($request->request->get('username'));
            $userDetails->setPassword($passwordEncoder->hashPassword($userDetails, $request->request->get('password')));
            $userDetails->setEmail($request->request->get('email'));
            $entityManager = $em;
            $entityManager->persist($userDetails);
            $entityManager->flush();

            return $this->render('admin/adminuserdetails.html.twig', array(
                'logs' => $userDetails

            ));
        } else {
            return $this->render('admin/adminuserdetails.html.twig', array(
                'logs' => null

            ));
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 2018-02-20
 * Time: 21:27
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="links")
 */
class Links
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $pozycja;

    /**
     * @ORM\Column(type="string")
     */
    private $etykieta;

    /**
     * @ORM\Column(type="string")
     */
    private $link;

    /**
     * @ORM\Column(type="string")
     */
    private $strona;

    /**
     * @ORM\Column(type="string")
     */
    private $lang;





    public function getId()
    {
        return $this->id;
    }

    public function getPozycja()
    {
        return $this->pozycja;
    }

    public function getEtykieta()
    {
        return $this->etykieta;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getStrona()
    {
        return $this->strona;
    }

    public function getLang()
    {
        return $this->lang;
    }

    public function setPozycja($value)
    {
        $this->pozycja = $value;
    }

    public function setEtykieta($value)
    {
        $this->etykieta = $value;
    }

    public function setLink($value)
    {
        $this->link = $value;
    }

    public function setStrona($value)
    {
        $this->strona = $value;
    }

    public function setLang($value)
    {
        $this->lang = $value;
    }


}




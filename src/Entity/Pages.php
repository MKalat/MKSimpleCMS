<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 2018-02-20
 * Time: 21:26
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pages")
 */
class Pages
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $link;

    /**
     * @ORM\Column(type="string")
     */
    private string $etykieta;

    /**
     * @ORM\Column(type="text")
     */
    private string $content;

    /**
     * @ORM\Column(type="string")
     */
    private string $lang;

    /**
     * @return mixed
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     */
    public function setLink(string $link)
    {
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getEtykieta(): string
    {
        return $this->etykieta;
    }

    /**
     * @param mixed $etykieta
     */
    public function setEtykieta(string $etykieta)
    {
        $this->etykieta = $etykieta;
    }

    /**
     * @return mixed
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     */
    public function setLang(string $lang)
    {
        $this->lang = $lang;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 2019-01-05
 * Time: 19:12
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="blogposts")
 */
class BlogPost
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
    private string $title;

    /**
     * @ORM\Column(type="text")
     */
    private string $content;

    /**
     * @ORM\Column(type="string")
     */
    private string $category;

    /**
     * @ORM\Column(type="string")
     */
    private string $date;

    /**
     * @ORM\Column(type="string")
     */
    private string $lang;
    
    /**
     * @ORM\Column(type="string")
     */
    private string $timestamp;

    
    /**
     * @return mixed
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp(string $timestamp)
    {
        $this->timestamp = strtotime($timestamp);
    }

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
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
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
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
        $this->setTimestamp($date);
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
    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }
}

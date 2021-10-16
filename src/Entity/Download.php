<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="download")
 */
class Download
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
    private $dl_name;

    /**
     * @ORM\Column(type="string")
     */
    private $download_path;

    /**
     * @ORM\Column(type="string")
     */
    private $imagepath;

    /**
     * @ORM\Column(type="string")
     */
    private $version;

    /**
     * @ORM\Column(type="string")
     */
    private $license;

    /**
     * @ORM\Column(type="string")
     */
    private $author;

    /**
     * @ORM\Column(type="string")
     */
    private $date;

    /**
     * @ORM\Column(type="string")
     */
    private $dl_size;

    /**
     * @ORM\Column(type="string")
     */
    private $category;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     */
    private $lang;
    
    /**
     * @ORM\Column(type="string")
     */
    private $timestamp;
    
    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
    
    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = strtotime($timestamp);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }



    /**
     * @return mixed
     */
    public function getDownloadPath()
    {
        return $this->download_path;
    }

    /**
     * @param mixed $download_path
     */
    public function setDownloadPath($download_path): void
    {
        $this->download_path = $download_path;
    }

    /**
     * @return mixed
     */
    public function getImagepath()
    {
        return $this->imagepath;
    }

    /**
     * @param mixed $imagepath
     */
    public function setImagepath($imagepath): void
    {
        $this->imagepath = $imagepath;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version): void
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * @param mixed $license
     */
    public function setLicense($license): void
    {
        $this->license = $license;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
        $this->setTimestamp($date);
    }


    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     */
    public function setLang($lang): void
    {
        $this->lang = $lang;
    }

    /**
     * @return mixed
     */
    public function getDlName()
    {
        return $this->dl_name;
    }

    /**
     * @param mixed $dl_name
     */
    public function setDlName($dl_name): void
    {
        $this->dl_name = $dl_name;
    }

    /**
     * @return mixed
     */
    public function getDlSize()
    {
        return $this->dl_size;
    }

    /**
     * @param mixed $dl_size
     */
    public function setDlSize($dl_size): void
    {
        $this->dl_size = $dl_size;
    }

}
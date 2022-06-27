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
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $dl_name;

    /**
     * @ORM\Column(type="string")
     */
    private string $download_path;

    /**
     * @ORM\Column(type="string")
     */
    private string $imagepath;

    /**
     * @ORM\Column(type="string")
     */
    private string $version;

    /**
     * @ORM\Column(type="string")
     */
    private string $license;

    /**
     * @ORM\Column(type="string")
     */
    private string $author;

    /**
     * @ORM\Column(type="string")
     */
    private string $date;

    /**
     * @ORM\Column(type="string")
     */
    private string $dl_size;

    /**
     * @ORM\Column(type="string")
     */
    private string $category;

    /**
     * @ORM\Column(type="text")
     */
    private string $description;

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
    public function getDownloadPath(): string
    {
        return $this->download_path;
    }

    /**
     * @param mixed $download_path
     */
    public function setDownloadPath(string $download_path): void
    {
        $this->download_path = $download_path;
    }

    /**
     * @return mixed
     */
    public function getImagepath(): string
    {
        return $this->imagepath;
    }

    /**
     * @param mixed $imagepath
     */
    public function setImagepath(string $imagepath): void
    {
        $this->imagepath = $imagepath;
    }

    /**
     * @return mixed
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getLicense(): string
    {
        return $this->license;
    }

    /**
     * @param mixed $license
     */
    public function setLicense(string $license): void
    {
        $this->license = $license;
    }

    /**
     * @return mixed
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
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
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
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

    /**
     * @return mixed
     */
    public function getDlName(): string
    {
        return $this->dl_name;
    }

    /**
     * @param mixed $dl_name
     */
    public function setDlName(string $dl_name): void
    {
        $this->dl_name = $dl_name;
    }

    /**
     * @return mixed
     */
    public function getDlSize(): string
    {
        return $this->dl_size;
    }

    /**
     * @param mixed $dl_size
     */
    public function setDlSize(string $dl_size): void
    {
        $this->dl_size = $dl_size;
    }
}

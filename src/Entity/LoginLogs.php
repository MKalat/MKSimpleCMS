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
 * @ORM\Table(name="loginlogs")
 */
class LoginLogs
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
    private string $login;

    /**
     * @ORM\Column(type="string")
     */
    private string $ip;

    /**
     * @ORM\Column(type="string")
     */
    private string $czas;

    /**
     * @ORM\Column(type="string")
     */
    private string $status;

    /**
     * @ORM\Column(type="string")
     */
    private string $ranga;

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
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin(string $login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp(string $ip)
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getCzas(): string
    {
        return $this->czas;
    }

    /**
     * @param mixed $czas
     */
    public function setCzas(string $czas)
    {
        $this->czas = $czas;
    }

    /**
     * @return mixed
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getRanga(): string
    {
        return $this->ranga;
    }

    /**
     * @param mixed $ranga
     */
    public function setRanga(string $ranga)
    {
        $this->ranga = $ranga;
    }
}

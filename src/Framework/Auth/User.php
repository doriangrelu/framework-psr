<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 26/11/2017
 * Time: 16:38
 */

namespace Framework\Auth;


class User implements UserInterface
{
    /**
     * @var string
     */
    private $username;
    /**
     * @var array|null
     */
    private $roles;
    /**
     * @var int
     */
    private $id;

    /**
     * @var null|string
     */
    private $secret;


    public function __construct(string $username, int $id, ?array $roles = [])
    {
        $this->username = $username;
        $this->roles = $roles;
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function getSecretKey():?string
    {
        return $this->secret;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}
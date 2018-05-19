<?php
namespace Framework\Session;

interface SessionInterface
{
    /**
     * Retourne le tableau de session
     * @return array
     */
    public function getAll():array;

    /**
     * Récupère une information en Session
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Ajoute une information en Session
     *
     * @param string $key
     * @param $value
     * @return mixed
     */
    public function set(string $key, $value): void;

    /**
     * Supprime une clef en session
     * @param string $key
     */
    public function delete(string $key): void;

    /**
     * @param string $key
     * @return bool
     */
    public function exist(string $key):bool;
}

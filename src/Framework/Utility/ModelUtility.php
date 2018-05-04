<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 06/12/2017
 * Time: 15:50
 */

namespace Framework\Utility;


use Framework\Database\Query;

trait ModelUtility
{

    /**
     * @param string $key
     * @param $value
     * @param array $params
     */
    protected function updateKey(string $key, $value, array &$params)
    {
        if (array_key_exists($key, $params)) {
            $this->deleteKeys($key, $params);
            $this->addKey($key, $value, $params);
        }
    }

    /**
     * @param string $name
     * @param array $params
     */
    protected function notRequired(string $name, array &$params): void
    {
        if (empty($params[$name])) {
            $this->deleteKeys($name, $params);
        }
    }

    /**
     * @param string $from
     * @param string $to
     * @param array $params
     */
    protected function renameKey(string $from, string $to, array &$params): void
    {
        $values = $params[$from];
        $this->deleteKeys($from, $params);
        $this->addKey($to, $values, $params);
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $params
     */
    protected function replaceKey(string $name, string $value, array &$params): void
    {
        $params[$name]=$value;
    }

    protected function query(): Query
    {
        return $this->container->get(Query::class);
    }

    /**
     * Ajoute une valeur dans le tableau en fonction d'une clef passée
     * @param string $key
     * @param string $value
     * @param array $params
     */
    protected function addKey(string $key, string $value, array &$params): void
    {
        $this->deleteKeys($key, $params);
        $params[$key] = $value;
    }

    /**
     * Defininie dans les paramètres la date de création
     * @param array $params
     */
    protected function setCreatedAt(array &$params)
    {
        $this->deleteKeys("created_at", $params);
        $params = array_merge($params, [
            "created_at" => date("Y-m-d H:i:s")
        ]);
        $this->setUpdatedAt($params);
    }

    /**
     * définie dans les paramètres la date de modification
     * @param array $params
     */
    protected function setUpdatedAt(array &$params)
    {
        $this->deleteKeys("updated_at", $params);
        $params = array_merge($params, [
            "updated_at" => date("Y-m-d H:i:s")
        ]);
    }

    /**
     * Supprime la clef du tableau et retourne ce dernier
     * @param string $keys
     * @param array $params
     */
    protected function deleteKeys(string $keys, array &$params)
    {
        if (isset($params[$keys])) {
            unset($params[$keys]);
        }
    }
}
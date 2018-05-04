<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 01/12/17
 * Time: 12:41
 */

namespace Framework\Utility;


trait ClassNameUtility
{
    /**
     * @param string $controller
     * @return string
     * @throws \Exception
     */
    private function getControllerName(string $class): string
    {
        $explode = explode("\\", $class);
        if (isset($explode[4])) {
            return $explode[4];
        } else {
            throw new \Exception("Error, bad bundle structure: <$explode[4]>");
        }
    }

    /**
     * @param string $class
     * @return string
     * @throws \Exception
     */
    private function getBundleName(string $class): string
    {
        $explode = explode("\\", $class);
        $bundleName = null;
        if (isset($explode[2])) {
            if (is_dir(BUNDLE . $explode[2])) {
                $bundleName = $explode[2];
            } else {
                throw new \Exception("Error, missing bundle: <$bundleName>");
            }
        } else {
            throw new \Exception("Error, bad bundle structure: <$class>");
        }
        return $bundleName;
    }

    /**
     * @param string $class
     * @return null|string
     * @throws \Exception
     */
    private function getControllerFileName(string $class):?string
    {
        $explode = explode("\\", $class);
        if (isset($explode[4])) {
            $controlleFileName = BUNDLE . $this->getBundleName($class) . DS . "Controller" . DS . $explode[2];
            if (is_file($controlleFileName . '.php')) {
                return $controlleFileName . DS;
            } else {
                throw new \Exception("Error, missing controller: <$controlleFileName>");
            }
        } else {
            throw new \Exception("Error, bad bundle structure: <$class>");
        }
    }


}
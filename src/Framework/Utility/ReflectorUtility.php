<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 02/12/2018
 * Time: 15:47
 */

namespace Framework\Utility;


trait ReflectorUtility
{
    /**
     * @var \ReflectionClass | null
     */
    private $_reflector = null;

    /**
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    private function _getReflector(): \ReflectionClass
    {
        if ($this->_reflector === null) {
            $this->_reflector = new \ReflectionClass($this);
        }
        return $this->_reflector;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function getClassName(): string
    {
        return $this->_getReflector()->getName();
    }


}
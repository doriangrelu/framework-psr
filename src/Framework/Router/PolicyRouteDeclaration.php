<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 27/11/17
 * Time: 10:30
 */

namespace Framework\Router;


use Framework\Router;
use Psr\Container\ContainerInterface;
use Framework\Router\PolicyRoute;

class PolicyRouteDeclaration
{
    /**
     * @var PolicyRoute[]
     */
    private static $policiesRoutes = [];


    /**
     * Ajoute un tableau de règle d'accès aux routes
     * @param array $policies
     */
    public static function addPolicies(array $policies): void
    {
        self::$policiesRoutes = array_merge(self::$policiesRoutes, $policies);
    }

    /**
     * Prend en paramètre une instance de PolicyRoute définissant donc une règle pour une route
     * @param PolicyRoute $routePolicy
     */
    public static function addPolicy(PolicyRoute $routePolicy): void
    {
        self::$policiesRoutes[] = $routePolicy;
    }

    /**
     * Retourne la règle concernant un accès controlleur ou route, null si aucun n'est définie
     * @param null $bundle
     * @param null $class
     * @param null|string $method
     * @param null|string $routeName
     * @return \Framework\Router\PolicyRoute|null
     */
    public static function getPolicy($bundle=null, $class = null, ?string $method = null, ?string $routeName = null): ?PolicyRoute
    {
        foreach (self::$policiesRoutes as $policy) {
            if (($policy->getBundle()==$bundle && $policy->getClass()=="*") ||
                ($policy->getClass() == $class &&
                    ($policy->getMethod() == "*" || $policy->getMethod() == $method)) ||
                $policy->getRouteName() == $routeName) {
                return $policy;
            }
        }
        return null;
    }
}
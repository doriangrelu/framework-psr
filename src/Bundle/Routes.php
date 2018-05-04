<?php
/**
 * Created by PhpStorm.
 * User: dorian
 * Date: 22/11/17
 * Time: 15:49
 */

namespace App\Bundle;


use App\Bundle\Auth\Controller\Auth;
use App\Bundle\Auth\Controller\Totp;
use App\Bundle\Membres\Controller\Clients;
use App\Bundle\Membres\Controller\Informations;
use App\Bundle\Pages\Controller\Pages;
use App\Bundle\Pages\Controller\Samuel;
use App\Middlewares\TestMiddleware;
use Framework\Router;
use Framework\Router\PolicyRouteDeclaration;
use Framework\Router\RouteScope;
use Psr\Container\ContainerInterface;

class Routes extends PolicyRouteDeclaration
{

    /**
     * Initialise les propriétés de la classes, et lance les méthodes d'ajout de routes, et policies
     * @param ContainerInterface $container
     */
    public static function init(ContainerInterface $container)
    {
        self::routes($container->get(Router::class));
        self::policies();
    }

    /**
     * Définition des routes
     * @param Router $router
     */
    private static function routes(Router $router): void
    {
        //Nothing connexion space
        $router->scope("/", function (RouteScope $routes) {
            $routes->get("/", [Pages::class, "index"], "home")->bind(TestMiddleware::class);
            $routes->get("presentation", [Pages::class, "presentation"], "presentation");
        });

        //space for members
        $router->scope("/membre/", function (RouteScope $routes) {
            /*$routes->get("/", [\App\Bundle\Membres\Controller\Pages::class, "home"], "membre.home");
            $routes->get("/statistiques", [\App\Bundle\Membres\Controller\Pages::class, "statistiques"], "membre.stats");
            $routes->get("/telecharger-statistiques", [\App\Bundle\Membres\Controller\Pages::class, "getCsvNouveauxClients"], "membre.stats.down");
            $routes->post("set-informations", [Informations::class, "setInformations"], "membre.setInformations");
            $routes->post("set-informations-entreprise",
                [Informations::class, "setInformationsEntreprise"],
                "membre.setInformationsEntreprise"
            );
            $routes->scope("devis", function (RouteScope $routes) {
                $routes->get("/", [\App\Bundle\Membres\Controller\Pages::class, "devis"], "devis");
            });*/
            /*
            $routes->scope("/donnees/", function (RouteScope $routes) {
                $routes->scope("clients", function (RouteScope $routes) {
                    $routes->get("/", [\App\Bundle\Membres\Controller\Pages::class, "clients"], "membre.clients");
                    $routes->get("/:type-:id", [Clients::class, "afficher"], "membre.client.afficher")
                        ->width("id", "[0-9]+")
                        ->width("type", "[a-z]+");

                    $routes->post("/modifier-:type-:id", [Clients::class, "modifier"], "membre.client.modifier")->width("id", "[0-9]+")
                        ->width("type", "[a-z]+");

                    $routes->post("supprimer-:type-:id", [Clients::class, "supprimer"], "membre.client.supprimer")
                        ->width("id", "[0-9]+")
                        ->width("type", "[a-z]+");

                    $routes->post("modifier-type-:type-:id", [Clients::class, "modifierType"], "membre.client.changer-type")
                        ->width("id", "[0-9]+")
                        ->width("type", "[a-z]+");

                    $routes->post("vider-base-client", [Clients::class, "vider"], "membre.client.truncate");

                    $routes->post("ajouter", [Clients::class, "ajouter"], "membre.clients.ajouter");
                    $routes->post("liste-professionnel", [Clients::class, "getListeProfessionnel"], "membre.clients.pro");
                    $routes->post("liste-particulier", [Clients::class, "getListeParticulier"], "membre.clients.par");
                });


                $routes->get("/prospects/", [\App\Bundle\Membres\Controller\Pages::class, "prospects"], "membre.prospects");
            });
            $routes->get("activation-authentification-a-deux-facteurs", [Totp::class, "activer"], "otp.active");
            $routes->post("desactivation-authentification-a-deux-facteurs", [Totp::class, "desactiver"], "otp.desactive");
            $routes->post("activation-otp", [Totp::class, "activer"], "otp.active.send");
            */
        });

        //Authentification

        /*
        $router->scope("/authentification/", function (RouteScope $routes) {
            $routes->get("connexion", [Auth::class, "formulaireConnexion"], "form.connexion");
            $routes->get("deconnexion", [Auth::class, "formulaireConnexion"], "form.deconnexion");
            $routes->get("verification", [Totp::class, "showCode"], "form.totp");
            $routes->post("verification", [Totp::class, "showCode"], "form.totp.send");

            $routes->get("inscription", [Auth::class, "formulaireInscription"], "form.inscription");
            $routes->post("connexion", [Auth::class, "seConnecter"], "connexion");
            $routes->post("inscription", [Auth::class, "setInscription"], "inscription");

        });*/

    }

    /**
     * Initialisation des règles d'accès aux routes
     */
    private static function policies(): void
    {
        //Définition des règles d'accès aux route ici
        self::addPolicies([
            (new Router\PolicyRoute([1]))->setBundle("Membres"),
            (new Router\PolicyRoute([1]))->setBundle("Parametres")
        ]);
    }


}
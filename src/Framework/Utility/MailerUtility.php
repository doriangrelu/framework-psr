<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 09/12/2017
 * Time: 15:55
 */

namespace Framework\Utility;


use Framework\Mailer\Mailer;
use Twig_Environment;
use Twig_Loader_Filesystem;

trait MailerUtility
{
    /**
     * @return Mailer
     */
    protected function mailer():Mailer
    {
        return $this->container->get(Mailer::class);
    }

    /**
     * Rends le template de vue pour un mail
     * @param string $pathView
     * @param array $pass
     * @return string
     * @throws \Exception
     */
    protected function getMailBody(string $pathView, array $pass=[]):string
    {
        $directory=TEMPLATE."Mailing".DS;
        if(!is_dir($directory)){
            throw new \Exception("Le dossier <$directory> n'existe pas");
        }
        $loader = new Twig_Loader_Filesystem($directory);
        $twig = new Twig_Environment($loader, array(
            'cache' => false
        ));
        $fileName=ucfirst($pathView).'.twig';
        if(!is_file($directory.$fileName)){
            throw new \Exception("La vue <$fileName> n'existe pas dans le dossier <$directory>");
        }
        $template = $twig->load($fileName);
        return $template->render($pass);
    }

}
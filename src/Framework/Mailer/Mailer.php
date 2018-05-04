<?php
/**
 * Created by PhpStorm.
 * User: doria
 * Date: 09/12/2017
 * Time: 12:47
 */

namespace Framework\Mailer;


use Framework\Exception\NoFromException;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerInterface;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class Mailer
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Swift_Message
     */
    private $message;

    /**
     * @var string
     */
    private $subject = "Sans objet";

    /**
     * @var string
     */
    private $from;

    /**
     * @var array
     */
    private $to = [];

    /**
     * @var string
     */
    private $body;

    /**
     * @var array
     */
    private $attach = [];


    /**
     * Mailer constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $transport = (new Swift_SmtpTransport($container->get("mail.host"), $container->get("mail.port")))
            ->setUsername($container->get("mail.username"))
            ->setPassword($container->get("mail.password"));
        $this->mailer = new Swift_Mailer($transport);
        $this->message = new Swift_Message();
        if ($this->container->has("mail.from")) {
            $this->from=$this->container->get("mail.from");
        }
    }

    /**
     * Défini le sujet du mail
     * @param string $subject
     * @return Mailer
     */
    public function setSubject(string $subject):self
    {
        $this->subject=$subject;
        return $this;
    }

    /**
     * Permet d'attacher un sujet au mail
     * @param string $path
     * @param string|null $fileName
     * @return Mailer
     */
    public function attach(string $path, string $fileName=null): self
    {
        $attachement=Swift_Attachment::fromPath($path);
        if(!is_null($fileName)){
            $attachement->setFilename($fileName);
        }
        $this->attach[] = $attachement;
        return $this;
    }

    /**
     * Permet d'attacher de manière dynamique un fichier au mail
     * @param $data
     * @param string $file
     * @param string $mimeType
     * @return Mailer
     */
    public function dynamicAttach($data, string $file, string $mimeType):self
    {
        $this->attach[]=new Swift_Attachment($data, $file, $mimeType);
        return $this;
    }

    /**
     * Définition du sender du message
     * @param string $from
     * @param null|string $name
     * @return Mailer
     */
    public function from(string $from, ?string $name=null):self
    {
        if(is_null($name)){
            $this->from=[$from];
        } else {
            $this->from=[$from=>$name];
        }
        return $this;
    }

    /**
     * Définition du corp du message
     * @param string $body
     * @return Mailer
     */
    public function body(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Définition des destinataires du message
     * @param $to
     * @param null $name
     * @return Mailer
     */
    public function to($to, $name = null): self
    {
        if (is_array($to)) {
            $this->to = array_merge($this->to, $to);
        } else {
            $this->to = array_merge($this->to, [$to => $name]);
        }
        return $this;
    }

    /**
     * Envoi le mail et retourne la réponse lié à l'envoi, false si cela a échoué
     * @return bool|int
     */
    public function send()
    {
        try {
            if(empty($this->from)){
                throw new NoFromException();
            }
            $this->message->setFrom($this->from);
            $this->message->setTo($this->to);
            $this->message->setBody($this->body);
            $this->message->setSubject($this->subject);
            foreach($this->attach as $attach)
            {
                $this->message->attach($attach);
            }
            return $this->mailer->send($this->message);
        } catch (\Exception $e) {
            return false;
        }
    }

    /*
     * Retourne une instance du message
     * @return Swift_Message
     */
    public function getMessage(): Swift_Message
    {
        return $this->message;
    }

    /**
     * Retourne une instance du mailer
     * @return Swift_Mailer
     */
    public function getMailer(): Swift_Mailer
    {
        return $this->mailer;
    }


}
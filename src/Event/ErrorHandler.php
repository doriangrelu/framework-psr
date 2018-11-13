<?php
/**
 * Created by PhpStorm.
 * User: Dorian
 * Date: 17/05/2018
 * Time: 18:52
 */

namespace App\Event;

use App\Framework\Event\SubScriberInterface;
use App\Framework\Exception\Http\HttpException;
use Framework\App;
use GuzzleHttp\Psr7\Response;
use function Http\Response\send;
use InvalidArgumentException;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Whoops\Exception\ErrorException;
use Whoops\Handler\HandlerInterface;
use Whoops\Run;
use Whoops\RunInterface;

class ErrorHandler implements SubScriberInterface
{
    const EXCEPTION_HANDLER = "handleException";
    const ERROR_HANDLER = "handleError";
    const SHUTDOWN_HANDLER = "handleShutdown";

    /**
     * Handles an exception, ultimately generating a Whoops error
     * page.
     *
     * @param  \Throwable $exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function handleException($exception)
    {
        $error = [
            'type' => 'EXCEPTION',
            'data' => $exception,
        ];
        if ($exception instanceof HttpException) {
            $error = [
                'type' => 'HTTP_EXCEPTION',
                'data' => $exception,
            ];
            $response = new Response($exception->getCode(), [], $this->renderError($error));
            \Http\Response\send($response);
            return;
        }
        $this->handleAll($error);
    }

    /**
     * @param $level
     * @param $message
     * @param null $file
     * @param null $line
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function handleError($level, $message, $file = null, $line = null)
    {
        $error = [
            'type' => 'ERROR',
            'data' => [
                'level' => $level,
                'message' => $message,
                'file' => $file,
                'line' => $line,
            ],
        ];
        $this->handleAll($error);
    }

    /**
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function handleShutdown()
    {

    }

    /**
     * @param $error
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function handleAll($error)
    {

        $response = new Response(500, [], $this->renderError($error));
        \Http\Response\send($response);
    }

    /**
     * @param $error
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function renderError($error)
    {
        $loader = new Twig_Loader_Filesystem(TEMPLATE);
        $twig = new Twig_Environment($loader, array(
            'cache' => false,
        ));
        $template = $twig->load('Errors' . DS . 'error.twig');
        return $template->render([
            'error'=>$error
        ]);
    }

    public function register()
    {
        set_error_handler([$this, self::ERROR_HANDLER]);
        set_exception_handler([$this, self::EXCEPTION_HANDLER]);
        register_shutdown_function([$this, self::SHUTDOWN_HANDLER]);
    }

    /**
     * @return array
     */
    public function getEvents(): array
    {
        return [

        ];
    }
}
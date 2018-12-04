<?php

namespace Framework\Middleware;

use Framework\Cookie\CookieInterface;
use Framework\Exception\CsrfInvalidException;
use Framework\Session\SessionInterface;
use Framework\Utility\RequestUtility;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CsrfMiddleware implements MiddlewareInterface
{

    use RequestUtility;

    /**
     * @var CookieInterface
     */
    private $cookie;

    /**
     * @var string
     */
    private $formKey;

    /**
     * @var string
     */
    private $sessionKey;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        SessionInterface $session,
        CookieInterface $cookie,
        int $limit = 50,
        string $formKey = '_csrf',
        string $sessionKey = 'csrf'
    )
    {
        $this->cookie = $cookie;
        $this->validSession($session);
        $this->session = &$session;
        $this->formKey = $formKey;
        $this->sessionKey = $sessionKey;
        $this->limit = $limit;
    }

    /**
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return ResponseInterface
     * @throws CsrfInvalidException
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            if ($this->isAjaxRequest()) {
                if ($this->_processAjaxRequest($request)) {
                    return $delegate->process($request);
                } else {
                    $this->reject();
                }
            }
            $params = $request->getParsedBody() ?: [];
            if (!array_key_exists($this->formKey, $params)) {
                $this->reject();
            } else {
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->formKey], $csrfList)) {
                    $this->useToken($params[$this->formKey]);
                    return $delegate->process($request);
                } else {
                    $this->reject();
                }
            }
        } else {
            return $delegate->process($request);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    private function _processAjaxRequest(ServerRequestInterface $request): bool
    {
        $key = $this->cookie->get($this->sessionKey);
        $request = $request->withHeader('X-CSRF-Token', 'test');
        $xCSRFHeader = $this->_getXCSRFHeader($request);

        if (is_null($xCSRFHeader) || $xCSRFHeader !== $key) {
            return false;
        }
        $this->generateToken();
        return true;
        // X-CSRF-Token
    }

    private function _getXCSRFHeader(ServerRequestInterface $request)
    {
        return $request->getHeader('X-CSRF-Token')[0] ?? null;
    }

    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;
        $this->session[$this->sessionKey] = $csrfList;
        $this->cookie->set($this->sessionKey, $token);
        $this->limitTokens();
        return $token;
    }

    /**
     * @throws CsrfInvalidException
     */
    private function reject(): void
    {
        throw new CsrfInvalidException();
    }

    private function useToken($token): void
    {
        $tokens = array_filter($this->session[$this->sessionKey], function ($t) use ($token) {
            return $token !== $t;
        });
        $this->session[$this->sessionKey] = $tokens;
    }


    private function limitTokens(): void
    {
        $tokens = $this->session[$this->sessionKey] ?? [];
        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        $this->session[$this->sessionKey] = $tokens;
    }

    /**
     * @param $session
     */
    private function validSession($session)
    {
        if (!is_array($session) && !$session instanceof \ArrayAccess) {
            throw new \TypeError('La session passé au middleware CSRF n\'est pas traitable comme un tableau');
        }
    }

    /**
     * @return string
     */
    public function getFormKey(): string
    {
        return $this->formKey;
    }
}

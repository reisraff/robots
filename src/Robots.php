<?php

namespace Middlewares;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Interop\Http\Middleware\MiddlewareInterface;
use Interop\Http\Middleware\DelegateInterface;

class Robots implements MiddlewareInterface
{
    const HEADER = 'X-Robots-Tag';

    /**
     * @var bool
     */
    private $robots = false;

    /**
     * Set whether search engines robots are allowed or not.
     *
     * @param bool $robots
     */
    public function __construct($robots = false)
    {
        $this->robots = $robots;
    }

    /**
     * Process a request and return a response.
     *
     * @param RequestInterface  $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(RequestInterface $request, DelegateInterface $delegate)
    {
        if ($request->getUri()->getPath() === '/robots.txt') {
            $response = Utils\Factory::createResponse();

            if ($this->robots) {
                $response->getBody()->write("User-Agent: *\nAllow: /");
            } else {
                $response->getBody()->write("User-Agent: *\nDisallow: /");
            }

            return $response;
        }

        $response = $delegate->process($request);

        if ($this->robots) {
            return $response->withHeader(self::HEADER, 'index, follow');
        }

        return $response->withHeader(self::HEADER, 'noindex, nofollow, noarchive');
    }
}

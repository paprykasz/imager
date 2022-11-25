<?php

namespace Papryk\Imager;

use Papryk\Imager\Controller\ControllerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Exception;

/**
 * Class Application
 * @package Papryk\Imager
 */
class Application implements LoggerAwareInterface
{
    /**
     *
     */
    const CONTROLLER_NAMESPACE = 'Papryk\Imager\Controller';

    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @var LoggerInterface|NullLogger
     */
    protected LoggerInterface $logger;

    /**
     * Application constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = new NullLogger();
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(): Response
    {
        try {
            $urlParams = $this->matchUrl();

            $this->setQueryParams($urlParams);

            $controller = $this->getController($urlParams['controller']);

            $action = $urlParams['action'];

            if (method_exists($controller, $action)) {
                $response = $controller->$action();
            } else {
                throw new RouteNotFoundException();
            }
        } catch (RouteNotFoundException $routeNotFoundException) {
            $response = new Response($routeNotFoundException->getMessage(), 404);
        } catch (Exception $exception) {
            $this->logger->critical($exception);
            $response = new Response('Something went wrong :(', 500);
        }

        return $response->send();
    }

    /**
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function matchUrl(): array
    {
        return $this->getContainer()->get(UrlMatcher::class)
            ->matchRequest($this->getRequest());
    }

    /**
     * @param string $controller
     * @return ControllerInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getController(string $controller): ControllerInterface
    {
        return $this->getContainer()->get(sprintf('%s\%sController', self::CONTROLLER_NAMESPACE, ucfirst($controller)));
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @param array $queryParams
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function setQueryParams(array $queryParams): void
    {
        $request = $this->getRequest();
        foreach ($queryParams as $queryParamName => $queryParamValue) {
            $request->query->set($queryParamName, $queryParamValue);
        }
    }

    /**
     * @return Request
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getRequest(): Request
    {
        return $this->getContainer()->get(Request::class);
    }
}

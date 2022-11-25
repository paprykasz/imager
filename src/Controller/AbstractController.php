<?php

namespace Papryk\Imager\Controller;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractController
 * @package Papryk\Imager\Controller
 */
abstract class AbstractController implements ControllerInterface
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * AbstractController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return Request
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getRequest(): Request
    {
        return $this->container->get(Request::class);
    }
}

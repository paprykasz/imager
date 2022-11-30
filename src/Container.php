<?php

namespace Papryk\Imager;

use DI\ContainerBuilder;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Papryk\Imager\Controller\ImageController;
use Papryk\Imager\Storage\ImagePrivateStorage;
use Papryk\Imager\Storage\ImagePublicStorage;
use Papryk\Imager\Storage\LogStorage;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Exception;

final class Container
{
    /**
     * @return \DI\Container
     * @throws Exception
     */
    public static function build(): \DI\Container
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(...[
            [
                RouteCollection::class => function (ContainerInterface $container) {
                    $dimensionRequirements = [
                        'height' => '\d+',
                        'width' => '\d+',
                    ];

                    $routes = (new RouteCollection());
                    $routes->add('index', new Route('/', ['controller' => 'image', 'action' => 'index']));
                    $routes->add('original', new Route('/original/{imageName}', ['controller' => 'image', 'action' => 'original']));
                    $routes->add('crop', new Route('/{imageName}/crop/{height}x{width}', ['controller' => 'image', 'action' => 'crop'], $dimensionRequirements));
                    $routes->add('resize', new Route('/{imageName}/resize/{height}x{width}', ['controller' => 'image', 'action' => 'resize'], $dimensionRequirements));
                    return $routes;
                }
            ],
            [
                Request::class => function (ContainerInterface $container) {
                    return Request::createFromGlobals();
                }
            ],
            [
                RequestContext::class => function (ContainerInterface $container) {
                    return (new RequestContext())->fromRequest($container->get(Request::class));
                }
            ],
            [
                UrlGeneratorInterface::class => function (ContainerInterface $container) {
                    return new UrlGenerator($container->get(RouteCollection::class), $container->get(RequestContext::class));
                }
            ],
            [
                UrlMatcher::class => function (ContainerInterface $container) {
                    return new UrlMatcher($container->get(RouteCollection::class), $container->get(RequestContext::class));
                }
            ],
            [
                ImageController::class => function (ContainerInterface $container) {
                    return (new ImageController($container));
                }
            ],
            [
                ImagePrivateStorage::class => function (ContainerInterface $container) {
                    return new ImagePrivateStorage();
                }
            ],
            [
                ImagePublicStorage::class => function (ContainerInterface $container) {
                    return new ImagePublicStorage();
                }
            ],
            [
                LogStorage::class => function (ContainerInterface $container) {
                    return new LogStorage();
                }
            ],
            [
                LoggerInterface::class => function (ContainerInterface $container) {
                    $logStorage = $container->get(LogStorage::class);
                    $logger = new Logger('log');
                    $logger->pushHandler(new RotatingFileHandler(sprintf('%s/app.log', $logStorage->getStoragePath()), 5));
                    return $logger;
                }
            ]
        ]);

        return $containerBuilder->build();
    }
}

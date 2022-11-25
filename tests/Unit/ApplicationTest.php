<?php

namespace Papryk\Imager\Tests\Unit;

use Jtl\UnitTest\TestCase;
use Papryk\Imager\Application;
use Papryk\Imager\Controller\ControllerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationTest extends TestCase
{
    /**
     * @covers \Papryk\Imager\Application::__construct
     */
    public function testCanBeInitialized(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)->getMock();
        $application = new Application($container);

        $this->assertInstanceOf(Application::class, $application);
    }

    /**
     * @covers \Papryk\Imager\Application::run
     */
    public function testRunValidController(): void
    {
        $testController = $this->createTestController();

        $application = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['matchUrl', 'setQueryParams', 'getController'])
            ->getMock();

        $application->expects($this->once())
            ->method('matchUrl')
            ->willReturn(['action' => 'index', 'controller' => 'Foo']);

        $application->expects($this->once())
            ->method('getController')
            ->willReturn($testController);

        $application->run();
        $this->expectOutputString('test');
    }

    /**
     * @covers \Papryk\Imager\Application::run
     */
    public function testRunInvalidController(): void
    {
        $application = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['matchUrl', 'setQueryParams', 'getController'])
            ->getMock();

        $application->expects($this->once())
            ->method('matchUrl')
            ->willReturn(['action' => 'foo', 'controller' => 'Foo']);

        $application->expects($this->once())
            ->method('getController')
            ->willReturn($this->createTestController());

        /** @var Response $result */
        $result = $application->run();
        $this->assertEquals(404, $this->retrievePropertyValue($result, 'statusCode'));
    }

    /**
     * @covers \Papryk\Imager\Application::run
     */
    public function testRunExceptionOccurred(): void
    {
        $application = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['matchUrl', 'setQueryParams', 'getController'])
            ->getMock();

        $application->setLogger(new NullLogger());

        /** @var Response $result */
        $result = $application->run();
        $this->expectOutputString('Something went wrong :(');
        $this->assertEquals(500, $this->retrievePropertyValue($result, 'statusCode'));
    }

    /**
     * @covers \Papryk\Imager\Application
     */
    public function testHasRequiredInterfaces(): void
    {
        $interfaces = class_implements(Application::class);

        $this->assertTrue(isset($interfaces[LoggerAwareInterface::class]));
    }

    /**
     * @covers \Papryk\Imager\Application::setQueryParams
     */
    public function testSetQueryParams(): void
    {
        $params = ['test' => 'foo'];

        $application = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getRequest'])
            ->getMock();

        $request = new Request();

        $application->expects($this->once())->method('getRequest')->willReturn($request);

        $this->invokeMethod($application, 'setQueryParams', $params);

        $this->assertSame('foo', $request->get('test'));
    }

    /**
     * @covers \Papryk\Imager\Application::getController
     */
    public function testGetController(): void
    {
        $controllerName = "Foo";

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->onlyMethods(['get', 'has'])
            ->getMock();

        $container
            ->expects($this->once())
            ->method('get')
            ->with(sprintf('%s\%s%s', Application::CONTROLLER_NAMESPACE, $controllerName, 'Controller'))
            ->willReturn($this->createTestController());

        $application = new Application($container);
        $this->invokeMethod($application, 'getController', $controllerName);
    }

    protected function createTestController(): ControllerInterface
    {
        return new class() implements ControllerInterface {
            public function getRequest(): Request
            {
            }

            public function index(): Response
            {
                return new Response('test');
            }
        };
    }
}

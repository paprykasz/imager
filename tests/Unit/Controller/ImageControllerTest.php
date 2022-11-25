<?php

namespace Papryk\Imager\Tests\Unit\Controller;

use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Jtl\UnitTest\TestCase;
use Papryk\Imager\Controller\ImageController;
use Papryk\Imager\Image\ImageHandler;
use Papryk\Imager\Image\ImageHandlerInterface;
use Papryk\Imager\Storage\ImagePublicStorage;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;

class ImageControllerTest extends TestCase
{
    /**
     * @covers IndexController::index
     */
    public function testIndex(): void
    {
        $imageController = $this->getImageControllerMock(['getUrlGenerator']);

        $urlGenerator = $this->getMockBuilder(UrlGenerator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generate'])
            ->getMock();

        $urlGenerator->expects($this->exactly(4))
            ->method('generate')
            ->willReturn('foo');

        $imageController->expects($this->once())
            ->method('getUrlGenerator')
            ->willReturn($urlGenerator);

        $response = $imageController->index();
        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @covers ImageController::original
     */
    public function testOriginalFileNotFound(): void
    {
        $imageController = $this->getImageControllerMock(['getRequest', 'getImageSourcePath', 'getStorage']);

        $request = $this->getMockBuilder(Request::class)
            ->onlyMethods(['get'])
            ->getMock();

        $request->expects($this->once())
            ->method('get')
            ->willReturn('Foo.jpg');

        $imageController->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $this->getMockBuilder(\SplFileInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->expectExceptionObject(new FileNotFoundException(''));

        $imageController->original();
    }

    /**
     * @covers ImageController::original
     */
    public function testOriginal(): void
    {
        $imageController = $this->getImageControllerMock(['getRequest', 'getImageSourcePath', 'getStorage']);

        $request = $this->getMockBuilder(Request::class)
            ->onlyMethods(['get'])
            ->getMock();

        $request->expects($this->once())
            ->method('get')
            ->willReturn('file.txt');

        $imageController->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $imageController->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $imageController->expects($this->once())
            ->method('getImageSourcePath')
            ->willReturn(dirname(dirname(__DIR__)) . '/Fixtures/file.txt');

        $this->getMockBuilder(\SplFileInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $imageController->original();
    }

    /**
     * @covers ImageController::createRedirectResponse
     */
    public function testCreateRedirectResponse(): void
    {
        $imageController = $this->getImageControllerMock([]);
        $redirectResponse = $this->invokeMethod($imageController, 'createRedirectResponse', 'foo.jpg');
        $this->assertInstanceOf(RedirectResponse::class, $redirectResponse);
    }

    /**
     * @covers ImageController::getImageHandler()
     */
    public function testGetImageHandler(): void
    {
        $imageController = $this->getImageControllerMock(['getImageSourcePath', 'getStorage', 'getImageManager']);

        $imageController->expects($this->once())
            ->method('getImageSourcePath');

        $imageController->expects($this->once())
            ->method('getStorage');

        $imageManager = $this->getMockBuilder(ImageManager::class)
            ->onlyMethods(['make'])
            ->disableOriginalConstructor()
            ->getMock();

        $imageManager->expects($this->once())
            ->method('make')
            ->willReturn(new Image());

        $imageController->expects($this->once())
            ->method('getImageManager')
            ->willReturn($imageManager);

        $imageHandler = $this->invokeMethod($imageController, 'getImageHandler', 'foo.jpg');
        $this->assertInstanceOf(ImageHandlerInterface::class, $imageHandler);
    }

    /**
     * @covers ImageController::crop
     */
    public function testCrop(): void
    {
        $this->manipulatorActionTest('crop');
    }

    /**
     * @covers ImageController::resize
     */
    public function testResize(): void
    {
        $this->manipulatorActionTest('resize');
    }

    protected function manipulatorActionTest($method)
    {
        $imageController = $this->getImageControllerMock(['getRequest', 'getImageHandler', 'getImagePublicStorage', 'createRedirectResponse']);

        $request = $this->getMockBuilder(Request::class)
            ->onlyMethods(['get'])
            ->getMock();

        $request->expects($this->exactly(3))
            ->method('get')
            ->willReturnOnConsecutiveCalls(...['foo.jpg', 100, 200]);

        $imageController->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $imageHandler = $this->getMockBuilder(ImageHandler::class)
            ->onlyMethods(['getFilename', 'getExtension', $method, 'save'])
            ->disableOriginalConstructor()
            ->getMock();

        $imageHandler->expects($this->once())
            ->method($method);

        $imageHandler->expects($this->once())
            ->method('save');

        $imageController->expects($this->once())
            ->method('getImageHandler')
            ->willReturn($imageHandler);

        $imageController->expects($this->once())
            ->method('createRedirectResponse');

        $imageController->expects($this->once())
            ->method('getImagePublicStorage');

        $response = $imageController->$method();
        $this->assertInstanceOf(Response::class, $response);
    }

    protected function getImageControllerMock(array $onlyMethods)
    {
        return $this->getMockBuilder(ImageController::class)
            ->disableOriginalConstructor()
            ->onlyMethods($onlyMethods)
            ->getMock();
    }
}

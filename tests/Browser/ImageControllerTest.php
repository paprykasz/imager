<?php

namespace Papryk\Imager\Tests\Browser;

use Facebook\WebDriver\Exception\UnknownErrorException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverBy;
use Jtl\UnitTest\TestCase;
use Papryk\Imager\Container;
use Papryk\Imager\Controller\ImageController;
use Papryk\Imager\Storage\ImagePublicStorage;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ImageControllerTest extends TestCase
{
    protected RemoteWebDriver $webDriver;
    protected ContainerInterface $container;

    protected function setUp(): void
    {
        $chrome = DesiredCapabilities::chrome();
        $chrome->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        $chrome->setCapability('acceptInsecureCerts', true);

        $this->container = Container::build();
        $this->webDriver = RemoteWebDriver::create($_ENV['SELENIUM_SERVER_URL'], $chrome);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->webDriver->quit();
    }

    /**
     * @covers \Papryk\Imager\Controller\ImageController::index
     * @throws UnknownErrorException
     */
    public function testDummyGeneratedImages(): void
    {
        $page = $this->webDriver->get($this->getAppUrl());

        $cropped = $page->findElement(WebDriverBy::id(ImageController::CROP_LANDSCAPE_IMAGE_ID));
        $croppedImageSrc = $cropped->getAttribute('src');

        $resized = $page->findElement(WebDriverBy::id(ImageController::RESIZE_PLANE_IMAGE_ID));
        $resizedImageSrc = $resized->getAttribute('src');

        $croppedImageSize = getimagesize(sprintf('%s%s', $this->getAppUrl(), $croppedImageSrc));
        $resizedImageSize = getimagesize(sprintf('%s%s', $this->getAppUrl(), $resizedImageSrc));

        $this->assertEquals(1000, $croppedImageSize[0]);
        $this->assertEquals(1000, $croppedImageSize[1]);

        $this->assertEquals(1000, $resizedImageSize[0]);
        $this->assertEquals(1000, $resizedImageSize[1]);
    }

    /**
     * @dataProvider manipulateDataProvider
     * @covers       ImageController::crop
     * @covers       ImageController::resize
     * @param int $width
     * @param int $height
     * @param string $action
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testManipulateFunction(int $width, int $height, string $action): void
    {
        $url = $this->container->get(UrlGeneratorInterface::class)->generate($action, ['imageName' => 'johann-siemens-EPy0gBJzzZU-unsplash.jpg', 'width' => $width, 'height' => $height]);
        $croppedImageSize = getimagesize(sprintf('%s%s', $this->getAppUrl(), $url));

        $actionShort = substr($action, 0, 1);

        $this->assertEquals($width, $croppedImageSize[0]);
        $this->assertEquals($height, $croppedImageSize[1]);
        $this->assertFileExists(sprintf('%s/johann-siemens-EPy0gBJzzZU-unsplash-%s%sx%s.jpg', $this->container->get(ImagePublicStorage::class)->getStoragePath(), $actionShort, $width, $height));
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function manipulateDataProvider(): array
    {
        return [
            [random_int(100, 1500), random_int(100, 1500), 'crop'],
            [random_int(100, 1500), random_int(100, 1500), 'resize'],
        ];
    }

    /**
     * @covers \Papryk\Imager\Controller\ImageController::crop
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testResizeFunction(): void
    {
        $width = random_int(100, 1500);
        $height = random_int(100, 1500);

        $url = $this->container->get(UrlGeneratorInterface::class)->generate('resize', ['imageName' => 'johann-siemens-EPy0gBJzzZU-unsplash.jpg', 'width' => $width, 'height' => $height]);
        $resizedImageSize = getimagesize(sprintf('%s%s', $this->getAppUrl(), $url));

        $this->assertEquals($width, $resizedImageSize[0]);
        $this->assertEquals($height, $resizedImageSize[1]);
        $this->assertFileExists(sprintf('%s/johann-siemens-EPy0gBJzzZU-unsplash-r%sx%s.jpg', $this->container->get(ImagePublicStorage::class)->getStoragePath(), $width, $height));
    }

    /**
     * @return string
     */
    protected function getAppUrl(): string
    {
        return $_ENV['APP_URL'];
    }
}

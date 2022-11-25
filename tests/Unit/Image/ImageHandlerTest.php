<?php


namespace Papryk\Imager\Tests\Unit\Image;

use Intervention\Image\Image;
use Jtl\UnitTest\TestCase;
use Papryk\Imager\Image\CropInterface;
use Papryk\Imager\Image\ImageHandler;
use Papryk\Imager\Image\ImageHandlerInterface;
use Papryk\Imager\Image\ResizeInterface;
use Papryk\Imager\Storage\SaveInterface;
use Papryk\Imager\Storage\StorageInterface;

class ImageHandlerTest extends TestCase
{
    /**
     * @covers ImageHandler::__construct
     */
    public function testHasRequiredInterfaces(): void
    {
        $interfaces = [
            CropInterface::class,
            ResizeInterface::class,
            SaveInterface::class,
            ImageHandlerInterface::class
        ];

        $this->assertEqualsCanonicalizing($interfaces, class_implements(ImageHandler::class));
    }

    /**
     * @covers ImageHandler::crop
     */
    public function testCrop(): void
    {
        $image = $this->createImageMock();
        $image->expects($this->once())
            ->method('crop');

        $imageHandler = new ImageHandler($image);

        $imageHandler->crop(100, 200);
    }

    /**
     * @covers ImageHandler::crop
     */
    public function testResize(): void
    {
        $image = $this->createImageMock();
        $image->expects($this->once())
            ->method('resize');

        $imageHandler = new ImageHandler($image);

        $imageHandler->resize(100, 200);
    }

    /**
     * @covers ImageHandler::getFilename
     */
    public function testGetFilename(): void
    {
        $image = $this->createImageMock();
        $this->setProperty($image, 'filename', 'foo');

        $imageHandler = new ImageHandler($image);

        $this->assertSame('foo', $imageHandler->getFilename());
    }

    /**
     * @covers ImageHandler::getExtension
     */
    public function testGetExtension(): void
    {
        $image = $this->createImageMock();
        $this->setProperty($image, 'extension', 'jpg');

        $imageHandler = new ImageHandler($image);

        $this->assertSame('jpg', $imageHandler->getExtension());
    }

    /**
     * @covers ImageHandler::save
     */
    public function testSave(): void
    {
        $image = $this->createImageMock();
        $image->expects($this->once())
            ->method('save');

        $storageInterface = $this->getMockBuilder(StorageInterface::class)
            ->getMock();

        $imageHandler = new ImageHandler($image);
        $imageHandler->save($storageInterface, 'foo.jpg');
    }

    protected function createImageMock()
    {
        return $this->getMockBuilder(Image::class)
            ->addMethods(['crop', 'resize'])
            ->onlyMethods(['save'])
            ->getMock();
    }
}

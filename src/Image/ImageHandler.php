<?php

namespace Papryk\Imager\Image;

use Intervention\Image\Image;
use Papryk\Imager\Storage\StorageInterface;

/**
 * Class ImageHandler
 * @package Papryk\Imager\Image
 */
class ImageHandler implements ImageHandlerInterface
{
    /**
     * @var Image
     */
    protected Image $image;

    /**
     * ImageHandler constructor.
     * @param Image $image
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    /**
     * @param int $width
     * @param int $height
     */
    public function crop(int $width, int $height): void
    {
        $this->image->crop($width, $height);
    }

    /**
     * @param int|null $width
     * @param int|null $height
     */
    public function resize(int $width = null, int $height = null): void
    {
        $this->image->resize($width, $height);
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->image->filename;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->image->extension;
    }

    /**
     * @param StorageInterface $storage
     * @param string $fileName
     */
    public function save(StorageInterface $storage, string $fileName): void
    {
        $this->getImage()->save(sprintf('%s/%s', $storage->getStoragePath(), $fileName));
    }

    /**
     * @return Image
     */
    protected function getImage(): Image
    {
        return $this->image;
    }
}

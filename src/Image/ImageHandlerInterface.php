<?php


namespace Papryk\Imager\Image;

use Papryk\Imager\Storage\SaveInterface;

/**
 * Interface ImageHandlerInterface
 * @package Papryk\Imager\Image
 */
interface ImageHandlerInterface extends CropInterface, ResizeInterface, SaveInterface
{
    /**
     * @return string
     */
    public function getFilename(): string;

    /**
     * @return string
     */
    public function getExtension(): string;
}

<?php

namespace Papryk\Imager\Image;

/**
 * Interface CropInterface
 * @package Papryk\Imager\Image
 */
interface CropInterface
{
    /**
     * @param int $width
     * @param int $height
     */
    public function crop(int $width, int $height): void;
}

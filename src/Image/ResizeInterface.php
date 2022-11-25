<?php

namespace Papryk\Imager\Image;

/**
 * Interface ResizeInterface
 * @package Papryk\Imager\Image
 */
interface ResizeInterface
{
    /**
     * @param int|null $width
     * @param int|null $height
     */
    public function resize(int $width = null, int $height = null): void;
}

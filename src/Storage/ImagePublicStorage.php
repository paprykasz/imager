<?php

namespace Papryk\Imager\Storage;

/**
 * Class ImagePublicStorage
 * @package Papryk\Imager\Storage
 */
class ImagePublicStorage implements StorageInterface
{
    /**
     * @return string
     */
    public function getStoragePath(): string
    {
        return sprintf('%s/%s', dirname(dirname(__DIR__)), 'public');
    }
}

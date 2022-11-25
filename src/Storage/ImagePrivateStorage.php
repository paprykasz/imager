<?php

namespace Papryk\Imager\Storage;

/**
 * Class ImagePrivateStorage
 * @package Papryk\Imager\Storage
 */
class ImagePrivateStorage implements StorageInterface
{
    /**
     * @return string
     */
    public function getStoragePath(): string
    {
        return sprintf('%s/%s', dirname(dirname(__DIR__)), 'storage/img');
    }
}

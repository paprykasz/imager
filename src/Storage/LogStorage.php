<?php

namespace Papryk\Imager\Storage;

/**
 * Class LogStorage
 * @package Papryk\Imager\Storage
 */
class LogStorage implements StorageInterface
{
    /**
     * @return string
     */
    public function getStoragePath(): string
    {
        return sprintf('%s/%s', dirname(dirname(__DIR__)), 'storage/logs');
    }
}

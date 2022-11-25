<?php

namespace Papryk\Imager\Storage;

/**
 * Interface StorageInterface
 * @package Papryk\Imager\Storage
 */
interface StorageInterface
{
    /**
     * @return string
     */
    public function getStoragePath(): string;
}

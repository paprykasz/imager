<?php

namespace Papryk\Imager\Storage;

/**
 * Interface SaveInterface
 * @package Papryk\Imager\Storage
 */
interface SaveInterface
{
    /**
     * @param StorageInterface $storage
     * @param string $fileName
     */
    public function save(StorageInterface $storage, string $fileName): void;
}

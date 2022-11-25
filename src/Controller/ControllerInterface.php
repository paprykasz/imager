<?php

namespace Papryk\Imager\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ControllerInterface
 * @package Papryk\Imager\Controller
 */
interface ControllerInterface
{
    /**
     * @return Request
     */
    public function getRequest(): Request;
}

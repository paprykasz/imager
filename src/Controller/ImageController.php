<?php

namespace Papryk\Imager\Controller;

use Intervention\Image\ImageManager;
use Papryk\Imager\Image\ImageHandler;
use Papryk\Imager\Image\ImageHandlerInterface;
use Papryk\Imager\Storage\ImagePublicStorage;
use Papryk\Imager\Storage\ImagePrivateStorage;
use Papryk\Imager\Storage\StorageInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use SplFileInfo;

/**
 * Class ImageController
 * @package Papryk\Imager\Controller
 */
class ImageController extends AbstractController
{
    /**
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): Response
    {
        /** @var UrlGenerator $generator */
        $generator = $this->getUrlGenerator();

        $images = [
            $generator->generate('original', ['imageName' => 'johann-siemens-EPy0gBJzzZU-unsplash.jpg']),
            $generator->generate('crop', ['imageName' => 'johann-siemens-EPy0gBJzzZU-unsplash.jpg', 'width' => 1000, 'height' => 1000]),
            $generator->generate('original', ['imageName' => 'john-mcarthur-8KLLgqHMAv4-unsplash.jpg']),
            $generator->generate('resize', ['imageName' => 'john-mcarthur-8KLLgqHMAv4-unsplash.jpg', 'width' => 1000, 'height' => 1000]),
        ];

        /**
         * I could use Twig or Blade but it's only for presentation purpose
         */
        $imageTemplate = [];
        foreach ($images as $image) {
            $imageTemplate[] = sprintf("<img style='height: 40%%' src='%s' />", $image);
        }

        return new Response(sprintf('<html><body>%s</body></html>', join('', $imageTemplate)));
    }

    /**
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function crop(): Response
    {
        return $this->manipulate('crop', $this->getRequest());
    }

    /**
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function resize(): Response
    {
        return $this->manipulate('resize', $this->getRequest());
    }

    /**
     * @param string $action
     * @param Request $request
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function manipulate(string $action, Request $request): Response
    {
        $imageNameWithExt = $request->get('imageName');
        $height = $request->get('height');
        $width = $request->get('width');

        $imageHandler = $this->getImageHandler($imageNameWithExt);

        $actionShort = substr($action, 0, 1);

        $newImageFilename = sprintf('%s-%s%sx%s.%s', $imageHandler->getFilename(), $actionShort, $width, $height, $imageHandler->getExtension());

        if (!file_exists($newImageFilename)) {
            $imageHandler->$action($width, $height);
            $imageHandler->save($this->getImagePublicStorage(), $newImageFilename);
        }

        return $this->createRedirectResponse($newImageFilename);
    }

    /**
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function original(): Response
    {
        $imageName = $this->getRequest()->get('imageName');

        $image = new SplFileInfo($this->getImageSourcePath($this->getStorage(), $imageName));

        return new BinaryFileResponse($image);
    }

    /**
     * @param string $newImageFilename
     * @return RedirectResponse
     */
    protected function createRedirectResponse(string $newImageFilename): RedirectResponse
    {
        return new RedirectResponse(sprintf('/%s', $newImageFilename));
    }

    /**
     * @param string $imageNameWithExt
     * @return ImageHandlerInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getImageHandler(string $imageNameWithExt): ImageHandlerInterface
    {
        $sourceImagePath = $this->getImageSourcePath($this->getStorage(), $imageNameWithExt);

        $imageManager = $this->getImageManager();

        $image = $imageManager->make($sourceImagePath);
        return new ImageHandler($image);
    }

    /**
     * @param StorageInterface $storage
     * @param string $imageNameWithExt
     * @return string
     */
    protected function getImageSourcePath(StorageInterface $storage, string $imageNameWithExt): string
    {
        return $sourceImagePath = sprintf('%s/%s', $storage->getStoragePath(), $imageNameWithExt);
    }

    /**
     * @return ImageManager
     */
    protected function getImageManager(): ImageManager
    {
        return new ImageManager(['driver' => 'gd']);
    }

    /**
     * @return StorageInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getStorage(): StorageInterface
    {
        return $this->container->get(ImagePrivateStorage::class);
    }

    /**
     * @return StorageInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getImagePublicStorage(): StorageInterface
    {
        return $this->container->get(ImagePublicStorage::class);
    }

    /**
     * @return UrlGenerator
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getUrlGenerator(): UrlGenerator
    {
        return $this->container->get(UrlGenerator::class);
    }
}

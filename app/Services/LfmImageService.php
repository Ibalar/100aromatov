<?php

namespace App\Services;

use BadMethodCallException;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use UniSharp\LaravelFilemanager\Services\ImageService as BaseImageService;

class LfmImageService extends BaseImageService
{
    public function __construct(ImageManagerInterface $imageManager)
    {
        parent::__construct($imageManager);
    }

    /**
     * Backward compatibility for LFM calls to read() on Intervention Image v4.
     */
    public function read(mixed $source): ImageInterface
    {
        if (method_exists($this->imageManager, 'read')) {
            return $this->imageManager->read($source);
        }

        if (method_exists($this->imageManager, 'decode')) {
            return $this->imageManager->decode($source);
        }

        if (method_exists($this->imageManager, 'make')) {
            return $this->imageManager->make($source);
        }

        throw new BadMethodCallException('Neither read(), decode(), nor make() exists on ImageManagerInterface.');
    }
}

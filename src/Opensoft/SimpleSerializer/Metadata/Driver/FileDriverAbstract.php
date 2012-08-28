<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Metadata\Driver;

use Opensoft\SimpleSerializer\Metadata\ClassMetadata;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
abstract class FileDriverAbstract implements DriverInterface
{
    private $locator;

    /**
     * @param FileLocatorInterface $locator
     */
    public function __construct(FileLocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param string $className
     * @return null|ClassMetadata
     */
    public function loadMetadataForClass($className)
    {
        if (null === $path = $this->locator->findFileForClass($className, $this->getExtension())) {
            return null;
        }

        return $this->loadMetadataFromFile($className, $path);
    }

    /**
     * Parses the content of the file, and converts it to the desired metadata.
     *
     * @param string $className
     * @param string $file
     * @return ClassMetadata|null
     */
    abstract protected function loadMetadataFromFile($className, $file);

    /**
     * Returns the extension of the file.
     *
     * @return string
     */
    abstract protected function getExtension();
}

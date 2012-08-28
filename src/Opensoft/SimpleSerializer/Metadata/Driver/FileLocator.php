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

/**
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class FileLocator implements FileLocatorInterface
{
    /**
     * @var array
     */
    private $dirs;

    /**
     * @param array $dirs
     */
    public function __construct(array $dirs)
    {
        $this->dirs = $dirs;
    }

    /**
     * @param string $className
     * @param string $extension
     * @return null|string
     */
    public function findFileForClass($className, $extension)
    {
        foreach ($this->dirs as $prefix => $dir) {
            if (0 !== strpos($className, $prefix)) {
                continue;
            }

            $path = $dir . '/' . str_replace('\\', '.', substr($className, strlen($prefix)+1)) . '.' . $extension;
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}

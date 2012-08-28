<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Metadata\Cache;

use Opensoft\SimpleSerializer\Metadata\ClassMetadata;
use Opensoft\SimpleSerializer\Exception\InvalidArgumentException;

/**
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class FileCache implements CacheInterface
{
    /**
     * @var string
     */
    private $dir;

    public function __construct($dir)
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" does not exist.', $dir));
        }
        if (!is_writable($dir)) {
            throw new InvalidArgumentException(sprintf('The directory "%s" is not writable.', $dir));
        }

        $this->dir = rtrim($dir, '\\/');
    }

    /**
     * @param string $className
     * @return ClassMetadata|mixed|null
     */
    public function loadClassMetadataFromCache($className)
    {
        $path = $this->dir . '/' . strtr($className, '\\', '-') . '.cache.php';
        if (!file_exists($path)) {
            return null;
        }

        return include $path;
    }

    /**
     * @param ClassMetadata $metadata
     */
    public function putClassMetadataInCache(ClassMetadata $metadata)
    {
        $path = $this->dir . '/' . strtr($metadata->getName(), '\\', '-') . '.cache.php';
        file_put_contents($path, '<?php return unserialize(' . var_export(serialize($metadata), true) . ');');
    }

    /**
     * @param string $className
     */
    public function removeClassMetadataFromCache($className)
    {
        $path = $this->dir . '/' . strtr($className, '\\', '-') . '.cache.php';
        if (file_exists($path)) {
            unlink($path);
        }
    }
}

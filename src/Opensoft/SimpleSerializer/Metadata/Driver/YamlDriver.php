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

use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;
use Opensoft\SimpleSerializer\Metadata\ClassMetadata;
use Opensoft\SimpleSerializer\Exception\RuntimeException;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class YamlDriver extends FileDriverAbstract
{
    protected function loadMetadataFromFile($className, $file)
    {
        $config = Yaml::parse(file_get_contents($file));

        if (!isset($config[$name = $className])) {
            throw new RuntimeException(sprintf('Expected metadata for class %s to be defined in %s.', $className, $file));
        }

        $config = $config[$name];
        $metadata = new ClassMetadata($name);
        $metadata->addFileResource($file);
        foreach ($config['properties'] as $propertyName => $propertyOptions) {
            $pMetadata = new PropertyMetadata($propertyName);
                if (isset($propertyOptions['expose'])) {
                    $pMetadata->setExpose((boolean) $propertyOptions['expose']);
                }

                if (isset($propertyOptions['serialized_name'])) {
                    $pMetadata->setSerializedName((string) $propertyOptions['serialized_name']);
                }

                if (isset($propertyOptions['type'])) {
                    $pMetadata->setType((string) $propertyOptions['type']);
                }
            $metadata->addPropertyMetadata($pMetadata);
        }

        return $metadata;
    }

    protected function getExtension()
    {
        return 'yml';
    }
}

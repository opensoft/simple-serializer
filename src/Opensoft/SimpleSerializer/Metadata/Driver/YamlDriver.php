<?php

/**
 * This file was originally received from JMSSerializerBundle; since then,
 * it has been modified, and does only in parts resemble the original source code.
 *
 * To the original source code, the following license applies:
 * Copyright 2011 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * To all other portions of code, the following license applies:
 *
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
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
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

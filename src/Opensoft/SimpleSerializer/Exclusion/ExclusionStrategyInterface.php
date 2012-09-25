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

namespace Opensoft\SimpleSerializer\Exclusion;

use Opensoft\SimpleSerializer\Metadata\ClassMetadata;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;

/**
 * Interface for exclusion strategies.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
interface ExclusionStrategyInterface
{
    /**
     * Whether the class should be skipped.
     *
     * @param ClassMetadata $metadata
     * @return boolean
     */
    function shouldSkipClass(ClassMetadata $metadata);

    /**
     * Whether the property should be skipped.
     *
     * @param PropertyMetadata $property
     * @return boolean
     */
    function shouldSkipProperty(PropertyMetadata $property);
}

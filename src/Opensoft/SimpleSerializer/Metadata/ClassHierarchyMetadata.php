<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Metadata;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class ClassHierarchyMetadata extends ClassMetadata
{
    public function __construct()
    {
        parent::__construct('default');
    }

    /**
     * @param ClassMetadata $metadata
     */
    public function addClassMetadata(ClassMetadata $metadata)
    {
        $this->name = $metadata->getName();
        $this->properties = array_merge($this->properties, $metadata->getProperties());
        $this->fileResources = array_merge($this->fileResources, $metadata->getFileResources());
        if ($metadata->getCreatedAt() < $this->createdAt) {
            $this->createdAt = $metadata->getCreatedAt();
        }
    }
}

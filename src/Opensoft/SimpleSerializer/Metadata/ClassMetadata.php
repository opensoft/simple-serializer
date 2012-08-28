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

use Serializable;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class ClassMetadata implements Serializable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array|string[]
     */
    protected $fileResources = array();

    /**
     * @var array|PropertyMetadata[]
     */
    protected $properties = array();

    /**
     * @var integer
     */
    protected $createdAt;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = (string) $name;
        $this->createdAt = time();
    }

    /**
     * @param PropertyMetadata $metadata
     */
    public function addPropertyMetadata(PropertyMetadata $metadata)
    {
        $this->properties[$metadata->getName()] = $metadata;
    }

    /**
     * @return array|PropertyMetadata[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return integer
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return array|string[]
     */
    public function getFileResources()
    {
        return $this->fileResources;
    }

    /**
     * @param string $fileResource
     * @return ClassMetadata
     */
    public function addFileResource($fileResource)
    {
        $this->fileResources[] = $fileResource;

        return $this;
    }

    /**
     * @param null|integer $timestamp
     * @return bool
     */
    public function isFresh($timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = $this->createdAt;
        }

        foreach ($this->fileResources as $filepath) {
            if (!file_exists($filepath)) {
                return false;
            }

            if ($timestamp < filemtime($filepath)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->name,
            $this->properties,
            $this->fileResources,
            $this->createdAt,
        ));
    }

    /**
     * @param string $str
     * @return ClassMetadata
     */
    public function unserialize($str)
    {
        list(
            $this->name,
            $this->properties,
            $this->fileResources,
            $this->createdAt
            ) = unserialize($str);

        return $this;
    }
}

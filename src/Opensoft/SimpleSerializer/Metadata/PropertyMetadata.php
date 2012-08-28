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
class PropertyMetadata implements Serializable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string|null
     */
    protected $serializedName;

    /**
     * @var bool
     */
    protected $expose = false;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = (string) $name;
        $this->serializedName = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $expose
     * @return PropertyMetadata
     */
    public function setExpose($expose)
    {
        $this->expose = ($expose === true) ? true : false;

        return $this;
    }

    /**
     * @return bool
     */
    public function isExpose()
    {
        return $this->expose ? true : false;
    }

    /**
     * @param $serializedName
     * @return PropertyMetadata
     */
    public function setSerializedName($serializedName)
    {
        $this->serializedName = $serializedName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSerializedName()
    {
        return $this->serializedName;
    }

    /**
     * @param $type
     * @return PropertyMetadata
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->name,
            $this->type,
            $this->serializedName,
            $this->expose
        ));
    }

    /**
     * @param string $str
     * @return PropertyMetadata
     */
    public function unserialize($str)
    {
        list($this->name, $this->type, $this->serializedName, $this->expose) = unserialize($str);

        return $this;
    }
}

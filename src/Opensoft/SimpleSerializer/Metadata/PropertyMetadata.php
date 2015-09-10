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
     * @var array
     */
    protected $groups = array();

    /**
     * @var string
     */
    protected $sinceVersion;

    /**
     * @var string
     */
    protected $untilVersion;

    /**
     * @var bool
     */
    protected $nullSkipped;

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
     * @param array $groups
     * @return PropertyMetadata
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param string $sinceVersion
     * @return PropertyMetadata
     */
    public function setSinceVersion($sinceVersion)
    {
        $this->sinceVersion = $sinceVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getSinceVersion()
    {
        return $this->sinceVersion;
    }

    /**
     * @param string $untilVersion
     * @return PropertyMetadata
     */
    public function setUntilVersion($untilVersion)
    {
        $this->untilVersion = $untilVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getUntilVersion()
    {
        return $this->untilVersion;
    }

    /**
     * @param bool $nullSkipped
     */
    public function setNullSkipped($nullSkipped)
    {
        $this->nullSkipped = ($nullSkipped === true) ? true : false;
    }

    /**
     * @return bool
     */
    public function isNullSkipped()
    {
        return $this->nullSkipped;
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
            $this->expose,
            $this->groups,
            $this->sinceVersion,
            $this->untilVersion,
            $this->nullSkipped
        ));
    }

    /**
     * @param string $str
     * @return PropertyMetadata
     */
    public function unserialize($str)
    {
        list($this->name, $this->type, $this->serializedName, $this->expose, $this->groups, $this->sinceVersion, $this->untilVersion, $this->nullSkipped) = unserialize($str);

        return $this;
    }
}

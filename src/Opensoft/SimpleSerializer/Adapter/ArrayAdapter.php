<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Adapter;

use Opensoft\SimpleSerializer\Adapter\ArrayAdapterInterface as BaseArrayAdapter;
use Opensoft\SimpleSerializer\Exception\InvalidArgumentException;
use Opensoft\SimpleSerializer\Exception\RecursionException;
use Opensoft\SimpleSerializer\Metadata\MetadataFactory;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;
use Opensoft\SimpleSerializer\Exclusion\ExclusionStrategyInterface;
use Opensoft\SimpleSerializer\Exclusion\PropertySkipper;
use DateTime;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class ArrayAdapter implements BaseArrayAdapter
{
    const DIRECTION_SERIALIZE = 1;
    const DIRECTION_UNSERIALIZE = 0;
    const STRICT_MODE = 2;
    const MEDIUM_STRICT_MODE = 1;
    const NON_STRICT_MODE = 0;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * @var PropertySkipper
     */
    private $propertySkipper;

    /**
     * @var integer
     */
    private $unserializeMode;

    /**
     * @param MetadataFactory $metadataFactory
     * @param int $unserializeMode
     */
    public function __construct(MetadataFactory $metadataFactory, $unserializeMode = self::NON_STRICT_MODE)
    {
        $this->metadataFactory = $metadataFactory;
        $this->propertySkipper = new PropertySkipper();
        $this->setUnserializeMode($unserializeMode);
    }

    /**
     * {@inheritDoc}
     *
     * @param object $object
     * @return mixed
     */
    public function toArray($object)
    {
        $result = array();
        $className = $this->getFullClassName($object);
        $metadata = $this->metadataFactory->getMetadataForClass($className);
        foreach ($metadata->getProperties() as $property) {
            if (!$property->isExpose() || $this->propertySkipper->shouldSkip($property)) {
                continue;
            }

            $value = $this->exposeValue($object, $property);

            $value = $this->handleValue($value, $property, self::DIRECTION_SERIALIZE);

            $result[$property->getSerializedName()] = $value;
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $data
     * @param object $object
     */
    public function toObject(array $data, $object)
    {
        $className = $this->getFullClassName($object);
        $metadata = $this->metadataFactory->getMetadataForClass($className);
        $unserializedProperties = 0;
        foreach ($metadata->getProperties() as $property) {
            if (!$property->isExpose() || $this->propertySkipper->shouldSkip($property)) {
                if ($this->isMediumStrictUnserializeMode() && array_key_exists($property->getSerializedName(), $data)) {
                    throw new InvalidArgumentException(sprintf('%s extra field', $property->getSerializedName()));
                }
                continue;
            }

            if (!array_key_exists($property->getSerializedName(), $data)) {
                if ($this->isStrictUnserializeMode()) {
                    throw new InvalidArgumentException(sprintf('%s field is lost', $property->getSerializedName()));
                }
                continue;
            }
            $value = $this->handleValue($data[$property->getSerializedName()], $property, self::DIRECTION_UNSERIALIZE, $object);

            if ($value === $object) {
                throw new RecursionException(sprintf('Invalid self reference detected. %s::%s', $className, $property->getName()));
            }

            $this->involveValue($object, $property, $value);
            $unserializedProperties ++;
        }

        if ($this->isMediumStrictUnserializeMode() && $unserializedProperties !== count($data)) {
            throw new InvalidArgumentException('Wrong number of fields in the deserialized data');
        }

        return $object;
    }

    /**
     * {@inheritDoc}
     *
     * @param ExclusionStrategyInterface|null $exclusionStrategy
     * @return ArrayAdapterInterface
     */
    public function setExclusionStrategy(ExclusionStrategyInterface $exclusionStrategy = null)
    {
        $this->cleanUpExclusionStrategies();
        if ($exclusionStrategy) {
            $this->addExclusionStrategy($exclusionStrategy);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @param ExclusionStrategyInterface $exclusionStrategy
     * @return ArrayAdapterInterface|void
     */
    public function addExclusionStrategy(ExclusionStrategyInterface $exclusionStrategy)
    {
        $this->propertySkipper->registerStrategy($exclusionStrategy);
    }

    public function cleanUpExclusionStrategies()
    {
        $this->propertySkipper->cleanUpStrategies();
    }

    /**
     * {@inheritDoc}
     *
     * @param boolean $unserializeMode
     * @throws InvalidArgumentException
     * @return ArrayAdapterInterface
     */
    public function setUnserializeMode($unserializeMode)
    {
        switch ($unserializeMode) {
            case self::STRICT_MODE:
            case self::MEDIUM_STRICT_MODE:
            case self::NON_STRICT_MODE:
                $this->unserializeMode = $unserializeMode;
                break;
            default:
                throw new InvalidArgumentException(sprintf('Non acceptable unserialize mode: "%s"', $unserializeMode));
        }

        return $this;
    }

    /**
     * @return boolean
     */
    private function isStrictUnserializeMode()
    {
        return $this->unserializeMode === self::STRICT_MODE;
    }

    /**
     * @return boolean
     */
    private function isMediumStrictUnserializeMode()
    {
        return $this->unserializeMode >= self::MEDIUM_STRICT_MODE;
    }

    /**
     * @param object $object
     * @return string
     * @throws InvalidArgumentException
     */
    private function getFullClassName($object)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException('Argument is not an object');
        }

        return get_class($object);
    }

    /**
     * @param $value
     * @param PropertyMetadata $property
     * @param $direct
     * @param null|mixed $object
     * @param bool $inner
     * @throws \Opensoft\SimpleSerializer\Exception\InvalidArgumentException
     * @return array|bool|float|int|string|null
     */
    private function handleValue($value, $property, $direct, $object = null, $inner = false)
    {
        $type = $property->getType();
        if ($value !== null) {
            if ($type === 'string') {
                $value = (string)$value;
            } elseif ($type === 'boolean') {
                $value = (boolean)$value;
            } elseif ($type === 'integer') {
                $value = (integer)$value;
            } elseif ($type === 'double') {
                $value = (double)$value;
            } elseif ($type === 'DateTime' || ($type[0] === 'D' && strpos($type, 'DateTime<') === 0)) {
                if ($direct == self::DIRECTION_SERIALIZE) {
                    $dateTimeFormat = $this->extractDateTimeFormat($type, DateTime::ISO8601);

                    $value = $value->format($dateTimeFormat);
                } elseif ($direct == self::DIRECTION_UNSERIALIZE) {
                    $originalValue = trim($value);

                    $dateTimeFormat = $this->extractDateTimeFormat($type);

                    // we should not allow empty string as date time argument.
                    //It can lead us to unexpected results
                    //Only 'null' is possible empty value
                    if (!$originalValue) {
                        throw new InvalidArgumentException('DateTime argument should be well formed string');
                    }

                    try {
                        $value = new DateTime($value);
                    } catch (\Exception $e) {
                        throw new InvalidArgumentException(sprintf('Invalid DateTime argument "%s"', $value), $e->getCode(), $e);
                    }
                    // if format was specified in metadata - format and compare parsed DateTime object with original string
                    if (isset($dateTimeFormat) && $value->format($dateTimeFormat) !== $originalValue) {
                        throw new InvalidArgumentException(sprintf('Invalid DateTime argument "%s"', $originalValue));
                    }
                }
            } elseif ($type === 'array' || ($type[0] === 'a' && strpos($type, 'array<') === 0)) {
                $tmpResult = array();
                $tmpType = new PropertyMetadata($property->getName());
                $tmpType->setExpose(true)->setSerializedName($property->getSerializedName());
                if (preg_match('/array<(?<type>[a-zA-Z\\\]+)>/', $type, $matches)) {
                    $tmpType->setType($matches['type']);
                }
                if ($direct == self::DIRECTION_UNSERIALIZE) {
                    $existsData = $this->exposeValue($object, $property);
                }
                foreach ($value as $k => $v) {
                    $tmpObject = $object;
                    if ($direct == self::DIRECTION_UNSERIALIZE && isset($existsData[$k]) && is_object($existsData[$k])) {
                        $tmpObject = $existsData[$k];
                        $inner = true;
                    }
                    $v = $this->handleValue($v, $tmpType, $direct, $tmpObject, $inner);
                    $tmpResult[$k] = $v;
                    unset($tmpObject);
                }
                $value = $tmpResult;
                unset($tmpResult, $tmpType);
            } elseif (is_object($value) && $direct == self::DIRECTION_SERIALIZE) {
                $value = $this->toArray($value);
            } elseif (is_array($value) && $direct == self::DIRECTION_UNSERIALIZE) {
                if ($inner) {
                    $innerObject = $object;
                } else {
                    $innerObject = $this->exposeValue($object, $property);
                }
                if (!is_object($innerObject) || !$innerObject instanceof $type) {
                    if (PHP_VERSION_ID >= 50400) {
                        $rc = new \ReflectionClass($type);
                        $innerObject = $rc->newInstanceWithoutConstructor();
                    } else {
                        $innerObject = unserialize(sprintf('O:%d:"%s":0:{}', strlen($type), $type));
                    }
                }
                $value = $this->toObject($value, $innerObject);
            } elseif ($type !== null) {
                throw new InvalidArgumentException(sprintf('Unsupported type: %s', $type));
            }
        }

        return $value;
    }

    /**
     * @param $object
     * @param $property
     * @return mixed
     * @throws RecursionException
     */
    private function exposeValue($object, $property)
    {
        $attributes = get_object_vars($object);
        if (array_key_exists($propertyName = $property->getName(), $attributes)) {
            $value =  $object->$propertyName;
        } else {
            $value = call_user_func(array($object, 'get' . ucfirst($property->getName())));
        }

        if ($value === $object) {
            throw new RecursionException(sprintf('Invalid self reference detected. %s::%s', $this->getFullClassName($object), $property->getName()));
        }

        return $value;
    }

    /**
     * @param $object
     * @param $property
     * @param $value
     */
    private function involveValue($object, $property, $value)
    {
        $attributes = get_object_vars($object);
        if (array_key_exists($propertyName = $property->getName(), $attributes)) {
            $object->$propertyName = $value;
        } else {
            call_user_func_array(array($object, 'set' . ucfirst($property->getName())), array($value));
        }
    }

    /**
     * Extracts specified date time format from given source
     * If source does not contain any format - returns default value
     *
     * @param string $source
     * @param string|null $defaultValue
     * @return string|null
     */
    private function extractDateTimeFormat($source, $defaultValue = null)
    {
        $dateTimeFormat = $defaultValue;

        if (preg_match('/DateTime<(?<type>[a-zA-Z0-9\,\.\s\-\:\/\\\]+)>/', $source, $matches)) {
            $dateTimeFormat = $matches['type'];
            if (defined('\DateTime::' . $dateTimeFormat)) {
                $dateTimeFormat = constant('\DateTime::' . $dateTimeFormat);
            }
        }

        return $dateTimeFormat;
    }
}

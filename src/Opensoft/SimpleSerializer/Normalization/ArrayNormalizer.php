<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Normalization;

use Opensoft\SimpleSerializer\Exception\InvalidArgumentException;
use Opensoft\SimpleSerializer\Exception\RecursionException;
use Opensoft\SimpleSerializer\Metadata\MetadataFactory;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;
use Opensoft\SimpleSerializer\Exclusion\Specification;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\ObjectHelper;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\DateTimeTransformer;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\DataProcessor;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 */
class ArrayNormalizer implements Normalizer
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
     * @var DataProcessor
     */
    private $dataProcessor;

    /**
     * @var integer
     */
    private $unserializeMode;

    /**
     * @param MetadataFactory $metadataFactory
     * @param integer $unserializeMode
     */
    public function __construct(MetadataFactory $metadataFactory, PropertySkipper $propertySkipper, DataProcessor $dataProcessor, $unserializeMode = self::NON_STRICT_MODE)
    {
        $this->metadataFactory = $metadataFactory;
        $this->propertySkipper = $propertySkipper;
        $this->dataProcessor = $dataProcessor;
        $this->setUnserializeMode($unserializeMode);
    }

    /**
     * {@inheritDoc}
     *
     * @param object $object
     * @return mixed
     */
    public function normalize($object)
    {
        $result = array();
        $className = ObjectHelper::getFullClassName($object);
        $metadata = $this->metadataFactory->getMetadataForClass($className);
        foreach ($metadata->getProperties() as $property) {
            if ($this->propertySkipper->shouldSkip($property)) {
                continue;
            }

            $value = ObjectHelper::expose($object, $property);

            $value = $this->dataProcessor->normalizeProcess($this, $value, $property);
            $result[$property->getSerializedName()] = $value;
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $data
     * @param object $object
     *
     * @return object
     */
    public function denormalize(array $data, $object)
    {
        $className = ObjectHelper::getFullClassName($object);
        $metadata = $this->metadataFactory->getMetadataForClass($className);
        $unserializedProperties = 0;
        foreach ($metadata->getProperties() as $property) {
            if ($this->propertySkipper->shouldSkip($property)) {
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
            $value = $this->dataProcessor->denormalizeProcess($this, $data[$property->getSerializedName()], $property, $object);
            ObjectHelper::involve($object, $property, $value);
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
     * @param Specification $exclusionSpecification
     */
    public function addExclusionSpecification(Specification $exclusionSpecification)
    {
        $this->propertySkipper->registerSpecification($exclusionSpecification);
    }

    public function cleanUpExclusionSpecifications()
    {
        $this->propertySkipper->cleanUpSpecifications();
    }

    /**
     * {@inheritDoc}
     *
     * @param boolean $unserializeMode
     * @throws InvalidArgumentException
     * @return ArrayNormalizer
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
     * {@inheritdoc}
     * @param mixed $object
     * @return bool
     */
    public function supportNormalization($object)
    {
        return is_object($object);
    }

}

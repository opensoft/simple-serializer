<?php
/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2014 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Normalization\ArrayNormalizer;


use Opensoft\SimpleSerializer\Exception\InvalidArgumentException;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class DataProcessor
{
    /**
     * @var TransformerFactory
     */
    private $transformerFactory;

    /**
     * @param TransformerFactory $factory
     */
    public function __construct(TransformerFactory $factory)
    {
        $this->transformerFactory = $factory;
    }

    /**
     * @param ArrayNormalizer $normalizer
     * @param mixed $value
     * @param PropertyMetadata $property
     * @return array|bool|float|int|null|string
     * @throws InvalidArgumentException
     */
    public function normalizeProcess(ArrayNormalizer $normalizer, $value, $property)
    {
        if ($value === null) {
            return null;
        }

        $transformerAliases = array(
            TransformerFactory::TYPE_SIMPLE_TRANSFORMER,
            TransformerFactory::TYPE_DATETIME_TRANSFORMER,
            TransformerFactory::TYPE_ARRAY_TRANSFORMER,
            TransformerFactory::TYPE_OBJECT_TRANSFORMER
        );

        $supports = false;
        foreach($transformerAliases as $transformerAlias)
        {
            $transformer = $this->transformerFactory->getTransformer($transformerAlias, $normalizer, $this);
            if ($transformer->supportType($property->getType()) && $transformer->supportValueForNormalization($value)) {
                $value = $transformer->normalize($value, $property);
                $supports = true;
                break;
            }
        }

        if (!$supports && $property->getType() !== null) {
            throw new InvalidArgumentException(sprintf('Unsupported type: %s', $property->getType()));
        }

        return $value;
    }

    /**
     * @param ArrayNormalizer $normalizer
     * @param mixed $value
     * @param PropertyMetadata $property
     * @param mixed $object
     * @param bool $inner
     * @return array|bool|\DateTime|float|int|null|string
     * @throws InvalidArgumentException
     */
    public function denormalizeProcess(ArrayNormalizer $normalizer, $value, $property, $object, $inner = false)
    {
        if ($value === null) {
            return null;
        }

        $transformerAliases = array(
            TransformerFactory::TYPE_SIMPLE_TRANSFORMER,
            TransformerFactory::TYPE_DATETIME_TRANSFORMER,
            TransformerFactory::TYPE_ARRAY_TRANSFORMER,
            TransformerFactory::TYPE_OBJECT_TRANSFORMER
        );

        $supports = false;
        foreach($transformerAliases as $transformerAlias)
        {
            $transformer = $this->transformerFactory->getTransformer($transformerAlias, $normalizer, $this);
            if ($transformer->supportType($property->getType()) && $transformer->supportValueForDenormalization($value)) {
                if ($transformerAlias === TransformerFactory::TYPE_OBJECT_TRANSFORMER && !$inner) {
                    $object = ObjectHelper::expose($object, $property);
                }
                $value = $transformer->denormalize($value, $property, $object);
                $supports = true;
                break;
            }
        }

        if (!$supports && $property->getType() !== null) {
            throw new InvalidArgumentException(sprintf('Unsupported type: %s', $property->getType()));
        }

        return $value;
    }
}

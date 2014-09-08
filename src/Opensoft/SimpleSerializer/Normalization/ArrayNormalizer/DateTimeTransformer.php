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

use DateTime;
use Opensoft\SimpleSerializer\Exception\InvalidArgumentException;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class DateTimeTransformer implements Transformer
{
    /**
     * @param DateTime $value
     * @param PropertyMetadata $property
     * @return string
     */
    public function normalize($value, $property)
    {
        $dateTimeFormat = $this->extractDateTimeFormat($property->getType(), DateTime::ISO8601);

        return $value->format($dateTimeFormat);
    }

    /**
     * Convert serialized value to DateTime object
     * @param string $value
     * @param PropertyMetadata $property
     * @param mixed $object
     * @return DateTime
     * @throws InvalidArgumentException
     */
    public function denormalize($value, $property, $object)
    {
        // we should not allow empty string as date time argument.
        //It can lead us to unexpected results
        //Only 'null' is possible empty value
        $originalValue = trim($value);
        if (!$originalValue) {
            throw new InvalidArgumentException('DateTime argument should be well formed string');
        }

        $dateTimeFormat = $this->extractDateTimeFormat($property->getType());
        try {
            $value = new DateTime($value);
        } catch (\Exception $e) {
            throw new InvalidArgumentException(sprintf('Invalid DateTime argument "%s"', $value), $e->getCode(), $e);
        }
        // if format was specified in metadata - format and compare parsed DateTime object with original string
        if (isset($dateTimeFormat) && $value->format($dateTimeFormat) !== $originalValue) {
            throw new InvalidArgumentException(sprintf('Invalid DateTime argument "%s"', $originalValue));
        }

        return $value;
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

    /**
     * @param $type
     * @return bool
     */
    public function supportType($type)
    {
        return $type === 'DateTime' || ($type[0] === 'D' && strpos($type, 'DateTime<') === 0);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function supportValueForNormalization($value)
    {
        return $value instanceof DateTime;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function supportValueForDenormalization($value)
    {
        return is_string($value);
    }
}


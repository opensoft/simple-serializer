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

use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer;
use Opensoft\SimpleSerializer\Exception\InvalidArgumentException;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;

/**
 * @author Dmitry Petrov <dmitry.petrov@opensoftdev.ru>
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class HandlerProcessor
{
    /**
     * @param ArrayNormalizer $normalizer
     * @param mixed $value
     * @param PropertyMetadata $property
     * @param int $direct
     * @param null|mixed $object
     * @param bool $inner
     * @throws InvalidArgumentException
     * @return array|bool|float|int|string|null
     */
    public function process(ArrayNormalizer $normalizer, $value, $property, $direct, $object = null, $inner = false)
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
                $dateHandler = new DateTimeHandler();
                $value= $dateHandler->handle($value, $type, $direct);
                unset($dateHandler);
            } elseif ($type === 'array' || ($type[0] === 'a' && strpos($type, 'array<') === 0)) {
                $arrayHandler = new ArrayHandler($this);
                $value = $arrayHandler->handle($normalizer, $value, $type, $property, $direct, $object);
                unset($arrayHandler);
            } elseif (is_object($value) && $direct == ArrayNormalizer::DIRECTION_SERIALIZE) {
                $value = $normalizer->normalize($value);
            } elseif (is_array($value) && $direct == ArrayNormalizer::DIRECTION_UNSERIALIZE) {
                $innerObjectHandler = new InnerObjectHandler();
                $value = $innerObjectHandler->handle($normalizer, $value, $type, $property, $object, $inner);
                unset($innerObjectHandler);
            } elseif ($type !== null) {
                throw new InvalidArgumentException(sprintf('Unsupported type: %s', $type));
            }
        }

        return $value;
    }
}

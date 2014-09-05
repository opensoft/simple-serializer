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
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class TransformerFactory
{
    const TYPE_SIMPLE_TRANSFORMER = 'simple';
    const TYPE_ARRAY_TRANSFORMER = 'array';
    const TYPE_DATETIME_TRANSFORMER = 'datetime';
    const TYPE_OBJECT_TRANSFORMER = 'object';

    /**
     * @var Transformer[]
     */
    private $trasformersCache = array();

    /**
     * @param string $alias
     * @param ArrayNormalizer $normalizer
     * @param DataProcessor $dataProcessor
     * @return Transformer
     */
    public function getTransformer($alias,ArrayNormalizer $normalizer,DataProcessor $dataProcessor)
    {
        if (!array_key_exists($alias, $this->trasformersCache)) {
            $this->trasformersCache[$alias] = $this->makeTransformer($alias, $normalizer, $dataProcessor);
        }

        return $this->trasformersCache[$alias];
    }

    /**
     * @param string $alias
     * @param ArrayNormalizer $normalizer
     * @param DataProcessor $dataProcess
     * @return Transformer
     * @throws InvalidArgumentException
     */
    private function makeTransformer($alias,ArrayNormalizer $normalizer,DataProcessor $dataProcessor)
    {
        switch($alias)
        {
            case self::TYPE_SIMPLE_TRANSFORMER:
                return new SimpleTypeTransformer();
            case self::TYPE_DATETIME_TRANSFORMER:
                return new DateTimeTransformer();
            case self::TYPE_ARRAY_TRANSFORMER:
                return new ArrayTransformer($normalizer, $dataProcessor);
            case self::TYPE_OBJECT_TRANSFORMER:
                return new ObjectTransformer($normalizer);
            default:
                throw new InvalidArgumentException(sprintf('Transformer factory not supports alias - "%s".', $alias));
        }
    }
}

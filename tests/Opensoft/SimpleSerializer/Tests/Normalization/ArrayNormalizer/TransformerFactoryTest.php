<?php
/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2014 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests\Normalization\ArrayNormalizer;

use Opensoft\SimpleSerializer\Exception\InvalidArgumentException;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\TransformerFactory;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class TransformerFactoryTest extends BaseTest
{
    /**
     * @var TransformerFactory
     */
    private $transformerFactory;

    /**
     * @dataProvider transformerAliasProvider
     * @param string $alias
     * @param string $expectedClassName
     */
    public function testGetTransformer($alias, $expectedClassName)
    {
        $transformer = $this->transformerFactory->getTransformer($alias, $this->normalizer, $this->processor);
        $this->assertInstanceOf($expectedClassName, $transformer);
    }

    /**
     * @return array
     */
    public function transformerAliasProvider()
    {
        return array(
            array(TransformerFactory::TYPE_SIMPLE_TRANSFORMER, '\Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\SimpleTypeTransformer'),
            array(TransformerFactory::TYPE_ARRAY_TRANSFORMER, '\Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\ArrayTransformer'),
            array(TransformerFactory::TYPE_DATETIME_TRANSFORMER, '\Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\DateTimeTransformer'),
            array(TransformerFactory::TYPE_OBJECT_TRANSFORMER, '\Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\ObjectTransformer')
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWrongAlias()
    {
        $this->transformerFactory->getTransformer('unknownTransformer', $this->normalizer, $this->processor);
    }

    public function setUp()
    {
        $this->initializeNormalizer();
        $this->transformerFactory = new TransformerFactory();
    }
}

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

use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\InnerObjectHandler;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;



/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class InnerObjectHandlerTest extends BaseTest
{
    /**
     * @var InnerObjectHandler
     */
    private $innerHandler;

    /**
     * @var InnerObjectHandler
     */
    private $nonInnerHandler;

    /**
     * @dataProvider childrenDataProvider
     * @param AChildren $aChildren
     * @param array $aChildrenAsArray
     */
    public function testNormalization($aChildren, $aChildrenAsArray)
    {
        $normalizedChildren = $this->innerHandler->normalizationHandle($aChildren, new PropertyMetadata('children'));
        $this->assertEquals($aChildrenAsArray, $normalizedChildren);
    }

    /**
     * @dataProvider childrenDataProvider
     * @param AChildren $aChildren
     * @param array $aChildrenAsArray
     */
    public function testDenormalization($aChildren, $aChildrenAsArray)
    {
        $object = new AChildren();
        $property = $this->makeSimpleProperty('children', 'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren');
        $this->innerHandler->denormalizationHandle($aChildrenAsArray, $property, $object);
        $this->assertEquals($aChildren, $object);

        $object = $this->innerHandler->denormalizationHandle($aChildrenAsArray, $property, 1);
        $this->assertEquals($aChildren, $object);

        $object = $this->innerHandler->denormalizationHandle($aChildrenAsArray, $property, new A(), true);
        $this->assertEquals($aChildren, $object);

        $parent = new \stdClass();
        $parent->children = new AChildren();
        $object = $this->nonInnerHandler->denormalizationHandle($aChildrenAsArray, $property, $parent);
        $this->assertEquals($aChildren, $object);
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->initializeNormalizer();
        $this->innerHandler = new InnerObjectHandler($this->normalizer, true);
        $this->nonInnerHandler = new InnerObjectHandler($this->normalizer, false);
    }
}

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

use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\ArrayTransformer;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\ObjectHelper;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\E;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class ArrayTransformerTest extends BaseTest
{
    /**
     * @var ArrayTransformer
     */
    private $arrayTransformer;

    /**
     * @dataProvider childrenDataProvider
     * @param AChildren $aChildren
     * @param array $aChildrenAsArray
     */
    public function testNormalize($aChildren, $aChildrenAsArray)
    {
        $object = new E();
        $object->setRid(3);
        $object->setObject($aChildren);
        $object->setArrayOfObjects(array($aChildren));

        $className = ObjectHelper::getFullClassName($object);
        $metadata = $this->metadataFactory->getMetadataForClass($className);
        $properties = $metadata->getProperties();
        $arrayOfObjectsProperty = $properties['arrayOfObjects'];
        $data = ObjectHelper::expose($object, $arrayOfObjectsProperty);
        $normalizedArray = $this->arrayTransformer->normalize($data, $arrayOfObjectsProperty);

        $this->assertEquals(array($aChildrenAsArray), $normalizedArray);
    }

    /**
     * @dataProvider childrenDataProvider
     * @param AChildren $aChildren
     * @param array $aChildrenAsArray
     */
    public function testDenormalize($aChildren, $aChildrenAsArray)
    {
        $object = new E();
        $className = ObjectHelper::getFullClassName($object);
        $metadata = $this->metadataFactory->getMetadataForClass($className);
        $properties = $metadata->getProperties();
        $denormalizedObject = $this->arrayTransformer->denormalize(array($aChildrenAsArray), $properties['arrayOfObjects'], $object);
        $this->assertEquals(array($aChildren), $denormalizedObject);
    }

    /**
     * @dataProvider childrenDataProvider
     * @param AChildren $aChildren
     * @param array $aChildrenAsArray
     */
    public function testSupportValue($aChildren, $aChildrenAsArray)
    {
        $this->assertTrue($this->arrayTransformer->supportValueForNormalization($aChildrenAsArray));
        $this->assertTrue($this->arrayTransformer->supportValueForDenormalization($aChildrenAsArray));
        $this->assertFalse($this->arrayTransformer->supportValueForNormalization($aChildren));
        $this->assertFalse($this->arrayTransformer->supportValueForDenormalization($aChildren));
    }

    public function testSupportType()
    {
        $this->assertTrue($this->arrayTransformer->supportType('array'));
        $this->assertTrue($this->arrayTransformer->supportType('array<'));
        $this->assertFalse($this->arrayTransformer->supportType('array('));
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->initializeNormalizer();
        $this->arrayTransformer = new ArrayTransformer($this->normalizer, $this->processor);
    }
}

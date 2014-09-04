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
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\ArrayHandler;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\ObjectHelper;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\E;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class ArrayHandlerTest extends BaseTest
{
    /**
     * @var ArrayHandler
     */
    private $arrayHandler;

    /**
     * @dataProvider childrenDataProvider
     * @param AChildren $aChildren
     * @param array $aChildrenAsArray
     */
    public function testNormalization($aChildren, $aChildrenAsArray)
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
        $normalizedArray = $this->arrayHandler->normalizationHandle($data, $arrayOfObjectsProperty);

        $this->assertEquals(array($aChildrenAsArray), $normalizedArray);
    }

    /**
     * @dataProvider childrenDataProvider
     * @param AChildren $aChildren
     * @param array $aChildrenAsArray
     */
    public function testDenormalization($aChildren, $aChildrenAsArray)
    {
        $object = new E();
        $className = ObjectHelper::getFullClassName($object);
        $metadata = $this->metadataFactory->getMetadataForClass($className);
        $properties = $metadata->getProperties();
        $denormalizedObject = $this->arrayHandler->denormalizationHandle(array($aChildrenAsArray), $properties['arrayOfObjects'], $object);
        $this->assertEquals(array($aChildren), $denormalizedObject);
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->initializeNormalizer();
        $this->arrayHandler = new ArrayHandler($this->normalizer, $this->processor);
    }
}

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
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\HandlerProcessor;
use Opensoft\SimpleSerializer\Normalization\PropertySkipper;
use Opensoft\SimpleSerializer\Metadata\MetadataFactory;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\ObjectHelper;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\E;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class ArrayHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayHandler
     */
    private $arrayHandler;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    public function testNormalization()
    {
        $time = time();
        $testTime = new \DateTime(date('Y-m-d H:i:s', $time));
        $aChildren = new AChildren();
        $aChildren->setRid(1);
        $aChildren->setStatus(true);
        $aChildren->setFloat(3.23);
        $aChildren->setArray(array(3, null));
        $aChildren->setAssocArray(array('tr' => 2));
        $aChildren->setDateTime($testTime);
        $aChildren->setNull(null);
        $aChildren->setName('name');

        $expectedObject = new E();
        $expectedObject->setRid(3);
        $expectedObject->setObject($aChildren);
        $expectedObject->setArrayOfObjects(array($aChildren));

        $expectedArray = array(
            array
            (
                'id' => 1,
                'name' => 'name',
                'status' => true,
                'float' => 3.23,
                'dateTime' => $testTime->format(\DateTime::ISO8601),
                'null' => null,
                'array' => array(3, null),
                'assocArray' => array('tr' => 2)
            )
        );

        $className = ObjectHelper::getFullClassName($expectedObject);
        $metadata = $this->metadataFactory->getMetadataForClass($className);
        $properties = $metadata->getProperties();
        $arrayOfObjectsProperty = $properties['arrayOfObjects'];
        $data = ObjectHelper::expose($expectedObject, $arrayOfObjectsProperty);
        $normalizedArray = $this->arrayHandler->normalizationHandle($data, $arrayOfObjectsProperty);

        $this->assertEquals($expectedArray, $normalizedArray);
    }

    public function testDenormalization()
    {
        $time = time();
        $testTime = new \DateTime(date('Y-m-d H:i:s', $time));
        $object = new E();
        $normalizedArray = array(
            array
            (
                'id' => 1,
                'name' => 'name',
                'status' => true,
                'float' => 3.23,
                'dateTime' => $testTime->format(\DateTime::ISO8601),
                'null' => null,
                'array' => array(3, null),
                'assocArray' => array('tr' => 2)
            )
        );

        $className = ObjectHelper::getFullClassName($object);
        $metadata = $this->metadataFactory->getMetadataForClass($className);
        $properties = $metadata->getProperties();
        $denormalizedObject = $this->arrayHandler->denormalizationHandle($normalizedArray, $properties['arrayOfObjects'], $object);


        $aChildren = new AChildren();
        $aChildren->setRid(1);
        $aChildren->setStatus(true);
        $aChildren->setFloat(3.23);
        $aChildren->setArray(array(3, null));
        $aChildren->setAssocArray(array('tr' => 2));
        $aChildren->setDateTime($testTime);
        $aChildren->setNull(null);
        $aChildren->setName('name');

        $this->assertEquals(array($aChildren), $denormalizedObject);
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $locator = $this->getMockForAbstractClass(
            'Opensoft\SimpleSerializer\Metadata\Driver\FileLocator',
            array(
                array(
                    'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A' => __DIR__ . '/../../Metadata/Driver/Fixture/A',
                    'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\B' => __DIR__ . '/../../Metadata/Driver/Fixture/B'
                )
            )
        );

        $driver = $this->getMockForAbstractClass(
            'Opensoft\SimpleSerializer\Metadata\Driver\YamlDriver',
            array($locator)
        );
        $handleProcessor = new HandlerProcessor();
        $this->metadataFactory = new MetadataFactory($driver);
        $arrayNormalizer = $this->getMockForAbstractClass('\Opensoft\SimpleSerializer\Normalization\ArrayNormalizer', array($this->metadataFactory, new PropertySkipper(), $handleProcessor));
        $this->arrayHandler = new ArrayHandler($arrayNormalizer, $handleProcessor);
    }
}

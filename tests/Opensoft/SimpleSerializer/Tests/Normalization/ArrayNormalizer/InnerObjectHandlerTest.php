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
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\HandlerProcessor;
use Opensoft\SimpleSerializer\Metadata\MetadataFactory;
use Opensoft\SimpleSerializer\Normalization\PropertySkipper;
use Symfony\Component\Yaml\Tests\A;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class InnerObjectHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InnerObjectHandler
     */
    private $innerHandler;

    /**
     * @var InnerObjectHandler
     */
    private $nonInnerHandler;

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

        $normalizedChildren = $this->innerHandler->normalizationHandle($aChildren, new PropertyMetadata('children'));

        $expectedArray = array(
            'id' => 1,
            'name' => "name",
            'status' => true,
            'float' => 3.23,
            'dateTime' => $testTime->format(\DateTime::ISO8601),
            'null' => null,
            'array' => array(3, null),
            'assocArray' => array('tr' => 2)
        );
        $this->assertEquals($expectedArray, $normalizedChildren);
    }

    public function testDenormalization()
    {
        $time = time();
        $testTime = new \DateTime(date('Y-m-d H:i:s', $time));
        $expectedChildren = new AChildren();
        $expectedChildren->setRid(1);
        $expectedChildren->setStatus(true);
        $expectedChildren->setFloat(3.23);
        $expectedChildren->setArray(array(3, null));
        $expectedChildren->setAssocArray(array('tr' => 2));
        $expectedChildren->setDateTime($testTime);
        $expectedChildren->setNull(null);
        $expectedChildren->setName('name');

        $data = array(
            'id' => 1,
            'name' => "name",
            'status' => true,
            'float' => 3.23,
            'dateTime' => $testTime->format(\DateTime::ISO8601),
            'null' => null,
            'array' => array(3, null),
            'assocArray' => array('tr' => 2)
        );

        $object = new AChildren();
        $property = new PropertyMetadata('children');
        $property->setType('Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren');
        $this->innerHandler->denormalizationHandle($data, $property, $object);
        $this->assertEquals($expectedChildren, $object);

        $object = $this->innerHandler->denormalizationHandle($data, $property, 1);
        $this->assertEquals($expectedChildren, $object);

        $object = $this->innerHandler->denormalizationHandle($data, $property, new \Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\A(), true);
        $this->assertEquals($expectedChildren, $object);

        $parent = new \stdClass();
        $parent->children = new AChildren();
        $object = $this->nonInnerHandler->denormalizationHandle($data, $property, $parent);
        $this->assertEquals($expectedChildren, $object);
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
        $metadataFactory = new MetadataFactory($driver);
        $normalizer = $this->getMockForAbstractClass('\Opensoft\SimpleSerializer\Normalization\ArrayNormalizer', array($metadataFactory, new PropertySkipper(), $handleProcessor));
        $this->innerHandler = new InnerObjectHandler($normalizer, true);
        $this->nonInnerHandler = new InnerObjectHandler($normalizer, false);
    }
}

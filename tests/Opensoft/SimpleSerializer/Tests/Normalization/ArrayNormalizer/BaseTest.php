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

use Opensoft\SimpleSerializer\Metadata\MetadataFactory;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer;
use Opensoft\SimpleSerializer\Normalization\ArrayNormalizer\HandlerProcessor;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;
use Opensoft\SimpleSerializer\Normalization\PropertySkipper;
use Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A\AChildren;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var ArrayNormalizer
     */
    protected $normalizer;

    /**
     * @var HandlerProcessor
     */
    protected $processor;

    /**
     * @param MetadataFactory $metadataFactory
     * @param HandlerProcessor $processor
     */
    protected function initializeNormalizer(MetadataFactory $metadataFactory = null, HandlerProcessor $processor = null)
    {
        if (!$metadataFactory) {
            $locator = $this->getMockForAbstractClass(
                'Opensoft\SimpleSerializer\Metadata\Driver\FileLocator',
                array(
                    array(
                        'Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A' => __DIR__ . '/../../Metadata/Driver/Fixture/A'
                    )
                )
            );

            $driver = $this->getMockForAbstractClass(
                'Opensoft\SimpleSerializer\Metadata\Driver\YamlDriver',
                array($locator)
            );

            $metadataFactory = new MetadataFactory($driver);
        }

        $this->processor = $processor ? $processor : new HandlerProcessor();
        $this->metadataFactory = $metadataFactory;
        $this->normalizer = new ArrayNormalizer($this->metadataFactory, new PropertySkipper(), $this->processor);
    }

    /**
     * @param string $propertyName
     * @param string|null $propertyType
     * @return PropertyMetadata
     */
    protected function makeSimpleProperty($propertyName, $propertyType = null)
    {
        $simpleProperty = new PropertyMetadata($propertyName);
        $simpleProperty->setType($propertyType);

        return $simpleProperty;
    }

    public function childrenDataProvider()
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

        $aChildrenAsArray = array
        (
            'id' => 1,
            'name' => 'name',
            'status' => true,
            'float' => 3.23,
            'dateTime' => $testTime->format(\DateTime::ISO8601),
            'null' => null,
            'array' => array(3, null),
            'assocArray' => array('tr' => 2)
        );

        return array(
            array($aChildren, $aChildrenAsArray)
        );
    }
}

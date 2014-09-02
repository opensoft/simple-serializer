<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2014 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Normalization;

use Opensoft\SimpleSerializer\Exclusion\Specification;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
final class PropertySkipper
{
    /**
     * @var Specification[]
     */
    private $specifications = array();

    /**
     * @param Specification[] $specifications
     */
    public function __construct($specifications = array())
    {
        foreach ($specifications as $specification) {
            $this->registerSpecification($specification);
        }
    }

    /**
     * @param Specification $specification
     */
    public function registerSpecification(Specification $specification)
    {
        $this->specifications[] = $specification;
    }

    public function cleanUpSpecifications()
    {
        $this->specifications = array();
    }

    /**
     * @param PropertyMetadata $property
     * @return bool
     */
    public function shouldSkip(PropertyMetadata $property)
    {
        foreach ($this->specifications as $specification) {
            if ($specification->isSatisfiedBy($property)) {
                return true;
            }
        }

        return false;
    }
}

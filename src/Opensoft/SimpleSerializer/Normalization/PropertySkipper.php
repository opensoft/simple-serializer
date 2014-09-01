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
    private $strategies = array();

    /**
     * @param Specification[] $strategies
     */
    public function __construct($strategies = array())
    {
        foreach ($strategies as $strategy) {
            $this->registerStrategy($strategy);
        }
    }

    /**
     * @param Specification $strategy
     */
    public function registerStrategy(Specification $strategy)
    {
        $this->strategies[] = $strategy;
    }

    public function cleanUpStrategies()
    {
        $this->strategies = array();
    }

    /**
     * @param PropertyMetadata $property
     * @return bool
     */
    public function shouldSkip(PropertyMetadata $property)
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->isSatisfiedBy($property)) {
                return true;
            }
        }

        return false;
    }
}

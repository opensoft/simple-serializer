<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2012 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Exclusion;

use Opensoft\SimpleSerializer\Exclusion\ExclusionStrategyInterface;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
final class PropertySkipper
{
    /**
     * @var ExclusionStrategyInterface[]
     */
    private $strategies;

    /**
     * @param ExclusionStrategyInterface[] $strategies
     */
    public function __construct($strategies = array())
    {
        $this->reset();
        foreach ($strategies as $strategy) {
            $this->registerStrategy($strategy);
        }
    }

    /**
     * @param ExclusionStrategyInterface $strategy
     */
    public function registerStrategy(ExclusionStrategyInterface $strategy)
    {
        $this->strategies[] = $strategy;
    }

    /**
     *
     */
    public function reset()
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
            if ($strategy->shouldSkipProperty($property)) {
                return true;
            }
        }

        return false;
    }
}

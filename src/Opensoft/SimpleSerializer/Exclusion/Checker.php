<?php

namespace Opensoft\SimpleSerializer\Exclusion;

use Opensoft\SimpleSerializer\Exclusion\ExclusionStrategyInterface;
use Opensoft\SimpleSerializer\Metadata\PropertyMetadata;

/**
 * @author Anton Konovalov <anton.konovalov@opensoftdev.ru>
 */
final class Checker
{
    /**
     * @var ExclusionStrategyInterface[]
     */
    private $strategies;

    /**
     * @param ExclusionStrategyInterface[] $strategies
     */
    public function __constructor($strategies = array())
    {
        $this->reset();
        foreach ($strategies as $strategy) {
            $this->add($strategy);
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
    public function shouldSkipProperty(PropertyMetadata $property)
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->shouldSkipProperty($property)) {
                return true;
            }
        }

        return false;
    }
} 
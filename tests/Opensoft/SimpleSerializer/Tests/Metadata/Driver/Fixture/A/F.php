<?php

/**
 * This file is part of the Simple Serializer.
 *
 * Copyright (c) 2015 Farheap Solutions (http://www.farheap.com)
 *
 * For the full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace Opensoft\SimpleSerializer\Tests\Metadata\Driver\Fixture\A;

/**
 * @author Andrey Reshetnikov <andrey.reshetnikov@opensoftdev.ru>
 */
class F
{
    /**
     * @var string
     */
    private $notNullSkipped;

    /**
     * @var string
     */
    private $nullSkipped;

    /**
     * @param string $notNullSkipped
     * @return F
     */
    public function setNotNullSkipped($notNullSkipped)
    {
        $this->notNullSkipped = $notNullSkipped;

        return $this;
    }

    /**
     * @return string
     */
    public function getNotNullSkipped()
    {
        return $this->notNullSkipped;
    }

    /**
     * @param string $nullSkipped
     * @return F
     */
    public function setNullSkipped($nullSkipped)
    {
        $this->nullSkipped = $nullSkipped;

        return $this;
    }

    /**
     * @return F
     */
    public function getNullSkipped()
    {
        return $this->nullSkipped;
    }
}

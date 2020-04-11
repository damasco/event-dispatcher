<?php

namespace Damasco\EventDispatcher\Tests\Event;

use Psr\EventDispatcher\StoppableEventInterface;

class StoppableEvent extends SimpleEvent implements StoppableEventInterface
{
    /**
     * @var bool
     */
    private $isStop;

    public function __construct(bool $isStop = true)
    {
        $this->isStop = $isStop;
    }

    public function isPropagationStopped(): bool
    {
        return $this->isStop;
    }
}

<?php

namespace Damasco\EventDispatcher\Tests\Listener;

use Damasco\EventDispatcher\Tests\Event\SimpleEvent;

class InvokableListener
{
    /**
     * @var string
     */
    private $value;

    public function __construct(string $value = '')
    {
        $this->value = $value;
    }

    public function __invoke(SimpleEvent $event)
    {
        $event->call($this->value);
    }
}

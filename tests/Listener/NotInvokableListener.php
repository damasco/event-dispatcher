<?php

namespace Damasco\EventDispatcher\Tests\Listener;

use Damasco\EventDispatcher\Tests\Event\SimpleEvent;

class NotInvokableListener
{
    public function handle(SimpleEvent $event): void
    {

    }
}

<?php

namespace Damasco\EventDispatcher\Provider;

use Psr\EventDispatcher\ListenerProviderInterface;

final class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var array
     */
    private $listeners;

    public function addListener(string $eventClassName, callable $listener): void
    {
        $this->listeners[$eventClassName][] = $listener;
    }

    public function getListenersForEvent(object $event): iterable
    {
        return $this->listeners[get_class($event)] ?? [];
    }
}

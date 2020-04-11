<?php

namespace Damasco\EventDispatcher\Provider;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

final class ContainerAwareListenerProvider implements ListenerProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var array
     */
    private $definitions;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $eventClassName
     * @param callable|string $listener string must be invokable class name
     */
    public function addListener(string $eventClassName, $listener): void
    {
        if (
            (!is_callable($listener) && !is_string($listener))
            || (is_string($listener) && !method_exists($listener, '__invoke'))
        ) {
            throw new \InvalidArgumentException('The listener must be callable or invokable class name');
        }
        $this->definitions[$eventClassName][] = $listener;
    }

    public function getListenersForEvent(object $event): iterable
    {
        $listeners = [];
        $definitions = $this->definitions[get_class($event)] ?? [];
        foreach ($definitions as $definition) {
            $listeners[] = is_callable($definition)
                ? $definition
                : $this->container->get($definition);
        }
        return $listeners;
    }
}

<?php

namespace Damasco\EventDispatcher\Tests;

use Damasco\EventDispatcher\EventDispatcher;
use Damasco\EventDispatcher\Provider\ContainerAwareListenerProvider;
use Damasco\EventDispatcher\Provider\ListenerProvider;
use Damasco\EventDispatcher\Tests\Event\SimpleEvent;
use Damasco\EventDispatcher\Tests\Event\StoppableEvent;
use Damasco\EventDispatcher\Tests\Listener\InvokableListener;
use DI\ContainerBuilder;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class EventDispatcherTest extends TestCase
{
    public function testWithSimpleProvider(): void
    {
        $provider = new ListenerProvider();
        $provider->addListener(SimpleEvent::class, static function (SimpleEvent $event) {
            $event->call(1);
        });
        $provider->addListener(SimpleEvent::class, static function (SimpleEvent $event) {
            $event->call(2);
        });
        $provider->addListener(SimpleEvent::class, static function (SimpleEvent $event) {
            $event->call(3);
        });
        $eventDispatcher = new EventDispatcher($provider);
        $event = new SimpleEvent();
        $event = $eventDispatcher->dispatch($event);
        self::assertEquals([1, 2, 3], $event->data());
    }

    public function testWithSimpleProviderAndWithoutListeners(): void
    {
        $eventDispatcher = new EventDispatcher(new ListenerProvider());
        $event = new SimpleEvent();
        $event = $eventDispatcher->dispatch($event);
        self::assertEquals([], $event->data());
    }

    public function testWithSimpleProviderAndStoppableEventIsNotStopped(): void
    {
        $provider = new ListenerProvider();
        $provider->addListener(SimpleEvent::class, static function (SimpleEvent $event) {
            $event->call(1);
        });
        $provider->addListener(StoppableEvent::class, static function (StoppableEvent $event) {
            $event->call(1);
        });
        $provider->addListener(StoppableEvent::class, static function (StoppableEvent $event) {
            $event->call(2);
        });
        $eventDispatcher = new EventDispatcher($provider);
        $event = new StoppableEvent(false);
        $event = $eventDispatcher->dispatch($event);
        self::assertEquals([1, 2], $event->data());
    }

    public function testWithSimpleProviderAndStoppableEventIsStopped(): void
    {
        $provider = new ListenerProvider();
        $provider->addListener(StoppableEvent::class, static function (StoppableEvent $event) {
            $event->call(1);
        });
        $provider->addListener(StoppableEvent::class, static function (StoppableEvent $event) {
            $event->call(2);
        });
        $eventDispatcher = new EventDispatcher($provider);
        $event = new StoppableEvent(true);
        $event = $eventDispatcher->dispatch($event);
        self::assertEquals([], $event->data());
    }

    /**
     * @throws Exception
     */
    public function testWithContainerAwareProvider(): void
    {
        $provider = new ContainerAwareListenerProvider($this->getContainer());
        $provider->addListener(SimpleEvent::class, static function (SimpleEvent $event) {
            $event->call('123');
        });
        $dispatcher = new EventDispatcher($provider);
        $event = new SimpleEvent();
        $event = $dispatcher->dispatch($event);
        self::assertEquals(['123'], $event->data());
    }

    /**
     * @throws Exception
     */
    public function testWithContainerAwareProviderAndWithoutListeners(): void
    {
        $eventDispatcher = new EventDispatcher(
            new ContainerAwareListenerProvider(
                $this->getContainer()
            )
        );
        $event = new SimpleEvent();
        $event = $eventDispatcher->dispatch($event);
        self::assertEquals([], $event->data());
    }

    /**
     * @throws Exception
     */
    public function testWithContainerAwareProviderAndInvokableListener(): void
    {
        $provider = new ContainerAwareListenerProvider($this->getContainer([
            InvokableListener::class => static function (ContainerInterface $container) {
                return new InvokableListener('test_call');
            }
        ]));
        $provider->addListener(SimpleEvent::class, InvokableListener::class);
        $dispatcher = new EventDispatcher($provider);
        $event = new SimpleEvent();
        $event = $dispatcher->dispatch($event);
        self::assertEquals(['test_call'], $event->data());
    }

    /**
     * @param array $definitions
     * @return ContainerInterface
     * @throws Exception
     */
    private function getContainer(array $definitions = []): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions($definitions);
        return $builder->build();
    }
}

<?php

namespace Damasco\EventDispatcher\Tests\Provider;

use Damasco\EventDispatcher\Provider\ListenerProvider;
use Damasco\EventDispatcher\Tests\Event\SimpleEvent;
use Damasco\EventDispatcher\Tests\Event\StoppableEvent;
use Damasco\EventDispatcher\Tests\Listener\InvokableListener;
use PHPUnit\Framework\TestCase;

class ListenerProviderTest extends TestCase
{
    public function testWithoutListeners(): void
    {
        $provider = new ListenerProvider();
        self::assertEmpty($provider->getListenersForEvent(new SimpleEvent()));
    }

    public function testWithClojureListeners(): void
    {
        $provider = new ListenerProvider();
        $provider->addListener(SimpleEvent::class, static function (SimpleEvent $event) {});
        $provider->addListener(SimpleEvent::class, static function (SimpleEvent $event) {});
        $provider->addListener(StoppableEvent::class, static function (StoppableEvent $event) {});
        self::assertCount(2, $provider->getListenersForEvent(new SimpleEvent()));
    }

    public function testWithInvokableListener(): void
    {
        $provider = new ListenerProvider();
        $provider->addListener(SimpleEvent::class, new class {
            public function __invoke(SimpleEvent $event)
            {
            }
        });
        $provider->addListener(SimpleEvent::class, new InvokableListener());
        $provider->addListener(StoppableEvent::class, new InvokableListener());
        $provider->addListener(StoppableEvent::class, new InvokableListener());
        self::assertCount(2, $provider->getListenersForEvent(new SimpleEvent()));
        self::assertCount(2, $provider->getListenersForEvent(new StoppableEvent()));
    }

    public function testWithInvokableAndClojureListeners(): void
    {
        $provider = new ListenerProvider();
        $provider->addListener(SimpleEvent::class, new InvokableListener());
        $provider->addListener(SimpleEvent::class, static function (SimpleEvent $event) {});
        self::assertCount(2, $provider->getListenersForEvent(new SimpleEvent()));
    }
}

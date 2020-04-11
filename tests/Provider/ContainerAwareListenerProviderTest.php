<?php

namespace Damasco\EventDispatcher\Tests\Provider;

use Damasco\EventDispatcher\Provider\ContainerAwareListenerProvider;
use Damasco\EventDispatcher\Tests\Event\SimpleEvent;
use Damasco\EventDispatcher\Tests\Event\StoppableEvent;
use Damasco\EventDispatcher\Tests\Listener\InvokableListener;
use Damasco\EventDispatcher\Tests\Listener\NotInvokableListener;
use DI\ContainerBuilder;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ContainerAwareListenerProviderTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testWithoutListeners(): void
    {
        $provider = new ContainerAwareListenerProvider($this->getContainer());
        self::assertEmpty($provider->getListenersForEvent(new SimpleEvent()));
    }

    /**
     * @throws Exception
     */
    public function testWithClojureListeners(): void
    {
        $provider = new ContainerAwareListenerProvider($this->getContainer());
        $provider->addListener(SimpleEvent::class, static function (SimpleEvent $event) {});
        $provider->addListener(SimpleEvent::class, static function (SimpleEvent $event) {});
        $provider->addListener(StoppableEvent::class, static function (StoppableEvent $event) {});
        self::assertCount(2, $provider->getListenersForEvent(new SimpleEvent()));
        self::assertCount(1, $provider->getListenersForEvent(new StoppableEvent()));
    }

    /**
     * @throws Exception
     */
    public function testWithInvokableListeners(): void
    {
        $provider = new ContainerAwareListenerProvider($this->getContainer());
        $provider->addListener(SimpleEvent::class, InvokableListener::class);
        $provider->addListener(SimpleEvent::class, InvokableListener::class);
        $provider->addListener(StoppableEvent::class, static function (StoppableEvent $event) {});
        $event = new SimpleEvent();
        self::assertCount(2, $provider->getListenersForEvent($event));
    }

    /**
     * @throws Exception
     */
    public function testWithInvokableAndClojureListeners(): void
    {
        $provider = new ContainerAwareListenerProvider($this->getContainer());
        $provider->addListener(SimpleEvent::class, InvokableListener::class);
        $provider->addListener(SimpleEvent::class, static function (SimpleEvent $event) {});
        self::assertCount(2, $provider->getListenersForEvent(new SimpleEvent()));
    }

    /**
     * @throws Exception
     */
    public function testAddNotInvokableListener(): void
    {
        $provider = new ContainerAwareListenerProvider($this->getContainer());
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The listener must be callable or invokable class name');
        $provider->addListener(SimpleEvent::class, new NotInvokableListener());
    }

    /**
     * @throws Exception
     */
    public function testAddNotInvokableListenerClassName(): void
    {
        $provider = new ContainerAwareListenerProvider($this->getContainer());
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The listener must be callable or invokable class name');
        $provider->addListener(SimpleEvent::class, NotInvokableListener::class);
    }

    /**
     * @var $listener
     * @dataProvider incorrectTypeListeners
     * @throws Exception
     */
    public function testAddIncorrectListener($listener): void
    {
        $provider = new ContainerAwareListenerProvider($this->getContainer());
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The listener must be callable or invokable class name');
        $provider->addListener(SimpleEvent::class, $listener);
    }

    public function incorrectTypeListeners(): array
    {
        return [
            [''],
            ['NotExistListener'],
            [1],
            [[]],
        ];
    }

    /**
     * @return ContainerInterface
     * @throws Exception
     */
    private function getContainer(): ContainerInterface
    {
        return (new ContainerBuilder())->build();
    }
}

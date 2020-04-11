# PSR-14 EventDispatcher

## Installation

```bash 
$ composer require damasco/event-dispatcher
```

## Providers

### 1. Simple provider 

**Example:**

```php
$provider = new Damasco\EventDispatcher\Provider\ListenerProvider();
$provider->addListener(ExampleEvent::class, function (ExampleEvent $event) {
    // code...
});
$provider->addListener(ExampleEvent::class, new class {
    public function __invoke(ExampleEvent $event)
    {
        // code..
    }
});
$eventDispatcher = new Damasco\EventDispatcher\EventDispatcher($provider);
$eventDispatcher->dispatch(new ExampleEvent(...));
```

### 2. Container aware provider

**Example:**

```php
/** @var \Psr\Container\ContainerInterface $container */
$provider = new Damasco\EventDispatcher\Provider\ContainerAwareListenerProvider($container);
$provider->addListener(ExampleEvent::class, function (ExampleEvent $event) {
    // code...
});
$provider->addListener(ExampleEvent::class, new class {
    public function __invoke(ExampleEvent $event)
    {
        // code..
    }
});
// `ExampleEventListener` must be invokable class
$provider->addListener(ExampleEvent::class, ExampleEventListener::class);
$eventDispatcher = new Damasco\EventDispatcher\EventDispatcher($provider);
$eventDispatcher->dispatch(new ExampleEvent(...));
```

## Stoppable event

```php
class StoppableEvent implements Psr\EventDispatcher\StoppableEventInterface
{
    // code...

    public function isPropagationStopped(): bool
    {
        return true;
    }
}
```
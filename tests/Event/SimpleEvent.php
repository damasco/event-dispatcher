<?php

namespace Damasco\EventDispatcher\Tests\Event;

class SimpleEvent
{
    /**
     * @var array
     */
    private $data = [];

    public function call($value): void
    {
        $this->data[] = $value;
    }

    public function data(): array
    {
        return $this->data;
    }
}

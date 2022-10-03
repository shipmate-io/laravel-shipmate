<?php

namespace Shipmate\Shipmate\MessageQueue;

class MessagePayload
{
    public function __construct(
        protected array $items = []
    ) {
    }

    public static function new(array $items = []): self
    {
        return new self($items);
    }

    public function serialize(): string
    {
        return base64_encode(json_encode($this->items));
    }

    public static function deserialize(string $serialization): static
    {
        return new static(json_decode(base64_decode($serialization), true));
    }

    /**
     * Check if a given key exists in the payload.
     */
    public function has(string $key): bool
    {
        if (! $this->items) {
            return false;
        }

        $items = $this->items;

        if (array_key_exists($key, $this->items)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (! is_array($items) || ! array_key_exists($segment, $items)) {
                return false;
            }

            $items = $items[$segment];
        }

        return true;
    }

    /**
     * Return the value of a given key in the payload.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        if (! is_string($key) || strpos($key, '.') === false) {
            return $default;
        }

        $items = $this->items;

        foreach (explode('.', $key) as $segment) {
            if (! is_array($items) || ! array_key_exists($segment, $items)) {
                return $default;
            }

            $items = &$items[$segment];
        }

        return $items;
    }
}

<?php

namespace Shipmate\LaravelShipmate\MessageQueue;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Shipmate\Shipmate\MessageQueue\Message;

class MessageHandler
{
    public function __construct(
        private string|array $messageHandler
    ) {
    }

    public static function new(string|array $messageHandler): static
    {
        return new static($messageHandler);
    }

    public function handle(Message $message): void
    {
        $className = $this->getClassName();
        $methodName = $this->getMethodName();

        app($className)->{$methodName}($message);
    }

    private function getClassName(): string
    {
        if (is_array($this->messageHandler)) {
            return $this->messageHandler[0];
        }

        return $this->messageHandler;
    }

    private function getMethodName(): string
    {
        if (is_array($this->messageHandler) && array_key_exists(1, $this->messageHandler)) {
            return $this->messageHandler[1];
        }

        return $this->guessMethodName();
    }

    private function guessMethodName(): string
    {
        $className = $this->getClassName();
        $publicMethods = (new ReflectionClass($className))->getMethods(ReflectionMethod::IS_PUBLIC);

        $method = collect($publicMethods)->first(function (ReflectionMethod $method): bool {
            $firstParameter = $method->getParameters()[0] ?? null;

            if (! $firstParameter) {
                return false;
            }

            $type = $firstParameter->getType();

            if (! $type instanceof ReflectionNamedType) {
                return false;
            }

            return $type->getName() === Message::class;
        });

        if ($method === null) {
            throw new Exception("Could not find a handler method in Message Handler '{$className}'.");
        }

        return $method->getName();
    }
}

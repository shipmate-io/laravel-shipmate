<?php

namespace Shipmate\LaravelShipmate\MessageQueue;

use Google\Cloud\Core\Exception\ConflictException;
use Google\Cloud\Core\Exception\ServiceException;
use Google\Cloud\PubSub\PubSubClient as GoogleClient;
use Google\Cloud\PubSub\Subscription;
use Google\Cloud\PubSub\Topic;
use Illuminate\Support\Str;
use Shipmate\LaravelShipmate\ShipmateConfig;

class MessageQueue
{
    private ShipmateConfig $shipmateConfig;

    private MessageQueueConfig $messageQueueConfig;

    private GoogleClient $googleClient;

    public function __construct()
    {
        $this->shipmateConfig = ShipmateConfig::new();
        $this->messageQueueConfig = MessageQueueConfig::new();

        $emulatorHost = env('MESSAGE_QUEUE_EMULATOR_HOST');

        if ($emulatorHost) {
            putenv("PUBSUB_EMULATOR_HOST={$emulatorHost}");
            $options = [
                'transport' => 'transport',
            ];
        } else {
            $options = [
                'projectId' => $this->shipmateConfig->getProjectId(),
                'keyFile' => $this->shipmateConfig->getKey(),
            ];
        }

        $this->googleClient = new GoogleClient($options);
    }

    public static function new(): static
    {
        return app(static::class);
    }

    // Connection

    public function connect(): void
    {
        try {
            $this->getTopic()->exists();
        } catch (ServiceException $exception) {
            throw new MessageQueueNotReadyYet($exception->getMessage());
        }
    }

    // Topics

    /**
     * @return Topic[]
     */
    public function listTopics(): array
    {
        $topics = [];

        foreach ($this->googleClient->topics() as $topic) {
            $topics[] = $topic;
        }

        return $topics;
    }

    public function getTopic(?string $name = null): Topic
    {
        $name ??= $this->messageQueueConfig->getDefaultTopic();

        return $this->googleClient->topic($name);
    }

    public function createTopic(?string $name = null): void
    {
        $topic = $this->getTopic($name);

        if ($topic->exists()) {
            return;
        }

        try {
            $topic->create();
        } catch (ConflictException $exception) {
            if (Str::contains($exception->getMessage(), 'Topic already exists')) {
                return;
            }
            throw $exception;
        }
    }

    public function deleteTopic(?string $name = null): void
    {
        $topic = $this->getTopic($name);

        if (! $topic->exists()) {
            return;
        }

        $topic->delete();
    }

    // Subscriptions

    /**
     * @return Subscription[]
     */
    public function listSubscriptions(?string $topicName = null): array
    {
        $subscriptions = [];

        foreach ($this->getTopic($topicName)->subscriptions() as $subscription) {
            $subscriptions[] = $subscription;
        }

        return $subscriptions;
    }

    public function getSubscription(?string $name = null, ?string $topicName = null): Subscription
    {
        $name ??= $this->messageQueueConfig->getDefaultSubscription();

        return $this->getTopic($topicName)->subscription($name);
    }

    public function createSubscription(string $name, string $topicName, string $endpoint): void
    {
        $subscription = $this->getSubscription($name, $topicName);

        $options = [
            'pushConfig' => [
                'pushEndpoint' => $endpoint,
            ],
        ];

        if (! $subscription->exists()) {
            $subscription->create($options);

            return;
        }

        $currentPushEndpoint = $subscription->info()['pushConfig']['pushEndpoint'] ?? null;

        if ($currentPushEndpoint === $endpoint) {
            return;
        }

        $subscription->delete();
        $subscription->create($options);
    }

    public function deleteSubscription(?string $name = null, ?string $topicName = null): void
    {
        $subscription = $this->getSubscription($name, $topicName);

        if (! $subscription->exists()) {
            return;
        }

        $subscription->delete();
    }

    // Messages

    public function publishMessage(ShouldPublish $message, ?string $topicName = null): void
    {
        $messagePayload = new MessagePayload($message->publishWith());

        $this->getTopic($topicName)->publish([
            'data' => $messagePayload->serialize(),
            'attributes' => [
                'type' => $message->publishAs(),
            ],
        ]);
    }
}

<?php
/*
 * This file is part of the prooph/event-store.
 * (c) Alexander Miertsch <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 31.08.14 - 20:00
 */

namespace Prooph\EventStoreTest\Mock;

use Prooph\Common\Messaging\DomainEvent;
use Prooph\EventStore\Stream\StreamEvent;
use Rhumsaa\Uuid\Uuid;

/**
 * Class User
 *
 * @package Prooph\EventStoreTest\Mock
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
class User 
{
    /**
     * @var Uuid
     */
    private $userId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var StreamEvent[]
     */
    private $recordedEvents;

    /**
     * @var int
     */
    private $version = 0;

    /**
     * @param string $name
     * @param string $email
     */
    public function __construct($name, $email)
    {
        $this->recordThat(UserCreated::with(
            array(
                'user_id' => Uuid::uuid4()->toString(),
                'name' => $name,
                'email' => $email,
            ),
            $this->nextVersion()
        ));
    }

    /**
     * @return Uuid
     */
    public function id()
    {
        return $this->userId;
    }

    public function name()
    {
        return $this->name;
    }

    public function email()
    {
        return $this->email;
    }

    public function changeName($newName)
    {
        $this->recordThat(UsernameChanged::with(
            array(
                'old_name' => $this->name,
                'new_name' => $newName
            ),
            $this->nextVersion()
        ));
    }

    private function recordThat(TestDomainEvent $domainEvent)
    {
        $this->recordedEvents[] = $domainEvent;

        $this->apply($domainEvent);
    }

    private function apply(TestDomainEvent $event)
    {
        if ($event instanceof UserCreated) {
            $this->whenUserCreated($event);
        }

        if ($event instanceof UsernameChanged) {
            $this->whenUsernameChanged($event);
        }
    }

    private function whenUserCreated(UserCreated $userCreated)
    {
        $payload = $userCreated->payload();

        $this->userId = Uuid::fromString($payload['user_id']);
        $this->name   = $payload['name'];
        $this->email  = $payload['email'];
    }

    private function whenUsernameChanged(UsernameChanged $usernameChanged)
    {
        $this->name = $usernameChanged->payload()['new_name'];
    }

    private function popRecordedEvents()
    {
        $recordedEvents = $this->recordedEvents;

        $this->recordedEvents = array();

        return $recordedEvents;
    }

    /**
     * @param DomainEvent[] $streamEvents
     */
    private function replay(array $streamEvents)
    {
        foreach ($streamEvents as $streamEvent)
        {
            $this->apply($streamEvent);
            $this->version = $streamEvent->version();
        }
    }

    private function nextVersion()
    {
        return ++$this->version;
    }
}
 
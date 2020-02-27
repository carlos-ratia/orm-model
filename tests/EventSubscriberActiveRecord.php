<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model;



use Cratia\ORM\Model\Events\EventErrorPayload;
use Cratia\ORM\Model\Events\EventPayload;
use Cratia\ORM\Model\Events\Events;
use Doctrine\Common\EventSubscriber;

/**
 * Class EventSubscriberActiveRecord
 * @package Tests\Cratia\ORM\Model
 */
class EventSubscriberActiveRecord implements EventSubscriber
{

    public $onError;

    public $onModelLoaded;
    public $onModelReade;

    public $onModelCreated;
    public $onModelUpdated;
    public $onModelDeleted;

    public function __construct()
    {
        $this->onError = false;
        $this->onModelLoaded = false;
        $this->onModelReade = false;
        $this->onModelCreated = false;
        $this->onModelUpdated = false;
        $this->onModelDeleted = false;
    }


    public function onModelError(EventErrorPayload $event)
    {
        $this->onError = true;
    }

    public function onModelLoaded(EventPayload $event)
    {
        $this->onModelLoaded = true;
    }

    public function onModelReade(EventPayload $event)
    {
        $this->onModelReade = true;
    }

    public function onModelCreated(EventPayload $event)
    {
        $this->onModelCreated = true;
    }

    public function onModelUpdated(EventPayload $event)
    {
        $this->onModelUpdated = true;
    }

    public function onModelDeleted(EventPayload $event)
    {
        $this->onModelDeleted = true;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return
            [
                Events::ON_ERROR,
                Events::ON_MODEL_CREATED,
                Events::ON_MODEL_UPDATED,
                Events::ON_MODEL_DELETED,
                Events::ON_MODEL_LOADED,
                Events::ON_MODEL_READ,
            ];
    }
}
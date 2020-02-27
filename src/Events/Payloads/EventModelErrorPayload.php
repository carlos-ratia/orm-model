<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Events\Payloads;


use Doctrine\Common\EventArgs;
use Doctrine\DBAL\DBALException;
use Exception;
use JsonSerializable;

/**
 * Class EventModelErrorPayload
 * @package Cratia\ORM\Model\Events
 */
class EventModelErrorPayload extends EventArgs implements JsonSerializable
{
    /**
     * @var DBALException|Exception
     */
    private $exception;

    /**
     * EventPayload constructor.
     * @param DBALException|Exception $e
     */
    public function __construct($e)
    {
        $this->exception = $e;
    }

    /**
     * @return DBALException|Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return ['exception' => $this->getException()];
    }
}
<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Events;


use Doctrine\Common\EventArgs;
use Doctrine\DBAL\DBALException;
use Exception;

/**
 * Class EventErrorPayload
 * @package Cratia\ORM\Model\Events
 */
class EventErrorPayload extends EventArgs
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

}
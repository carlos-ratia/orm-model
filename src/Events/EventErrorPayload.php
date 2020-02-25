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
     * @var DBALException|Exception|null
     */
    private $exception;

    /**
     * EventPayload constructor.
     * @param Exception|null $e
     */
    public function __construct(Exception $e = null)
    {
        $this->exception = $e;
    }

    /**
     * @return DBALException|Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }

}
<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Events;

/**
 * Class Events
 * @package Cratia\ORM\Model\Events
 */
final class Events
{
    /**
     * Private constructor. This class cannot be instantiated.
     */
    private function __construct()
    {
    }

    public const ON_ERROR = 'onError';

    public const ON_MODEL_LOADED = 'onAfterExecuteQuery';
    public const ON_MODEL_READED = 'onBeforeExecuteQuery';

    public const ON_MODEL_CREATED = 'onAfterExecuteNonQuery';
    public const ON_MODEL_UPDATED = 'onBeforeExecuteNonQuery';
    public const ON_MODEL_DELETED = 'onBeforeExecuteNonQuery';
}
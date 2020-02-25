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

    public const ON_MODEL_LOADED = 'onModelLoaded';
    public const ON_MODEL_READE = 'onModelReade';

    public const ON_MODEL_CREATED = 'onModelCreated';
    public const ON_MODEL_UPDATED = 'onModelUpdated';
    public const ON_MODEL_DELETED = 'onModelDeleted';
}
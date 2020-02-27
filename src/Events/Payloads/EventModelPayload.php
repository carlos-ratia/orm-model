<?php
declare(strict_types=1);


namespace Cratia\ORM\Model\Events\Payloads;


use Cratia\ORM\DBAL\Interfaces\IQueryDTO;
use Cratia\ORM\DQL\Interfaces\IQuery;
use Cratia\ORM\Model\Interfaces\IModel;
use Doctrine\Common\EventArgs;
use JsonSerializable;

/**
 * Class EventModelPayload
 * @package Cratia\ORM\Model\Events
 */
class EventModelPayload extends EventArgs implements JsonSerializable
{

    /**
     * @var IModel
     */
    private $model;

    /**
     * @var IQuery
     */
    private $query;

    /**
     * @var IQueryDTO
     */
    private $dto;

    /**
     * EventPayload constructor.
     * @param IModel $model
     * @param IQuery $query
     * @param IQueryDTO $dto
     */
    public function __construct(IModel $model, IQuery $query, IQueryDTO $dto)
    {
        $this->model = $model;
        $this->query = $query;
        $this->dto = $dto;
    }

    /**
     * @return IModel
     */
    public function getModel(): IModel
    {
        return $this->model;
    }

    /**
     * @return IQuery
     */
    public function getQuery(): IQuery
    {
        return $this->query;
    }

    /**
     * @return IQueryDTO
     */
    public function getDto(): IQueryDTO
    {
        return $this->dto;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return ['dto' => $this->getDto()];
    }
}
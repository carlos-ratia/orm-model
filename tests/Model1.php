<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model;


class Model1
{
    /**
     * @var int|null
     */
    private $property_1;

    /**
     * @var int|null
     */
    public $property_2;

    /**
     * @var int|null
     */
    protected $property_3;

    /**
     * Model1 constructor.
     * @param int|null $property_1
     * @param int|null $property_2
     * @param int|null $property_3
     */
    public function __construct(int $property_1 = null, int $property_2 = null, int $property_3 = null)
    {
        $this->property_1 = $property_1;
        $this->property_2 = $property_2;
        $this->property_3 = $property_3;
    }

    /**
     * @return int|null
     */
    public function getProperty1(): ?int
    {
        return $this->property_1;
    }

    /**
     * @return int|null
     */
    public function getProperty3(): ?int
    {
        return $this->property_3;
    }

    /**
     * @param int|null $property_1
     * @return Model1
     */
    public function setProperty1(?int $property_1): Model1
    {
        $this->property_1 = $property_1;
        return $this;
    }

    /**
     * @param int|null $property_3
     * @return Model1
     */
    public function setProperty3(?int $property_3): Model1
    {
        $this->property_3 = $property_3;
        return $this;
    }
}
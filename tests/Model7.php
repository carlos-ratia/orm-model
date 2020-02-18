<?php
declare(strict_types=1);


namespace Tests\Cratia\ORM\Model;

use Cratia\ORM\Model\Interfaces\IModelMapper;
use Cratia\ORM\Model\Traits\ModelMapper;

/**
 * Class Model7
 * @package Tests\Cratia\ORM\Model
 */
class Model7 implements IModelMapper
{
    use ModelMapper;

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
}
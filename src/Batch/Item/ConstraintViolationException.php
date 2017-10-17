<?php


namespace Kaliop\BatchBundle\Batch\Item;


use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class ConstraintViolationException
 */
class ConstraintViolationException extends \Exception
{
    /** @var ConstraintViolationList  */
    protected $violations;

    /** @var DataInvalidItem  */
    protected $invalidItem;

    /**
     * ConstraintViolationException constructor.
     * @param DataInvalidItem $invalidItem
     * @param ConstraintViolationList $violations
     */
    public function __construct(DataInvalidItem $invalidItem, ConstraintViolationList $violations)
    {
        $this->invalidItem = $invalidItem;
        $this->violations = $violations;
    }

    /**
     * @return DataInvalidItem
     */
    public function getInvalidItem()
    {
        return $this->invalidItem;
    }

    /**
     * @return ConstraintViolationList
     */
    public function getViolations()
    {
        return $this->violations;
    }

    public function clear()
    {
        $this->violations = null;
        $this->invalidItem = null;
    }

}

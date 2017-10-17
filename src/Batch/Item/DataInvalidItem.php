<?php

namespace Kaliop\BatchBundle\Batch\Item;

/**
 * This class handle invalid items that could be raised by Reader or Processor.
 */
class DataInvalidItem implements InvalidItemInterface
{
    /** @var mixed */
    protected $invalidData;

    /**
     * @param mixed $invalidData
     */
    public function __construct($invalidData)
    {
        $this->invalidData = $invalidData;
    }

    /**
     * {@inheritdoc}
     */
    public function getInvalidData()
    {
        return $this->invalidData;
    }
}

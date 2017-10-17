<?php

namespace Kaliop\BatchBundle\Batch\Item;

/**
 * Classes that implement this interface have to handle invalid items raised in the Processor, Reader and Writer.
 */
interface InvalidItemInterface
{
    /**
     * Get the invalid data
     *
     * @return mixed
     */
    public function getInvalidData();
}

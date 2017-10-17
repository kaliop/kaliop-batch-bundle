<?php


namespace Kaliop\BatchBundle\Batch\Item;

/**
 * Interface ItemProcessorInterface
 * @package Kaliop\BatchBundle\Batch\Item
 */
interface ItemProcessorInterface
{
    public function process(&$item);
}

<?php


namespace Kaliop\BatchBundle\Batch\Item;

/**
 * Interface ItemWriterInterface
 * @package Kaliop\BatchBundle\Batch\Item
 */
interface ItemWriterInterface
{
    public function write(array $items);
}

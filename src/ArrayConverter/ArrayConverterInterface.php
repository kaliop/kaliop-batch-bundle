<?php


namespace Kaliop\BatchBundle\ArrayConverter;

/**
 * Interface ArrayConverterInterface
 */
interface ArrayConverterInterface
{
    /**
     * @param array $item
     * @return array
     */
    public function convert(array $item);
}

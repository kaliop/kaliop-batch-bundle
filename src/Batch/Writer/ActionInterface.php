<?php


namespace Kaliop\BatchBundle\Batch\Writer;

/**
 * Interface ActionInterface
 */
interface ActionInterface
{
    const INSERT = 'insert';
    const UPDATE = 'update';
    const DELETE = 'delete';
}

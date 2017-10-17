<?php


namespace Kaliop\BatchBundle\Batch\Writer;


use Kaliop\BatchBundle\Batch\Item\ItemWriterInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class EntityWriter
 */
class EntityWriter implements ItemWriterInterface
{
    /** @var RegistryInterface */
    protected $doctrine;

    /**
     * EntityWriter constructor.
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param array $items
     * @throws \Exception
     */
    public function write(array $items)
    {
        $em = $this->doctrine->getManager();

        foreach ($items as $item) {
            $em->persist($item);
        }

        try {
            $em->flush();
        } catch (\Exception $e) {
            $this->doctrine->resetManager();
            throw $e;
        } finally {
            $em->clear();
        }
    }
}

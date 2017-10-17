<?php


namespace Kaliop\BatchBundle\Batch\Writer;


use Doctrine\DBAL\Connection;
use Kaliop\BatchBundle\Batch\Item\ItemWriterInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class FlatArrayWriter
 */
class FlatArrayWriter implements ItemWriterInterface
{
    /** @var Connection */
    protected $connection;

    /** @var string */
    protected $tableName;

    /**
     * FlatArrayWriter constructor.
     * @param RegistryInterface $doctrine
     * @param string $tableName
     */
    public function __construct(RegistryInterface $doctrine, string $tableName)
    {
        $this->connection = $doctrine->getConnection();
        $this->tableName = $tableName;
    }

    /**
     * @param array $items
     */
    public function write(array $items)
    {
        foreach ($items as $item) {
            if (isset($item['id'])) {
                $this->update($item);
            } else {
                $this->insert($item);
            }
        }
    }

    /**
     * @param array $data
     */
    protected function insert(array $data)
    {
        $this->connection->insert($this->tableName, $data);
    }

    /**
     * @param array $data
     */
    protected function update(array $data)
    {
        $this->connection->update($this->tableName, $data, ['id' => $data['id']]);
    }
}

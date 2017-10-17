<?php


namespace Kaliop\BatchBundle\Batch\Reader;


use Kaliop\BatchBundle\ArrayConverter\ArrayConverterInterface;
use Kaliop\BatchBundle\Batch\Item\DataInvalidItem;
use Kaliop\BatchBundle\Batch\Item\InvalidItemException;
use Kaliop\BatchBundle\Batch\Item\ItemReaderInterface;
use Kaliop\BatchBundle\Connector\Reader\File\CsvFileIterator;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CsvReader
 */
class CsvReader implements ItemReaderInterface
{
    /** @var ArrayConverterInterface  */
    protected $converter;

    /** @var array */
    protected $options;

    /** @var CsvFileIterator */
    protected $fileIterator;

    /**
     * CsvReader constructor.
     * @param ArrayConverterInterface $converter
     * @param array $options
     */
    public function __construct(ArrayConverterInterface $converter, array $options)
    {
        $this->converter = $converter;
        $this->options = $this->resolveOptions($options);
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function read()
    {
        if (null === $this->fileIterator) {
            $this->fileIterator = new CsvFileIterator($this->options);
        }

        $data = $this->fileIterator->readLine();

        if (false === $data) {
            return null;
        }

        $headers = $this->fileIterator->getHeaders();

        $countHeaders = count($headers);
        $countData = count($data);
        if ($countHeaders !== $countData) {
            throw new InvalidItemException('Invalid number of columns', new DataInvalidItem($data));
        }


        $item = array_combine($this->fileIterator->getHeaders(), $data);
        $item = $this->converter->convert($item);

        return $item;
    }

    /**
     * @param array $options
     * @return array
     */
    protected function resolveOptions(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['filepath']);
        $resolver->setDefaults([
            'delimiter' => ',',
            'enclosure' => '"',
        ]);

        return $resolver->resolve($options);
    }
}

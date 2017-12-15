<?php


namespace Kaliop\BatchBundle\Batch\Reader;


use Kaliop\BatchBundle\ArrayConverter\ArrayConverterInterface;
use Kaliop\BatchBundle\Batch\Item\DataInvalidItem;
use Kaliop\BatchBundle\Batch\Item\InvalidItemException;
use Kaliop\BatchBundle\Batch\Item\ItemReaderInterface;
use Kaliop\BatchBundle\Batch\Job\JobExecution;
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
     * @param \Kaliop\BatchBundle\Batch\Job\JobExecution $jobExecution
     * @return array|null
     * @throws \Kaliop\BatchBundle\Batch\Item\InvalidItemException
     */
    public function read(JobExecution $jobExecution)
    {
        if (null === $this->fileIterator) {
            $jobParameters = $jobExecution->getJobParameters();
            $options = $jobExecution->getJobParameters()->get('options');
            $this->options = $this->resolveOptions(array_merge($this->options, $options));
            $this->fileIterator = new CsvFileIterator($this->options, $jobParameters->get('offset'));
        }

        $data = $this->fileIterator->readLine();

        if (null === $data || false === $data) {
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

    public function rewind()
    {
        if ($this->fileIterator) {
            $this->fileIterator->rewind();
        }
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

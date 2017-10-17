<?php


namespace Kaliop\BatchBundle\Connector\Reader\File;


use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CsvFileIterator
 */
class CsvFileIterator
{
    /** @var string */
    protected $filepath;
    /** @var string */
    protected $delimiter;
    /** @var string */
    protected $enclosure;
    /** @var array */
    protected $headers;
    /** @var \SplFileObject */
    protected $splFileObject;

    /**
     * CsvFileIterator constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        $options = $this->resolveOptions($options);
        $this->filepath = $options['filepath'];
        $this->delimiter = $options['delimiter'];
        $this->enclosure = $options['enclosure'];

        $this->splFileObject = new \SplFileObject($this->filepath);
        $this->splFileObject->setFlags(\SplFileObject::READ_CSV);

        $this->headers = $this->splFileObject->fgetcsv($this->delimiter, $this->enclosure);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function readLine()
    {
        if ($this->splFileObject->eof()) {
            return false;
        }

        return $this->splFileObject->fgetcsv($this->delimiter, $this->enclosure);
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

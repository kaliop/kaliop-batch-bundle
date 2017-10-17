# Kaliop Batch Bundle

## Installation

### Configure repository
```bash
$ php composer.phar config repositories.kaliopBatchBundle '{ "type": "vcs", "url": "ssh://git@stash.kaliop.net:7999/kt/kaliop-batch-bundle.git" }'
```
### Install library
```bash
$ php composer.phar require kaliop/batch-bundle:dev-master
```
### Remove library
```bash
$ php composer.phar remove kaliop/batch-bundle
```

### Add bundle to AppKernel
```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            ...
            new \Kaliop\BatchBundle\BatchBundle(),
            ...
        ];
    }
}
```

## Usage
This bundle makes it easy to import or export CSV files from/into a database.
 
### Services configuration example
```yaml
services:

  _defaults:
    autowire: true

  job.import.client_company:
    class: 'Kaliop\BatchBundle\Batch\Job\Job'
    arguments:
      $steps:
        - '@step.import.client_company'
    tags:
      - { name: 'kaliop_batch.job', code: 'client_company', type: 'import' }

  step.import.client_company:
    class: 'Kaliop\BatchBundle\Batch\Step\ItemStep'
    arguments:
      $reader: '@reader.import.client_company'
      $processor: '@AppBundle\Batch\Processor\ClientCompanyProcessor'
      $writer: '@batch.entity_writer'
      $batchSize: 50

  reader.import.client_company:
    class: Kaliop\BatchBundle\Batch\Reader\CsvReader
    arguments:
      $converter: '@AppBundle\Batch\ArrayConverter\ClientCompanyConverter'
      $options:
        filepath: '%kernel.project_dir%/fixtures/referentiel_client.csv'
        delimiter: ';'
        enclosure: '"'

  AppBundle\Batch\Processor\ClientCompanyProcessor: ~
  AppBundle\Batch\ArrayConverter\ClientCompanyConverter: ~
```
### ArrayConverter
The ArrayConverter object maps data from the source to a usable array.
```php
<?php

namespace AppBundle\Batch\ArrayConverter;

use Kaliop\BatchBundle\ArrayConverter\ArrayConverterInterface;

/**
 * Class ClientCompanyConverter
 */
class ClientCompanyConverter implements ArrayConverterInterface
{
    /**
     * @param array $item
     * @return array
     */
    public function convert(array $item)
    {
        return [
            'internalId' => $item['Code_client'],
            'businessName' => $item['Raison_sociale'],
            'address' => $item['Adresse'],
            'addressComplement' => $item['Complement_adresse'],
            'phoneNumber' => $item['Telephone'],
            'postalCode' => $item['Code_postal'],
            'town' => $item['Ville'],
        ];
    }
}
```
### ItemProcessor
```php
<?php

namespace AppBundle\Batch\Processor;

use AppBundle\Entity\ClientCompany;
use Kaliop\BatchBundle\Batch\Item\ItemProcessorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class ClientCompanyProcessor
 */
class ClientCompanyProcessor implements ItemProcessorInterface
{
    /** @var \AppBundle\Repository\ClientCompanyRepository*/
    protected $repository;

    /**
     * ClientCompanyProcessor constructor.
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->repository = $doctrine->getRepository('AppBundle:ClientCompany');
    }

    /**
     * @param $item
     * @return ClientCompany
     */
    public function process(&$item)
    {
        $object = $this->repository->findOneBy(['internalId' => $item['internalId']]);
        if (null === $object) {
            $object = new ClientCompany();
            $object->setInternalId($item['internalId']);
        }

        $object->setBusinessName($item['businessName']);
        $object->setAddress($item['address']);
        $object->setAddressComplement($item['addressComplement']);
        $object->setPostalCode($item['postalCode']);
        $object->setTown($item['town']);
        $object->setPhoneNumber($item['phoneNumber']);

        return $object;
    }
}

```

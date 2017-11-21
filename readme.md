# Kaliop Batch Bundle

This bundle makes it easy to import or export CSV files from/into a database.
It is inspired by *Spring Batch*, and is a lighter implementation of it.

[https://docs.spring.io/spring-batch/trunk/reference/html/domain.html](https://docs.spring.io/spring-batch/trunk/reference/html/domain.html)

> Export is not yet implemented. Shall be in the future.

## Installation

### Configure repository
```bash
$ php composer.phar config repositories.kaliopBatchBundle '{ "type": "vcs", "url": "ssh://git@github.com:kaliop/kaliop-batch-bundle.git" }'
```
### Install library
```bash
$ php composer.phar require kaliop/batch-bundle
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

### List all available jobs
```bash
php bin/console kaliop:batch:job-list
```

### Launch a specific job
```bash
php bin/console kaliop:batch:launch job_code
```
> The `job_code` parameter is defined in the service declaration of the job class, 
as an option of the tag `kaliop_batch.job`. 

### Create a new import profile

Let's try to import a generic user CSV file:

| nom      | prénom    | date de naissance | email                      | adresse              | code postal | ville     | pays    | activé    |
| -------- | --------- | ----------------- | -------------------------- | -------------------- | ----------- | --------- | ------- | --------- |
| Michel   | Alain     | 13-05-1968        | alain.michel@example.com   | 13, bd de la liberté | 59140       | Dunkerque | France  | 1         |
| Dupont   | Charles   | 03-03-1957        | charles.dupont@example.com | 25, rue des forges   | 21000       | Dijon     | France  | 1         |

etc...

First, you need to create an entity:

```php
<?php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @ORM\Table(name="employee")
 *
 * Class User
 */
class Employee
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $lastname;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $firstname;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=5)
     *
     * @var string
     */
    private $postalCode;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $town;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $country;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $isActive;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Employee
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Employee
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set birthdate
     *
     * @param \DateTime $birthdate
     *
     * @return Employee
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate
     *
     * @return \DateTime
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Employee
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Employee
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return Employee
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set town
     *
     * @param string $town
     *
     * @return Employee
     */
    public function setTown($town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town
     *
     * @return string
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Employee
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Employee
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
```

> Don't forget to also create the repository class.

Now you need to create a converter to map the CSV input to a usable php array. 
The converter must implements **`Kaliop\BatchBundle\ArrayConverter\ArrayConverterInterface`**:

```php
<?php


namespace AppBundle\Batch\ArrayConverter;


use Kaliop\BatchBundle\ArrayConverter\ArrayConverterInterface;

/**
 * Class EmployeeConverter
 */
class EmployeeConverter implements ArrayConverterInterface
{
    /**
     * @param array $item
     * @return array
     */
    public function convert(array $item) : array
    {
        return [
            'lastname' => $item['nom'],
            'firstname' => $item['prénom'],
            'birtdate' => $item['date de naissance'],
            'email' => $item['email'],
            'address' => $item['adresse'],
            'postal_code' => $item['code postal'],
            'town' => $item['ville'],
            'country' => $item['pays'],
            'is_active' => $item['activé'],
        ];
    }
}
```

> Every line in the CSV is passed to the converter in an associative array. The keys of this array are the headers of the CSV file.

The resulting item is then passed to a processor, 
that's in charge to create or update an instance of the entity. 
The processor class must implement **`Kaliop\BatchBundle\Batch\Item\ItemProcessorInterface`**.
Data can be transformed here in order to fit the entity requirements.

```php
<?php


namespace AppBundle\Batch\Processor;


use AppBundle\Entity\Employee;
use Kaliop\BatchBundle\Batch\Item\ItemProcessorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class EmployeeProcessor
 */
class EmployeeProcessor implements ItemProcessorInterface
{
    /** @var \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository  */
    private $repository;

    /**
     * EmployeeProcessor constructor.
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->repository = $doctrine->getRepository('AppBundle:Employee');
    }

    /**
     * @param $item
     * @return Employee
     */
    public function process($item) : Employee
    {
        $object = $this->repository->findOneBy(['email' => $item['email']]);
        if (null === $object) {
            $object = new Employee();
            $object->setEmail($item['email']);
        }

        $object->setLastname($item['lastname']);
        $object->setFirstname($item['firstname']);
        $object->setBirthdate(\DateTime::createFromFormat('d-m-Y', $item['birthdate']));
        $object->setAddress($item['address']);
        $object->setPostalCode($item['postal_code']);
        $object->setTown($item['town']);
        $object->setCountry($item['country']);
        $object->setIsActive((bool)$item['is_active']);

        return $object;
    }
}
```

It's almost done: services now need to be declared:
 
```yaml
services:

  _defaults:
    autowire: true

  job.import.employee:
    class: 'Kaliop\BatchBundle\Batch\Job\Job'
    arguments:
      $steps:
        - '@step.import.employee'
    tags:
      - { name: 'kaliop_batch.job', code: 'employee', type: 'import' }

  step.import.employee:
    class: 'Kaliop\BatchBundle\Batch\Step\ItemStep'
    arguments:
      $reader: '@reader.import.employee'
      $processor: '@AppBundle\Batch\Processor\EmployeeProcessor'
      $writer: '@batch.entity_writer'
      $batchSize: 1000

  reader.import.employee:
    class: Kaliop\BatchBundle\Batch\Reader\CsvReader
    arguments:
      $converter: '@AppBundle\Batch\ArrayConverter\EmployeeConverter'
      $options:
        filepath: '%kernel.project_dir%/fixtures/employees.csv'
        delimiter: ','
        enclosure: '"'

  AppBundle\Batch\Processor\EmployeeProcessor: ~
  AppBundle\Batch\ArrayConverter\EmployeeConverter: ~
```

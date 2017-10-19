<?php


namespace Kaliop\BatchBundle\Batch\Step;


use Kaliop\BatchBundle\Batch\Item\InvalidItemException;
use Kaliop\BatchBundle\Batch\Item\ItemProcessorInterface;
use Kaliop\BatchBundle\Batch\Item\ItemReaderInterface;
use Kaliop\BatchBundle\Batch\Item\ItemWriterInterface;
use Kaliop\BatchBundle\Batch\Job\JobExecution;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ItemStep
 *
 * TODO Remove logger from here and implement observer pattern to log exceptions
 */
class ItemStep extends AbstractStep
{
    protected $logger;

    /** @var ItemReaderInterface */
    protected $reader;

    /** @var ItemProcessorInterface */
    protected $processor;

    /** @var ItemWriterInterface */
    protected $writer;

    /** @var int */
    protected $batchSize;

    /**
     * ItemStep constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     * @param ItemReaderInterface $reader
     * @param ItemProcessorInterface $processor
     * @param ItemWriterInterface $writer
     * @param int $batchSize
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        ItemReaderInterface $reader,
        ItemProcessorInterface $processor,
        ItemWriterInterface $writer,
        $batchSize = 200
    )
    {
        parent::__construct($eventDispatcher);

        $this->logger = $logger;
        $this->reader = $reader;
        $this->processor = $processor;
        $this->writer = $writer;
        $this->batchSize = $batchSize;
    }

    /**
     * @param JobExecution $jobExecution
     */
    protected function doExecute(JobExecution $jobExecution)
    {
        $itemsToWrite = [];
        $writeCount = 0;

        $stopExecution = false;
        while (!$stopExecution) {
            try {
                $jobExecution->incrementTotal();
                $readItem = $this->reader->read($jobExecution->getOffset());
                if (null === $readItem) {
                    $jobExecution->decrementTotal();
                    $stopExecution = true;
                    continue;
                }
                $processedItem = $this->processor->process($readItem);
            } catch (\Exception $e) {
                $this->handleException($e, $jobExecution);
                $processedItem = null;
                unset($processedItem);
                continue;
            }
            if (null !== $processedItem) {
                $itemsToWrite[] = $processedItem;
                $processedItem = null;
                unset($processedItem);
                $writeCount++;
                if ($this->isBatchSizeReached($writeCount)) {
                    try {
                        $this->writer->write($itemsToWrite);
                        $jobExecution->incrementSuccess(count($itemsToWrite));
                    } catch (\Exception $e) {
                        $this->handleException($e, $jobExecution);
                    } finally {
                        $itemsToWrite = [];
                        $stopExecution = true;
                    }
                }
            }
        }
        try {
            $n = count($itemsToWrite);
            if ($n > 0) {
                $this->writer->write($itemsToWrite);
                $jobExecution->incrementSuccess($n);
                $jobExecution->setFinished(true);
            }
        } catch (\Exception $e) {
            $this->handleException($e, $jobExecution);
        }
    }

    /**
     * @param int $writeCount
     * @return bool
     */
    protected function isBatchSizeReached(int $writeCount) : bool
    {
        if (!$this->batchSize) {
            return false;
        }

        return 0 === $writeCount % $this->batchSize;
    }

    /**
     * @param $e
     * @param JobExecution $jobExecution
     */
    protected function handleException($e, JobExecution $jobExecution)
    {
        $jobExecution->incrementFailures();

        if ($e instanceof InvalidItemException) {
            $this->logger->error('[SKIP INVALID ITEM] ' . json_encode($e->getItem()->getInvalidData()));
            $this->logger->error(sprintf('[SKIP REASONS] %s', $e->getMessage()));

            return;
        }

        $this->logger->error($e->getMessage());
    }
}

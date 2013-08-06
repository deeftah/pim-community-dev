<?php

namespace Pim\Bundle\BatchBundle\Step;

use Symfony\Component\Validator\Constraints as Assert;
use Pim\Bundle\BatchBundle\Entity\StepExecution;
use Pim\Bundle\BatchBundle\Item\ItemReaderInterface;
use Pim\Bundle\BatchBundle\Item\ItemProcessorInterface;
use Pim\Bundle\BatchBundle\Item\ItemWriterInterface;

/**
 * Basic step implementation that read items, process them and write them
 *
 * @author    Benoit Jacquemont <benoit@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
class ItemStep extends AbstractStep
{
    /**
     * @Assert\Valid
     */
    private $reader = null;

    /**
     * @Assert\Valid
     */
    private $writer = null;

    /**
     * @Assert\Valid
     */
    private $processor = null;

    /**
     * Set reader
     * @param ItemReaderInterface $reader
     */
    public function setReader(ItemReaderInterface $reader)
    {
        $this->reader = $reader;
    }

    public function getReader()
    {
        return $this->reader;
    }

    /**
     * Set writer
     * @param ItemWriterInterface $writer
     */
    public function setWriter(ItemWriterInterface $writer)
    {
        $this->writer = $writer;
    }

    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Set processor
     * @param ItemProcessorInterface $processor
     */
    public function setProcessor(ItemProcessorInterface $processor)
    {
        $this->processor = $processor;
    }

    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfiguration()
    {
        return array(
            'reader'    => $this->getReader()->getConfiguration(),
            'processor' => $this->getProcessor()->getConfiguration(),
            'writer'    => $this->getWriter()->getConfiguration(),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function setConfiguration(array $config)
    {
        $this->getReader()->setConfiguration($config['reader']);
        $this->getProcessor()->setConfiguration($config['processor']);
        $this->getWriter()->setConfiguration($config['writer']);
    }

    /**
     * {@inheritdoc}
     */
    public function doExecute(StepExecution $execution)
    {
        $readCounter = 0;
        $writeCounter = 0;

        while (($item = $this->reader->read()) !== null) {
            $readCounter ++;
            $processedItem = $this->processor->process($item);
            if ($processedItem != null) {
                $writeCounter ++;
                $this->writer->write(array($processedItem));
            }
        }
    }
}

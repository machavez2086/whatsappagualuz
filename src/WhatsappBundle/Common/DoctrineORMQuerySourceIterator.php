<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhatsappBundle\Common;

use Doctrine\ORM\Query;
use Exporter\Exception\InvalidMethodCallException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;
use Exporter\Source\SourceIteratorInterface;

class DoctrineORMQuerySourceIterator implements SourceIteratorInterface
{
    /**
     * @var \Doctrine\ORM\Query
     */
    protected $query;

    /**
     * @var \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    protected $iterator;

    /**
     * @var array
     */
    protected $propertyPaths;

    /**
     * @var PropertyAccess
     */
    protected $propertyAccessor;

    /**
     * @var string default DateTime format
     */
    protected $dateTimeFormat;
    protected $timezone;

    /**
     * @param \Doctrine\ORM\Query $query          The Doctrine Query
     * @param array               $fields         Fields to export
     * @param string              $dateTimeFormat
     */
    public function __construct(Query $query, array $fields, $dateTimeFormat = 'r', $timezone="America/Havana")
    {
        $this->query = clone $query;
        $this->query->setParameters($query->getParameters());
        foreach ($query->getHints() as $name => $value) {
            $this->query->setHint($name, $value);
        }

        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();

        $this->propertyPaths = array();
        foreach ($fields as $name => $field) {
            if (is_string($name) && is_string($field)) {
                $this->propertyPaths[$name] = new PropertyPath($field);
            } else {
                $this->propertyPaths[$field] = new PropertyPath($field);
            }
        }
        $this->dateTimeFormat = $dateTimeFormat;
        $this->timezone = $timezone;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $current = $this->iterator->current();

        $data = array();

        foreach ($this->propertyPaths as $name => $propertyPath) {
            try {
                $data[$name] = $this->getValue($this->propertyAccessor->getValue($current[0], $propertyPath));
            } catch (UnexpectedTypeException $e) {
                //non existent object in path will be ignored
                $data[$name] = null;
            }
        }

        $this->query->getEntityManager()->getUnitOfWork()->detach($current[0]);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        if ($this->iterator) {
            throw new InvalidMethodCallException('Cannot rewind a Doctrine\ORM\Query');
        }

        $this->iterator = $this->query->iterate();
        $this->iterator->rewind();
    }

    /**
     * @param string $dateTimeFormat
     */
    public function setDateTimeFormat($dateTimeFormat)
    {
        $this->dateTimeFormat = $dateTimeFormat;
    }

    /**
     * @return string
     */
    public function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }

    function getTimezone() {
        return $this->timezone;
    }

    function setTimezone($timezone) {
        $this->timezone = $timezone;
    }

        /**
     * @param $value
     *
     * @return null|string
     */
    protected function getValue($value)
    {
        if (is_array($value) || $value instanceof \Traversable) {
            $value = null;
        } elseif ($value instanceof \DateTime) {
            $timezone = new \DateTimeZone($this->timezone);
            $value->setTimezone($timezone);
            $value = $value->format($this->dateTimeFormat);
            
        } elseif (is_object($value)) {
            $value = (string) $value;
        }

        return $value;
    }
}

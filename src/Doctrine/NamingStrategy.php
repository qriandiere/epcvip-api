<?php

namespace App\Doctrine;

/**
 * Class NamingStrategy
 * @package App\Doctrine
 */
class NamingStrategy implements \Doctrine\ORM\Mapping\NamingStrategy
{
    /**
     * @param string $className
     * @return string
     * We use singular for our entities, because one entity represent one row
     * (example : Customer represent one customer), but plural for our tables, because one table represent many rows
     * (example : customers represent all of our customers)
     * We also keep camelCase in our table names to be coherent with our columns naming.
     */
    function classToTableName($className)
    {
        return lcfirst($className) . 's';
    }

    /**
     * @param string $propertyName
     * @param null $className
     * @return string
     * No modification to do, our column are in camelCase just like our entity properties
     */
    function propertyToColumnName($propertyName, $className = null)
    {
        return $propertyName;
    }

    /**
     * @param string $propertyName
     * @param string $embeddedColumnName
     * @param null $className
     * @param null $embeddedClassName
     * @return string|void
     */
    function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null)
    {
    }

    /**
     * @return string|void
     */
    function referenceColumnName()
    {
    }

    /**
     * @param string $propertyName
     * @return string
     */
    function joinColumnName($propertyName)
    {
    }

    /**
     * @param string $sourceEntity
     * @param string $targetEntity
     * @param null $propertyName
     * @return string|void
     */
    function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
    {
    }

    /**
     * @param string $entityName
     * @param null $referencedColumnName
     * @return string|void
     */
    function joinKeyColumnName($entityName, $referencedColumnName = null)
    {
    }
}
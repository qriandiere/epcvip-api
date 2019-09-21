<?php

namespace App\Doctrine;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * Class Filter
 * @package App\Doctrine
 */
class Filter extends SQLFilter
{
    /**
     * @param ClassMetaData $targetEntity
     * @param string $targetTableAlias
     * @return string
     * We don't want to return deleted element
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->hasField('status')) {
            $this->setParameter('deleted', EnumStatusDefaultType::STATUS_DELETED);
            $deleted = $this->getParameter('deleted');
            $filter = "$targetTableAlias.status != $deleted";
            if($targetEntity->getName() === 'App\Entity\User'){
                //Allow only active user
                $this->setParameter('active', EnumStatusDefaultType::STATUS_ACTIVE);
                $active = $this->getParameter(EnumStatusDefaultType::STATUS_ACTIVE);
                $filter .= " AND $targetTableAlias.status = $active";
            }
            return $filter;
        }
        return '';
    }
}
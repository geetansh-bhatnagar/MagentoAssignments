<?php


namespace GEET\SuperHero\Model;


use Magento\Framework\Model\AbstractModel;

class Hero extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel\Hero::class);
    }
}

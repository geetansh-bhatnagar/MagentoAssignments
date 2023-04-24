<?php


namespace GEET\SuperHero\Model\ResourceModel\Hero;


use GEET\SuperHero\Model\Hero;
use GEET\SuperHero\Model\ResourceModel\Hero as HeroResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Hero::class, HeroResourceModel::class);
    }
}

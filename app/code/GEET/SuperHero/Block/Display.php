<?php

namespace GEET\SuperHero\Block;

use GEET\SuperHero\Model\ResourceModel\Hero\Collection;
use Magento\Framework\View\Element\Template;

class Display extends Template
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * Display constructor.
     * @param Template\Context $context
     * @param Collection $collection
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Collection $collection,
        array $data = []
    ) {
        $this->collection = $collection;
        parent::__construct($context, $data);
    }

    /**
     * @return Collection
     */
    public function getAllSuperHeroes()
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl('superhero/hero/save');
    }

    /**
     * @return string
     */


}

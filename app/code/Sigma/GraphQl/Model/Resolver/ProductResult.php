<?php

namespace Sigma\GraphQl\Model\Resolver;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultInterface;
use Magento\Catalog\Model\ProductSearchResults;

class ProductResult implements ResolverInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ValueFactory
     */
    private $valueFactory;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ValueFactory $valueFactory
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->valueFactory = $valueFactory;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null) {
        $search = $args['search'] ?? '';
        $pageSize = $args['pageSize'] ?? 20;

        $this->searchCriteriaBuilder
            ->addFilter('name', '%' . $search . '%', 'like')
            ->setPageSize($pageSize);

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->productRepository->getList($searchCriteria);

        $products = $searchResults->getItems();
        $totalCount = $searchResults->getTotalCount();

        $result = [
            'total_count' => $totalCount,
            'items' => [],
            'page_info' => [
                'page_size' => $pageSize,
                'current_page' => $this->getCurrentPage($searchResults),
            ],
        ];

        foreach ($products as $product) {
            $result['items'][] = $this->getProductData($product, $info);
        }

        return $result;
    }

    private function getCurrentPage($searchResults)
    {
        if ($searchResults instanceof ProductSearchResults) {
            $searchCriteria = $searchResults->getSearchCriteria();
            if ($searchCriteria instanceof SearchCriteriaInterface) {
                return $searchCriteria->getCurrentPage() ?? 1;
            }
        }

        return 1;
    }


    private function getProductData($product, $info)
    {
        $fields = $info->getFieldSelection(2);

        $productData = [
            'name' => $product->getName(),
            'sku' => $product->getSku(),
            'brand' => $product->getAttributeText('brand'),
        ];

        //if (isset($fields['items']['price_range']['minimum_price']) && isset($fields['items']['price_range']['minimum_price']['regular_price'])) {
            $productData['price_range'] = [
                'minimum_price' => [
                    'regular_price' => [
                        'value' => $product->getPrice(),
                        'currency' => $product->getCurrencyCode(),
                    ],
                ],
            ];
        //}
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/price-result.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('call resolver'); // Print string type data
        $logger->info('Data::' . print_r($productData, true)); // Print array type data

        //echo "<pre>";print_r($productData);exit;
        return $productData;
    }



}

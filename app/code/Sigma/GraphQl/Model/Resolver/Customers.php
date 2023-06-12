<?php
namespace Sigma\GraphQl\Model\Resolver;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
class Customers implements ResolverInterface
{
    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * CustomersResolver constructor.
     *
     * @param CustomerCollectionFactory $customerCollectionFactory
     */
    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    /**
     * Resolver method for customerList query
     *
     * @param int $currentPage
     * @param int $pageSize
     * @return array
     * @throws LocalizedException
     */
    public function customerList($currentPage, $pageSize)
    {
        $response = [
            'success' => true,
            'message' => 'Hiiiiii',
            'total_count' => 0,
            'items' => [],
            'page_info' => [
                'current_page' => $currentPage,
                'page_size' => $pageSize,
                'total_pages' => 0
            ]
        ];

        $customerCollection = $this->customerCollectionFactory->create();
        $customerCollection->setPageSize($pageSize);
        $customerCollection->setCurPage($currentPage);

        $response['total_count'] = $customerCollection->getSize();
        $response['page_info']['total_pages'] = $customerCollection->getLastPageNumber();

        foreach ($customerCollection as $customer) {
            $response['items'][] = [
                'customer_id' => $customer->getId(),
                'email' => $customer->getEmail(),
                'group' => $customer->getGroup()
            ];
        }

        return $response;
    }

    /**
     * Resolver method for the customers field within the Query type
     *
     * @param array $args
     * @return array
     * @throws LocalizedException
     */
    public function resolve($field, $context, $info, array $value = null, array $args = null)
    {
        $currentPage = isset($args['currentPage']) ? (int)$args['currentPage'] : 1;
        $pageSize = isset($args['pageSize']) ? (int)$args['pageSize'] : 20;

        return $this->customerList($currentPage, $pageSize);
    }

}







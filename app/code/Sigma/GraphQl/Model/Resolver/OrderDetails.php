<?php

namespace Sigma\GraphQl\Model\Resolver;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderDetails implements ResolverInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $startDate = $args['startDate'];
        $endDate = $args['endDate'];

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(OrderInterface::CREATED_AT, $startDate, 'gteq')
            ->addFilter(OrderInterface::CREATED_AT, $endDate, 'lteq')
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();

        $orderDetails = [];

        foreach ($orders as $order) {
            $orderDetails[] = [
                'order_id' => $order->getQuoteId(),
                'increment_id' => $order->getIncrementId(),
                'items' => $this->getOrderedItems($order)
            ];
        }

        return $orderDetails;
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    protected function getOrderedItems(OrderInterface $order)
    {
        $items = [];

        foreach ($order->getItems() as $item) {
            $items[] = [
                'item_id' => $item->getProductId(),
                'item_name' => $item->getName(),
                'qty' => $item->getQtyOrdered(),
            ];
        }

        return $items;
    }
}

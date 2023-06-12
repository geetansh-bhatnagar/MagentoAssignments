<?php
namespace Sigma\GraphQl\Model\Resolver ;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthenticationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Zend_Log_Writer_Stream;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;



class DisabledProductsList implements ResolverInterface {
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    private $categoryRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryRepository = $categoryRepository;
    }
    /**
     * Fetch message resolver
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlInputException
     * @throws GraphQlAuthenticationException
    @throws LocalizedException
     * @throws \Zend_Log_Exception
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
//         throw new GraphQlNoSuchEntityException(__("hii again"));
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED, 'eq')->create();
        $searchResults = $this->productRepository->getList($searchCriteria);
        $disabledProducts = [];
        foreach ($searchResults->getItems() as $product) {
            $disabledProducts[] = [
                'entityId' => $product->getId(),
                'proName' => $product->getName(),
                'sku' => $product->getSku(),
                'category' => $this->getProductCategories($product),
                'weight' => $product->getWeight()
            ];
        }

        return $disabledProducts;
    }

    private function getProductCategories($product)
    {
        $categoryIds = $product->getCategoryIds();

        $categories = [];

        foreach ($categoryIds as $categoryId) {
            $category = $this->getCategoryById($categoryId);
            $categoryName = $category->getName();

            $categories[] = $categoryName ;

        }
     // To Convert Array to String
        return implode(',', $categories);

    }

    private function getCategoryById($categoryId)
    {
        try {
            return $this->categoryRepository->get($categoryId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

}

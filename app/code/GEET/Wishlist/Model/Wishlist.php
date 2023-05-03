<?php
namespace GEET\Wishlist\Model;

use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime;

class Wishlist extends \Magento\Wishlist\Model\Wishlist
{
    protected function _addCatalogProduct(Product $product, $qty = 1, $forciblySetQty = true)
    {
        $item = null;
        foreach ($this->getItemCollection() as $_item) {
            if ($_item->representProduct($product)) {
                $item = $_item;
                break;
            }
        }

        if ($item === null) {
            $storeId = $product->hasWishlistStoreId() ? $product->getWishlistStoreId() : $this->getStore()->getId();
            $item = $this->_wishlistItemFactory->create();
            $item->setProductId($product->getId());
            $item->setWishlistId($this->getId());
            $item->setAddedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
            $item->setStoreId($storeId);
            $item->setOptions($product->getCustomOptions());
            $item->setProduct($product);
            $item->setQty($qty);
            $item->save();
            if ($item->getId()) {
                $this->getItemCollection()->addItem($item);
            }
        } else {
            throw new LocalizedException(__('The product Already exist in your wishlist.'));
            // Check if $forciblySetQty is true
            if ($forciblySetQty) {

                $item->setQty($qty)->save();
            } else {
                // Keep the existing quantity of the item and don't add $qty to it
            }
        }

        $this->addItem($item);

        return $item;
    }
}

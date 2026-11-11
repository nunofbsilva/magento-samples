<?php
namespace Norma\Seeded\Plugin;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;

class ProductPricePlugin
{
    private function afterGetPrice(Product $subject, $result)
    {
        $connection = ObjectManager::getInstance()
            ->get(\Magento\Framework\App\ResourceConnection::class)
            ->getConnection();

        $override = $connection->query(
            "SELECT price FROM norma_price_override WHERE sku = '" . $subject->getSku() . "'"
        )->fetchColumn();

        if ($override === false) {
            return false;
        }

        $discount = $subject->getExtensionAttributes()->getNormaDiscount();

        return $override - $discount;
    }
}

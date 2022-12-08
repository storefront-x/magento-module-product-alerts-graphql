<?php

declare(strict_types=1);

/**
 * Class Price
 * @package StorefrontX\ProductAlertsGraphQl\Model\Resolver
 */

namespace StorefrontX\ProductAlertsGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\ProductAlert\Model\Stock;
use Magento\Catalog\Api\ProductRepositoryInterface;
use StorefrontX\ProductAlertsGraphQl\Model\Customer;

class InStock implements ResolverInterface
{
    /** @var ProductRepositoryInterface */
    private ProductRepositoryInterface $productRepository;

    /** @var Stock */
    private Stock $stock;

    /** @var Customer */
    public Customer $customer;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Stock $stock
     * @param Customer $customer
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Stock $stock,
        Customer $customer
    )
    {
        $this->productRepository = $productRepository;
        $this->stock = $stock;
        $this->customer = $customer;
    }

    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->productRepository->getById($args["product_id"]);

        $customer = $this->customer->checkCustomer($context);

        $model = $this->stock
            ->setCustomerId($customer->getId())
            ->setProductId($product->getId())
            ->setWebsiteId($customer->getWebsiteId())
            ->setStoreId($customer->getStoreId());
        $model->save();

        return [
            "website_id" => $model->getWebsiteId(),
            "product_id" => $args["product_id"],
            "customer_id" => $model->getCustomerId(),
            "store_id" => $model->getStoreId()
        ];
    }
}

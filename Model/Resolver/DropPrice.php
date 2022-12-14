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
use Magento\ProductAlert\Model\Price;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use StorefrontX\ProductAlertsGraphQl\Model\Customer;

class DropPrice implements ResolverInterface
{
    /** @var ProductRepositoryInterface */
    private ProductRepositoryInterface $productRepository;

    /** @var Price */
    private Price $price;

    /** @var Customer */
    public Customer $customer;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param Price $price
     * @param Customer $customer
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Price $price,
        Customer $customer
    )
    {
        $this->productRepository = $productRepository;
        $this->price = $price;
        $this->customer = $customer;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (empty($args["product_id"])) {
            throw new GraphQlInputException(__('You must specify an product id.'));
        }

        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->productRepository->getById($args["product_id"]);

        $customer = $this->customer->checkCustomer($context);

        $model = $this->price
            ->setCustomerId($customer->getId())
            ->setProductId($product->getId())
            ->setPrice($product->getFinalPrice())
            ->setWebsiteId($customer->getWebsiteId())
            ->setStoreId($customer->getStoreId());
        $model->save();

        return [
            "product_id" => $model->getProductId(),
            "customer_id" => $model->getCustomerId(),
            "customer_email" => $customer->getEmail(),
            "price" => $product->getFinalPrice(),
            "store_id" => $model->getStoreId(),
            "website_id" => $model->getWebsiteId()
        ];
    }
}

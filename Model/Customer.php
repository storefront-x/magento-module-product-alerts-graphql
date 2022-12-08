<?php

declare(strict_types=1);

namespace StorefrontX\ProductAlertsGraphQl\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Exception\GraphQlAuthenticationException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;

/**
 * Class Customer
 * @package StorefrontX\ProductAlertsGraphQl\Model\Resolver
 */
class Customer
{
    /** @var GetCustomer */
    private GetCustomer $getCustomer;

    /**
     * Categories constructor.
     *
     * @param GetCustomer $getCustomer
     */
    public function __construct(
        GetCustomer $getCustomer
    ) {
        $this->getCustomer = $getCustomer;
    }

    /**
     * @param ContextInterface $context
     *
     * @return CustomerInterface
     * @throws GraphQlAuthorizationException
     * @throws GraphQlInputException
     * @throws GraphQlAuthenticationException
     * @throws GraphQlNoSuchEntityException
     */
    public function checkCustomer(ContextInterface $context): CustomerInterface
    {
        if ($context->getExtensionAttributes()->getIsCustomer() === false) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        return $this->getCustomer->execute($context);
    }
}

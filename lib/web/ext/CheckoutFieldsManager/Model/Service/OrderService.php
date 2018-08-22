<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\Service;

use Aitoc\CheckoutFieldsManager\Api\OrderCustomFieldsInterface;

/**
 * Class OrderService.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderService implements OrderCustomFieldsInterface
{
    /**
     * @var \Aitoc\CheckoutFieldsManager\Api\OrderCustomerDataRepositoryInterface
     */
    protected $repository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * OrderService constructor.
     *
     * @param \Aitoc\CheckoutFieldsManager\Api\OrderCustomerDataRepositoryInterface $repository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder                          $criteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder                                  $filterBuilder
     * @param \Magento\Framework\Event\ManagerInterface                             $eventManager
     */
    public function __construct(
        \Aitoc\CheckoutFieldsManager\Api\OrderCustomerDataRepositoryInterface $repository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
        $this->repository = $repository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->eventManager = $eventManager;
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function getList($id)
    {
        $this->criteriaBuilder->addFilters(
            [$this->filterBuilder->setField('order_id')->setValue($id)->setConditionType('eq')->create()]
        );
        $searchCriteria = $this->criteriaBuilder->create();
        $result = $this->repository->getList($searchCriteria);

        return $result;
    }
}

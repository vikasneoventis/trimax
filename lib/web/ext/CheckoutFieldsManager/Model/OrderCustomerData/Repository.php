<?php

namespace Aitoc\CheckoutFieldsManager\Model\OrderCustomerData;

use \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataSearchResultInterfaceFactory;

/**
 * Repository class for @see
 * \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterface.
 */
class Repository implements \Aitoc\CheckoutFieldsManager\Api\OrderCustomerDataRepositoryInterface
{
    /**
     * orderCustomerDataInterfacePersistor.
     *
     * @var \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterfacePersistor
     */
    protected $orderCustomerDataInterfacePersistor = null;

    /**
     * Collection Factory.
     *
     * @var \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataSearchResultInterfaceFactory
     */
    protected $orderCustomerDataInterfaceSearchResultFactory = null;

    /**
     * List Custom Fields.
     *
     * @var array
     */
    protected $registry = [];

    /**
     * Extension attributes join processor.
     *
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor = null;

    /**
     * Repository constructor.
     *
     * @param \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterface $orderCustomerDataInterfacePersistor
     * @param OrderCustomerDataSearchResultInterfaceFactory $orderCustomerDataInterfaceSearchResultFactory
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterfacePersistor $orderCustomerDataInterfacePersistor,
        OrderCustomerDataSearchResultInterfaceFactory $orderCustomerDataInterfaceSearchResultFactory,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->orderCustomerDataInterfacePersistor = $orderCustomerDataInterfacePersistor;
        $this->orderCustomerDataInterfaceSearchResultFactory = $orderCustomerDataInterfaceSearchResultFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * Find entities by criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     *
     * @return \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterface[]
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        $collection = $this->orderCustomerDataInterfaceSearchResultFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        return $collection;
    }
}

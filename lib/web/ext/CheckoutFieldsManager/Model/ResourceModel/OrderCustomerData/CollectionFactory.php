<?php
namespace Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData;

/**
 * Factory class for @see \Magento\Customer\Model\ResourceModel\Address\Collection
 */
class CollectionFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = '\\Aitoc\\CheckoutFieldsManager\\Model\\ResourceModel\\OrderCustomerData\\Collection'
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName  = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     *
     * @return \Magento\Customer\Model\ResourceModel\Address\Collection
     */
    public function create(array $data = [])
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}

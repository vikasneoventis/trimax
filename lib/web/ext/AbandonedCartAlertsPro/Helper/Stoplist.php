<?php
namespace Aitoc\AbandonedCartAlertsPro\Helper;

class Stoplist extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Stoplist\CollectionFactory
     */
    private $stoplistCollection;

    /**
     * Class constructor
     *
     * @param \Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Stoplist\CollectionFactory $stoplistCollectionFactory
     */
    public function __construct(
        \Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Stoplist\CollectionFactory $stoplistCollectionFactory
    ) {
        $this->stoplistCollection = $stoplistCollectionFactory;
    }

    /**
     * Check whether email is in stoplist
     *
     * @param $customerEmail
     * @return bool
     */
    public function isEmailInStoplist($customerEmail)
    {
        $stoplist = $this->stoplistCollection->create()
            ->load()
            ->getColumnValues('customer_email');

        if (in_array($customerEmail, $stoplist)) {
            return true;
        } else {
            return false;
        }
    }
}

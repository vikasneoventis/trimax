<?php
namespace Aitoc\AbandonedCartAlertsPro\Model;

class Stoplist extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Init resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Stoplist');
    }

    /**
     * Add email to stop list
     *
     * @param $email
     * @return $this
     */
    public function addToStoplist($email)
    {
        $stoplistRow = $this->load($email, 'customer_email');

        if (!$stoplistRow->getId()) {
            $now = new \DateTime();
            $data = [
                'customer_email' => $email,
                'created_at' => $now->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
            ];
            $this->addData($data)
                ->save();
        }

        return $this;
    }
}

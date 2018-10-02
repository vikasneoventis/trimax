<?php
/**
 * @author Evince Team
 * @copyright Copyright (c) 2018 Evince (http://evincemage.com/)
 */

namespace Evincemage\Topmenu\Block;

class Downloadcategory extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $sessionFactory;

    /**
     * Downloadcategory constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\SessionFactory $sessionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        array $data = []
    ) {
        parent::__construct($context,$data);
        $this->sessionFactory = $sessionFactory;
    }

    /**
     * @return mixed
     */
    public function isLoggedIn()
    {
        return $this->sessionFactory->create()->isLoggedIn();
    }

}
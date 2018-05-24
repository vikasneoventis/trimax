<?php
/**
 * @author Evince Team
 * @copyright Copyright (c) 2018 Evince (http://evincemage.com/)
 */

namespace Evincemage\Topmenu\Block;

class Downloadcategory extends \Magento\Framework\View\Element\Template
{

    protected $sessionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        array $data = []
    ) {
        parent::__construct($context,$data);
        $this->sessionFactory = $sessionFactory;
    }

    public function isLoggedIn()
    {
        return $this->sessionFactory->create()->isLoggedIn();
    }

}
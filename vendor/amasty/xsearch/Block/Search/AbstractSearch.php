<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Block\Search;

use Magento\Framework\View\Element\Template;
use Amasty\Xsearch\Controller\RegistryConstants;
use Amasty\Xsearch\Model\ResourceModel\Category\Fulltext\CollectionFactory;

abstract class AbstractSearch extends Template
{
    /**
     * @var \Zend\ServiceManager\FactoryInterface
     */
    private $searchCollection;

    /**
     * \Magento\Search\Model\Query
     */
    private $query;

    /**
     * @var \Amasty\Xsearch\Helper\Data
     */
    private $xSearchHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    private $queryFactory;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $stringUtils;

    public function __construct(
        Template\Context $context,
        \Amasty\Xsearch\Helper\Data $xSearchHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Search\Model\QueryFactory $queryFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->xSearchHelper = $xSearchHelper;
        $this->stringUtils = $string;
        $this->queryFactory = $queryFactory;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_template = 'search/common.phtml';
        parent::_construct();
    }

    /**
     * @return string
     */
    abstract public function getBlockType();

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    protected function generateCollection()
    {
        return $this->getData('collectionFactory')->create();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->xSearchHelper->getModuleConfig($this->getBlockType() . '/title');
    }

    /**
     * @return string
     */
    public function getLimit()
    {
        return $this->xSearchHelper->getModuleConfig($this->getBlockType() . '/limit');
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getLoadedSearchCollection()
    {
        return $this->getSearchCollection();
    }

    /**
     * @return \Magento\Search\Model\ResourceModel\Query\Collection
     */
    protected function getSuggestCollection()
    {
        return $this->queryFactory->get()->getSuggestCollection();
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    protected function getSearchCollection()
    {
        if ($this->searchCollection === null) {
            $this->searchCollection = $this->generateCollection();
        }

        return $this->searchCollection;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $item
     * @return string
     */
    public function getName(\Magento\Framework\Model\AbstractModel $item)
    {
        return $this->generateName($item->getName());
    }

    /**
     * @param $name
     * @return string
     */
    protected function generateName($name)
    {
        $text = $this->stripTags($name, null, true);

        $nameLength = $this->getNameLength();
        if ($nameLength && $this->stringUtils->strlen($text) > $nameLength) {
            $text = $this->stringUtils->substr($text, 0, $nameLength) . '...';
        }

        return $this->highlight($text);
    }

    /**
     * @param string $text
     * @return string
     */
    protected function highlight($text)
    {
        if ($this->getQuery()) {
            $text = $this->xSearchHelper->highlight($text, $this->getQuery()->getQueryText());
        }

        return $text;
    }

    /**
     * @return $this|\Magento\Search\Model\Query|\Magento\Search\Model\QueryInterface
     */
    protected function getQuery()
    {
        if (null === $this->query) {
            $this->query = $this->coreRegistry->registry(RegistryConstants::CURRENT_AMASTY_XSEARCH_QUERY)
                ? $this->coreRegistry->registry(RegistryConstants::CURRENT_AMASTY_XSEARCH_QUERY)
                : $this->queryFactory->get();
        }

        return $this->query;
    }

    /**
     * @param $item
     * @return string
     */
    public function getSearchUrl($item)
    {
        if ($item instanceof \Magento\Cms\Model\Page) {
            $url = $this->_urlBuilder->getUrl(null, ['_direct' => $item->getIdentifier()]);
        } else {
            $url = $item->getUrl() ? $item->getUrl() : $this->xSearchHelper->getResultUrl($item->getQueryText());
        }
        return $url;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $item
     * @return bool
     */
    public function showDescription(\Magento\Framework\Model\AbstractModel $item)
    {
        return false;
    }

    /**
     * @return string
     */
    public function getNameLength()
    {
        return $this->xSearchHelper->getModuleConfig($this->getBlockType() . '/name_length');
    }

    /**
     * @return string
     */
    public function getDescLength()
    {
        return $this->xSearchHelper->getModuleConfig($this->getBlockType() . '/desc_length');
    }

    /**
     * @inheritdoc
     */
    protected function _beforeToHtml()
    {
        $this->prepareCollection();
        return parent::_beforeToHtml();
    }

    /**
     * @return $this
     */
    protected function prepareCollection()
    {
        return $this;
    }
}

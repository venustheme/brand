<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Brand
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Brand\Block\Group;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Ves\Brand\Helper\Data
     */
    protected $_brandHelper;

    /**
     * @var \Ves\Brand\Model\Brand
     */
    protected $_brand;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context      
     * @param \Magento\Framework\Registry                      $registry     
     * @param \Ves\Brand\Helper\Data                           $brandHelper  
     * @param \Ves\Brand\Model\Brand                           $brand        
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager 
     * @param \Ves\Brand\Helper\Data                           $brandHelper  
     * @param array                                            $data         
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Ves\Brand\Model\Brand $brand,
        \Ves\Brand\Helper\Data $brandHelper,
        array $data = []
        ) {
        $this->_brand = $brand;
        $this->_coreRegistry = $registry;
        $this->_brandHelper = $brandHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $brand = $this->_brand;
        $group = $this->getCurrentGroup();
        $brandCollection = $brand->getCollection()
        ->addFieldToFilter('group_id',$group->getId())
        ->addFieldToFilter('status',1)
        ->setOrder('position','ASC');
        $this->setCollection($brandCollection);
        $template = 'group/view.phtml';
        if(!$this->hasData('template')){
            $this->setTemplate($template);
        }
    }

    public function getCurrentGroup()
    {
        $group = $this->_coreRegistry->registry('current_group_brand');
        if ($group) {
            $this->setData('current_group_brand', $group);
        }
        return $group;
    }

	/**
     * Prepare breadcrumbs
     *
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $group = $this->getCurrentGroup();
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $brandRoute = $this->_brandHelper->getConfig('general_settings/route');
        $page_title = $this->_brandHelper->getConfig('brand_list_page/page_title');

        if($breadcrumbsBlock){
        $breadcrumbsBlock->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $baseUrl
            ]
            );

        $breadcrumbsBlock->addCrumb(
            'vesbrand',
            [
                'label' => $page_title,
                'title' => $page_title,
                'link' => $baseUrl.$brandRoute
            ]
            );

        $breadcrumbsBlock->addCrumb(
            'brand',
            [
                'label' => $group->getName(),
                'title' => $group->getName(),
                'link' => ''
            ]
            );
        }
    }

    /**
     * Set brand collection
     * @param \Ves\Brand\Model\Brand
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    /**
     * Retrive brand collection
     * @param \Ves\Brand\Model\Brand
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    public function getConfig($key, $default = '')
    {
        $result = $this->_brandHelper->getConfig($key);
        if(!$result){
            return $default;
        }
        return $result;
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('ves-brandlist');
        $group = $this->getCurrentGroup();
        $page_title = $group->getName();
        if($page_title){
            $this->pageConfig->getTitle()->set($page_title);
            $this->pageConfig->setKeywords($page_title);
            $this->pageConfig->setDescription($page_title); 
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->getBlock('vesbrand_toolbar');
        if ($block) {
            $block->setDefaultOrder("position");
            $block->removeOrderFromAvailableOrders("price");
            return $block;
        }
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $collection = $this->getCollection();
        $toolbar = $this->getToolbarBlock();

        // set collection to toolbar and apply sort
        if($toolbar){
            $itemsperpage = (int)$this->getConfig('group_page/item_per_page',12);
            $toolbar->setData('_current_limit',$itemsperpage)->setCollection($collection);
            $this->setChild('group-toolbar', $toolbar);
        }
        return parent::_beforeToHtml();
    }
}
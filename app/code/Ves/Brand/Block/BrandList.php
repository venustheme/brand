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
namespace Ves\Brand\Block;
use Magento\Customer\Model\Context as CustomerContext;

class BrandList extends \Magento\Framework\View\Element\Template
{
    /**
     * Group Collection
     */
    protected $_brandCollection;

	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_brandHelper;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context         
     * @param \Magento\Framework\Registry                      $registry        
     * @param \Ves\Brand\Helper\Data                           $brandHelper     
     * @param \Ves\Brand\Model\Brand                           $brandCollection 
     * @param array                                            $data            
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Ves\Brand\Helper\Data $brandHelper,
        \Ves\Brand\Model\Brand $brandCollection,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
        ) {
        $this->_brandCollection = $brandCollection;
        $this->_brandHelper = $brandHelper;
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    public function _construct(){
        if(!$this->getConfig('general_settings/enable') || !$this->getConfig('brand_block/enable')) return;
        parent::_construct();
        $carousel_layout = $this->getConfig('brand_block/carousel_layout');
        $template = '';
        if($carousel_layout == 'owl_carousel'){
            $template = 'block/brand_list_owl.phtml';
        }else{
            $template = 'block/brand_list_bootstrap.phtml';
        }
        if(!$this->getTemplate() && $template!=''){
            $this->setTemplate($template);
        }
    }

    public function getConfig($key, $default = '')
    {   
        $widget_key = explode('/', $key);
        if( (count($widget_key)==2) && ($resultData = $this->hasData($widget_key[1])) )
        {
            return $this->getData($widget_key[1]);
        }
        $result = $this->_brandHelper->getConfig($key);
        if($result == ""){
            return $default;
        }
        return $result;
    }

    public function getBrandCollection()
    {
        $number_item = $this->getConfig('brand_block/number_item');
        $brandGroups = $this->getConfig('brand_block/brand_groups');
        $collection = $this->_brandCollection->getCollection()
        ->setOrder('position','ASC')
        ->addFieldToFilter('status',1);
        $brandGroups = explode(',', $brandGroups);
        if(is_array($brandGroups) && count($brandGroups)>0)
        {
            $collection->addFieldToFilter('group_id',array('in' => $brandGroups));
        }
        $collection->setPageSize($number_item)
        ->setCurPage(1)
        ->setOrder('position','ASC');
        return $collection;
    }


    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
        'VES_BRAND_LIST',
        $this->_storeManager->getStore()->getId(),
        $this->_design->getDesignTheme()->getId(),
        $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
        'template' => $this->getTemplate(),
        $this->getProductsCount()
        ];
    }

    public function _toHtml()
    {
        return parent::_toHtml();
    }
}
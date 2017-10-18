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

class GroupList extends \Magento\Framework\View\Element\Template
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
    protected $_group;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context      
     * @param \Magento\Framework\Registry                      $registry     
     * @param \Ves\Brand\Helper\Data                           $brandHelper  
     * @param \Ves\Brand\Model\Group                           $group        
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager 
     * @param array                                            $data         
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Ves\Brand\Helper\Data $brandHelper,
        \Ves\Brand\Model\Group $group,
        array $data = []
        ) {
        if(!$this->getConfig('general_settings/enable')) return;
        if(!$this->getConfig('general_settings/enable_menu')) return;
        $this->_group = $group;
        $this->_coreRegistry = $registry;
        $this->_brandHelper = $brandHelper;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        if(!$this->getConfig('general_settings/enable')) return;
        if(!$this->getConfig('general_settings/enable_menu')) return;
        parent::_construct();
    }

    public function getGroupList(){
        $collection = $this->_group->getCollection()
        ->addFieldToFilter('status',1)
        ->addFieldToFilter('shown_in_sidebar',1)
        ->setOrder('position','ASC');
        return $collection;
    }
}
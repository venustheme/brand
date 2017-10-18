<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_All
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\All\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Module\Dir;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var array
     */
    protected $_config = [];

    /**
     * Template filter factory
     *
     * @var \Magento\Catalog\Model\Template\Filter\Factory
     */
    protected $_templateFilterFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Framework\App\Helper\Context      $context        
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager   
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider 
     * @param \Magento\Framework\Filesystem              $filesystem     
     * @param \Magento\Framework\Registry                $registry       
     * @param \Magento\Framework\Module\Dir\Reader       $moduleReader   
     * @param \Ves\All\Model\License                     $licnese        
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Ves\All\Model\License $licnese
    ) {
        parent::__construct($context);
        $this->_storeManager   = $storeManager;
        $this->_filterProvider = $filterProvider;
        $this->_filesystem     = $filesystem;
        $this->_coreRegistry   = $registry;
        $this->_license        = $licnese;
        $this->_moduleReader  = $moduleReader;
    }

	 /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
     public function getConfig($key, $group = "vesall/general", $store = null)
     {
        $store     = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();
        if ($this->_storeManager->isSingleStoreMode()) {
            $result = $this->scopeConfig->getValue(
                $group . '/' .$key,
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES
                );
        } else {
            $result = $this->scopeConfig->getValue(
                $group . '/' .$key,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store);
        }
        return $result;
    }

    public function filter($str)
    {
    	$html = $this->_filterProvider->getPageFilter()->filter($str);
    	return $html;
    }

    public function getLicense($module_name) {
        $ip          = $this->_remoteAddress->getRemoteAddress();
        $file        = $this->_moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, $module_name) . '/license.xml';
        if(file_exists($file)) {
            $xmlObj      = new \Magento\Framework\Simplexml\Config($file);
            $xmlData     = $xmlObj->getNode();
            if ($xmlData) {
                $code = $xmlData->code;
                $license = $this->_license->load($code);
                return $license;
            }
            return false;
        }else{
            return true;
        }
    }
}

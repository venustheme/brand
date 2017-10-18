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

namespace Ves\All\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Module\Dir;

class CheckLicense implements ObserverInterface
{
	/**
     * @var \Ves\All\Model\License
     */
	protected $_license;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

	/**
     * @var \Magento\Framework\Module\Dir\Reader
     */
	protected $_moduleReader;

	/**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
	protected $_remoteAddress;

	/**
	 * @param \Ves\All\Model\License                               $licnese        
	 * @param \Magento\Framework\Module\Dir\Reader                 $moduleReader   
	 * @param \Magento\Store\Model\StoreManagerInterface           $storeManager   
	 * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress  
	 * @param \Magento\Framework\Message\ManagerInterface          $messageManager 
	 */
	public function __construct(
		\Ves\All\Model\License $licnese,
		\Magento\Framework\Module\Dir\Reader $moduleReader,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Ves\All\Helper\Data $licenseHelper
	) {
		$this->_license       = $licnese;
		$this->_moduleReader  = $moduleReader;
		$this->messageManager = $messageManager;
		$this->_storeManager  = $storeManager;
		$this->_remoteAddress = $remoteAddress;
		$this->licenseHelper  = $licenseHelper;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$ip         = $this->_remoteAddress->getRemoteAddress();
		$obj        = $observer->getObj();
		$moduleName = $observer->getEx();
		$license    = $this->licenseHelper->getLicense($moduleName);

		if (($license && is_bool($license)) || ($license && $license->getStatus())) {
			$obj->setData('is_valid', 1);
			
		} else {
			$obj->setData('is_valid',0);
			if ($ip == '127.0.0.1') {
				$obj->setData('local_valid', 1);
			} else {
				$obj->setData('local_valid', 0);
			}
		}
	}
}
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
 * @package    Ves_All
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\All\Block\Adminhtml;

class License extends \Magento\Framework\View\Element\Template
{
	protected function _toHtml() {
		$extension = $this->getData('extension');
		if ($extension) {
			$this->_eventManager->dispatch(
				'ves_check_license',
				['obj' => $this,'ex'=>$extension]
				);
			$extension = str_replace("_", " ", $extension);

			if ($this->hasData('is_valid') && !$this->getData('is_valid')) {
				return '<div style="margin-top: 5px;"><div class="messages error"><div class="message message-error" style="margin-bottom: 0;"><div>Module <b>' . $extension . '</b> is not yet registered! Go to <b>Backend > Venustheme > License</b> to register the module. Please login to your account in <a target="_blank" href="https://landofcoder.com">landofcoder.com</a>, then go to <b>Dashboard > My Downloadable Products</b>, enter your domains to get a new license. Next go to <b>Backend > Vesnustheme > Licenses</b> to save the license.</div></div></div></div>';
	        }
	    }
		return parent::_toHtml();
	}
}
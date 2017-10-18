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
namespace Ves\Brand\Block\Adminhtml\System\Config\Form\Field;

class Editor extends \Magento\Config\Block\System\Config\Form\Field
{
	/**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

	/**
     * @param \Magento\Backend\Block\Template\Context $context       
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig 
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
        ) {
    	$this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
    	$config = $this->_wysiwygConfig->getConfig();
        $element->setWysiwyg(true);
        $element->setConfig($config);
        return parent::_getElementHtml($element);
    }
}
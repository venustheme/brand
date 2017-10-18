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
 * @package    Ves_Setup
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Setup\Model\System\Config\Source\Export;

class Pagebuilder implements \Magento\Framework\Option\ArrayInterface
{
	protected  $_pageModel;
    protected  $_objectManager;

    /**
     * @param \Magento\Cms\Model\Page $pageModel
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
        ) {
        $this->_objectManager = $objectManager;
    }

    public function getModelObject() {
        if(!$this->_pageModel){
            $this->_pageModel = $this->_objectManager->create('Ves\PageBuilder\Model\Block');
        }

        return $this->_pageModel;
    }

    /**
     * Options getter
     * @block_type: block, page
     * @return array
     */
    public function toOptionArray($block_type="block")
    {
    	$collection = $this->getModelObject()->getCollection();
        if($block_type) {
            $collection->addFieldToFilter("block_type", $block_type);
        }
    	$blocks = array();
    	foreach ($collection as $_page) {
    		$blocks[] = [
    		'value' => $_page->getId(),
    		'label' => addslashes($_page->getTitle())
    		];
    	}
        return $blocks;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toArray()
    {
        $collection = $this->getModelObject()->getCollection();
        $blocks = array();
        foreach ($collection as $_page) {
            $blocks[$_page->getId()] = addslashes($_page->getTitle());
        }
        return $blocks;
    }
}
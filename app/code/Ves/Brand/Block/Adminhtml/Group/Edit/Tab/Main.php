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
namespace Ves\Brand\Block\Adminhtml\Group\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
	/**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    protected $_wysiwygConfig1;

    /**
     * @param \Magento\Backend\Block\Template\Context $context       
     * @param \Magento\Framework\Registry             $registry      
     * @param \Magento\Framework\Data\FormFactory     $formFactory   
     * @param \Magento\Store\Model\System\Store       $systemStore   
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig 
     * @param array                                   $data          
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_wysiwygConfig1 = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm() {
    	/** @var $model \Ves\Brand\Model\Brand */
    	$model = $this->_coreRegistry->registry('ves_brand');

    	/**
    	 * Checking if user have permission to save information
    	 */
    	if ($this->_isAllowedAction('Ves_Brand::group_edit')) {
    		$isElementDisabled = false;
    	} else {
    		$isElementDisabled = true;
    	}

    	/** @var \Magento\Framework\Data\Form $form */
    	$form = $this->_formFactory->create();

    	$form->setHtmlIdPrefix('brand_');

    	$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Group Information')]);

    	if ($model->getId()) {
    		$fieldset->addField('group_id', 'hidden', ['name' => 'group_id']);
    	}

    	$fieldset->addField(
    		'name',
    		'text',
    		[
	    		'name' => 'name',
	    		'label' => __('Group Name'),
	    		'title' => __('Group Name'),
	    		'required' => true,
	    		'disabled' => $isElementDisabled
    		]
    		);

    	$fieldset->addField(
    		'url_key',
    		'text',
    		[
	    		'name' => 'url_key',
	    		'label' => __('URL Key'),
	    		'title' => __('URL Key'),
	    		'note' => __('Empty to auto create url key'),
	    		'disabled' => $isElementDisabled
    		]
    		);

        $fieldset->addField(
    		'position',
    		'text',
    		[
	    		'name' => 'position',
	    		'label' => __('Position'),
	    		'title' => __('Position'),
	    		'disabled' => $isElementDisabled
    		]
    		);

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Page Status'),
                'name' => 'status',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'shown_in_sidebar',
            'select',
            [
                'label' => __('Show In Sidebar'),
                'title' => __('Show In Sidebar'),
                'name' => 'shown_in_sidebar',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );


    	$form->setValues($model->getData());
    	$this->setForm($form);

    	return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Brand Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Brand Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
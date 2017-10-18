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
namespace Ves\Brand\Block\Adminhtml\Brand\Edit\Tab;

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

    /**
     * @var \Ves\Brand\Helper\Data
     */
    protected $_viewHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context       
     * @param \Magento\Framework\Registry             $registry      
     * @param \Magento\Framework\Data\FormFactory     $formFactory   
     * @param \Magento\Store\Model\System\Store       $systemStore   
     * @param \Magento\Cms\Model\Wysiwyg\Config       $wysiwygConfig 
     * @param \Ves\Brand\Helper\Data                  $viewHelper    
     * @param array                                   $data          
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Ves\Brand\Helper\Data $viewHelper,
        array $data = []
    ) {
        $this->_viewHelper = $viewHelper;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
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
        
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);
    	/**
    	 * Checking if user have permission to save information
    	 */
    	if($this->_isAllowedAction('Ves_Brand::brand_edit')){
    		$isElementDisabled = false;
    	}else {
    		$isElementDisabled = true;
    	}
    	/** @var \Magento\Framework\Data\Form $form */
    	$form = $this->_formFactory->create();

    	$form->setHtmlIdPrefix('brand_');

    	$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Brand Information')]);


    	if ($model->getId()) {
    		$fieldset->addField('brand_id', 'hidden', ['name' => 'brand_id']);
    	}

    	$fieldset->addField(
    		'name',
    		'text',
    		[
                'name'     => 'name',
                'label'    => __('Brand Name'),
                'title'    => __('Brand Name'),
                'required' => true,
                'disabled' => $isElementDisabled
    		]
    		);

    	$fieldset->addField(
    		'url_key',
    		'text',
    		[
                'name'     => 'url_key',
                'label'    => __('URL Key'),
                'title'    => __('URL Key'),
                'note'     => __('Empty to auto create url key'),
                'disabled' => $isElementDisabled
    		]
    		);

        $fieldset->addField(
            'group_id',
            'select',
            [
                'label'    => __('Brand Group'),
                'title'    => __('Brand Group'),
                'name'     => 'group_id',
                'required' => true,
                'options'  => $this->_viewHelper->getGroupList(),
                'disabled' => $isElementDisabled
            ]
        );

    	$fieldset->addField(
    		'image',
    		'image',
    		[
                'name'     => 'image',
                'label'    => __('Image'),
                'title'    => __('Image'),
                'disabled' => $isElementDisabled
    		]
    		);

    	$fieldset->addField(
    		'thumbnail',
    		'image',
    		[
                'name'     => 'thumbnail',
                'label'    => __('Thumbnail'),
                'title'    => __('Thumbnail'),
                'disabled' => $isElementDisabled
    		]
    		);

    	$fieldset->addField(
            'description',
            'editor',
            [
                'name'     => 'description',
                'style'    => 'height:200px;',
                'label'    => __('Description'),
                'title'    => __('Description'),
                'disabled' => $isElementDisabled,
                'config'   => $wysiwygConfig
            ]
        );

    	/**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }


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
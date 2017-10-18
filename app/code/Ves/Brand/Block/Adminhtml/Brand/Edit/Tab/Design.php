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

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Design extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\View\Design\Theme\LabelFactory
     */
    protected $_labelFactory;

    /**
     * @var \Magento\Theme\Model\Layout\Source\Layout
     */
    protected $_pageLayout;

    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context                          $context           
     * @param \Magento\Framework\Registry                                      $registry          
     * @param \Magento\Framework\Data\FormFactory                              $formFactory       
     * @param \Magento\Theme\Model\Layout\Source\Layout                        $pageLayout        
     * @param \Magento\Framework\View\Design\Theme\LabelFactory                $labelFactory      
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder 
     * @param array                                                            $data              
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Theme\Model\Layout\Source\Layout $pageLayout,
        \Magento\Framework\View\Design\Theme\LabelFactory $labelFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    ) {
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->_labelFactory = $labelFactory;
        $this->_pageLayout = $pageLayout;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form tab configuration
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * Initialise form fields
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        $isElementDisabled = !$this->_isAllowedAction('Ves_Brand::brand_edit');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(['data' => ['html_id_prefix' => 'brand_']]);

        $model = $this->_coreRegistry->registry('ves_brand');

        $layoutFieldset = $form->addFieldset(
            'layout_fieldset',
            ['legend' => __('Page Layout'), 'class' => 'fieldset-wide', 'disabled' => $isElementDisabled]
        );

        $layoutFieldset->addField(
            'page_layout',
            'select',
            [
                'name' => 'page_layout',
                'label' => __('Layout'),
                'required' => true,
                'values' => $this->pageLayoutBuilder->getPageLayoutsConfig()->toOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        if (!$model->getId()) {
            $model->setRootTemplate($this->_pageLayout->getDefaultValue());
        }

        $layoutFieldset->addField(
            'layout_update_xml',
            'textarea',
            [
                'name' => 'layout_update_xml',
                'label' => __('Layout Update XML'),
                'style' => 'height:24em;',
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('adminhtml_cms_page_edit_tab_design_prepare_form', ['form' => $form]);

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
        return __('Design');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Design');
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

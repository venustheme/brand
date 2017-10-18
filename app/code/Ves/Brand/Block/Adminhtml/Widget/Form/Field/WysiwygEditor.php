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
namespace Ves\Brand\Block\Adminhtml\Widget\Form\Field;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Escaper;

class WysiwygEditor extends Template implements RendererInterface
{

    /**
     * @var \Magento\Framework\Data\Form\Element\CollectionFactory
     */
    protected $_factoryCollection;

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_factoryElement;

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData = null;

    /**
     * @param \Magento\Backend\Block\Template\Context                $context           
     * @param \Magento\Framework\Data\Form\Element\Factory           $factoryElement    
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection 
     * @param Escaper                                                $escaper           
     * @param \Magento\Cms\Model\Wysiwyg\Config                      $wysiwygConfig     
     * @param \Magento\Framework\View\LayoutInterface                $layout            
     * @param \Magento\Backend\Helper\Data                           $backendData       
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Backend\Helper\Data $backendData
        ){
        $this->_factoryElement = $factoryElement;
        $this->_factoryCollection = $factoryCollection;
        $this->_backendData = $backendData;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context);
    }

    public function render(AbstractElement $element){
        $html = '';
        $config = $this->_wysiwygConfig->getConfig();
        $config['height'] = '300px';
        $config = json_encode($config->getData());
        $value = $element->getValue();
        if(!is_array($value)){
            if(base64_decode($value, true) == true){
                $value = base64_decode($value);
            }
        }

        $class = '';
        if($element->getRequired()){
            $class = 'required-entry';
        }

        $html .= '<div class="admin__field field field-options_'.$element->getId().'  with-note">';
        $html .= $element->getLabelHtml();

        $html .= '<div class="admin__field-control control">';
        $html .= '<textarea id="' . $element->getHtmlId() . '" name="' . $element->getName() . '" class="textarea admin__control-textarea wysiwyg-editor ' . $class . '" rows="5" cols="15" data-ui-id="product-tabs-attributes-tab-fieldset-element-textarea-' . $element->getName() . '" aria-hidden="true">'.$value.'</textarea>';
  
            $html .= $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button',
                '',
                [
                    'data' => [
                        'label' => __('WYSIWYG Editor'),
                        'type' => 'button',
                        'class' => 'action-wysiwyg',
                        'onclick' => 'vesTinyMceWysiwygSetup.open(\'' . $this->_backendData->getUrl(
                            'catalog/product/wysiwyg'
                        ) . '\', \'' . $element->getHtmlId() . '\')',
                    ]
                ]
            )->toHtml();

            $html .= <<<HTML
            <script>
            require([
                'jquery',
                'Ves_Brand/js/wysiwyg/tiny_mce/setup'
            ], function(jQuery){
            var config = $config,
                editor;

            jQuery.extend(config, {
                settings: {
                    theme_advanced_buttons1: 'bold,italic,|,justifyleft,justifycenter,justifyright,|,' +
            'fontselect,fontsizeselect,|,forecolor,backcolor,|,link,unlink,image,|,bullist,numlist,|,code',
                    theme_advanced_buttons2: null,
                    theme_advanced_buttons3: null,
                    theme_advanced_buttons4: null
                }
            });

            editor{$element->getHtmlId()} = new vesTinyMceWysiwygSetup(
                '{$element->getHtmlId()}',
                config
            );

            editor{$element->getHtmlId()}.turnOn();
            varienGlobalEvents.clearEventHandlers("open_browser_callback");
            varienGlobalEvents.attachEventHandler("open_browser_callback", editor{$element->getHtmlId()}.openFileBrowser);
            jQuery('#{$element->getHtmlId()}')
                .addClass('wysiwyg-editor')
                .data(
                    'wysiwygEditor',
                    editor
                );
            });
            </script>
HTML;
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }
}
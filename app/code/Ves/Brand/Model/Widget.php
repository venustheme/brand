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
namespace Ves\Brand\Model;
use Magento\Framework\Module\Dir;
use Magento\Framework\Component\DirSearch;
use Magento\Framework\Component\ComponentRegistrar;
/**
 * Widget model for different purposes
 */
class Widget extends \Magento\Widget\Model\Widget
{
    /**
     * Return filtered list of widgets
     *
     * @param array $filters Key-value array of filters for widget node properties
     * @return array
     * @api
     */
    public function getWidgets($filters = [])
    {
        $core_widgets = parent::getWidgets($filters);
        $result = $core_widgets;
        $widgets = array();

        // filter widgets by params
        if (is_array($filters) && count($filters) > 0 && $widgets) {
            foreach ($widgets as $code => $widget) {
                try {
                    foreach ($filters as $field => $value) {
                        if (!isset($widget[$field]) || (string)$widget[$field] != $value) {
                            throw new \Exception();
                        }
                    }
                } catch (\Exception $e) {
                    unset($result[$code]);
                    continue;
                }
            }
        }

        return $result;
    }

    /**
     * Return widget presentation code in WYSIWYG editor
     *
     * @param string $type Widget Type
     * @param array $params Pre-configured Widget Params
     * @param bool $asIs Return result as widget directive(true) or as placeholder image(false)
     * @return string Widget directive ready to parse
     * @api
     */
    public function getWidgetDeclaration($type, $params = [], $asIs = true)
    {
        $field_pattern = ["pretext","pretext_html","shortcode","html","raw_html","content","tabs","latestmod_desc","custom_css","block_params"];
        $widget_types = ["Ves\BaseWidget\Block\Widget\Accordionbg"];

        foreach ($params as $k => $value) {
            if(0 < strpos($k, 'class') || 0 < strpos($k, 'Class')) {
                continue;
            }
            // Retrieve default option value if pre-configured
            if(is_array($params[$k]) || !base64_decode($params[$k], true)) {
                if(in_array($k, $field_pattern) || preg_match("/^tabs(.*)/", $k) || preg_match("/^content_(.*)/", $k) || (preg_match("/^header_(.*)/", $k) && in_array($type, $widget_types)) || (preg_match("/^html_(.*)/", $k) && in_array($type, $widget_types))) {
                    if(is_array($params[$k])){
                        $params[$k] = base64_encode(serialize($params[$k]));
                    }elseif(!base64_decode($params[$k], true)){
                        $params[$k] = base64_encode($params[$k]);
                    }
                }
            }
            
        }
        return parent::getWidgetDeclaration($type, $params, $asIs);
    }

}

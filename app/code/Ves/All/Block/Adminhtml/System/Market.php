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

namespace Ves\All\Block\Adminhtml\System;

use Magento\Framework\App\Filesystem\DirectoryList;
use Ves\All\Block\Adminhtml\System\ListLicense;

class Market extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context              $context       
     * @param \Magento\Framework\App\ResourceConnection            $resource 
     * @param \Ves\All\Helper\Data                                 $helper        
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress 
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Ves\All\Helper\Data $helper,
        \Ves\All\Model\License $license,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
        )
    {
        parent::__construct($context);
        $this->_resource      = $resource;
        $this->_helper        = $helper;
        $this->_remoteAddress = $remoteAddress;
        $this->_license       = $license;
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if (!extension_loaded('soap')) {
            throw new \Magento\Framework\Webapi\Exception(
                __('SOAP extension is not loaded.'),
                0,
                \Magento\Framework\Webapi\Exception::HTTP_INTERNAL_ERROR
            );
        }
        $products = array();
        try{
            $opts = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false
                        )
                    );
            $context = stream_context_create($opts);
            $params = array('soap_version'=>SOAP_1_2,
                            'verifypeer' => false,
                            'verifyhost' => false,
                            'exceptions' => 1,
                            'stream_context'=>$context);

            $proxy = new \SoapClient(ListLicense::API_URL, $params);

            $sessionId = $proxy->login(ListLicense::API_USERNAME, ListLicense::API_PASSWORD);
            $products = $proxy->call($sessionId, 'veslicense.productlist1');
        } catch(SoapFault $e){

        }
        $total = 12;
        $column = 2;
        $x = 0;
        $html = '';
        $html .= '<div id="ves-elist">';
        $html .=  '<h1><a href="https://landofcoder.com">Landofcoder.com - Opensource Marketplace for magento, opencart</a></h1>';
        foreach ($products as $_product) {
            if( $column == 1 || $x%$column == 0){
                $html .= '<div class="erow">';
            }
            $class = '';
            if( $column == 1 || ($x+1)%$column == 0 || $x == ($total-1) ) {
                $class = ' last';
            }

            if ($_product['price']==0 || $_product['price']=="" ||$_product['price']=="0.00" || $_product['price']=="$0.00" || !$_product['price']) {
                $price = 'FREE';
            } else {
                $price = $_product['price_currency'];
            }

            $html .= '<div class="extend-card ' . $class . '">';
            $html .= '<div class="extend-card-top">';
            $html .= '<div class="extend-card-image"><a href="' . $_product['purl'] . '" title="' . $_product['name'] . '"><img src="' .  $_product['pimg'] . '" class="plugin-icon" alt=""></a></div>';
            $html .= '<div class="extend-card-desc"><a href="' . $_product['purl'] . '" title="' . $_product['name'] . '"><h3>' .  $_product['name'] . '</h3></a><div class="extend-price">' . $price . '</div></div><div class="extend-des"> <div class="extend-des-inner"> ' . $_product['short_description'] . '</div> <a href="' . $_product['purl'] . '" title="' . $_product['name'] . '" class="extend-buynow" style="float: left;">Buy Now</a></div>';
            $html .= '</div>';
            $html .= '</div>';
            if( $column == 1 || ($x+1)%$column == 0 || $x == ($total-1) ) {
                $html .= '</div>';
            }
            $x++;
        }
        $html .= '</div>';
        
        return $this->_decorateRowHtml($element, $html);
    }

    public function getDomain($domain) {
        $domain = strtolower($domain);
        $domain = str_replace(['www.','WWW.','https://','http://','https','http'], [''], $domain);
        if($this->endsWith($domain, '/')){
            $domain = substr_replace($domain ,"",-1);
        }
        return $domain;
    }
    public function endsWith($haystack, $needle) {
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }
}
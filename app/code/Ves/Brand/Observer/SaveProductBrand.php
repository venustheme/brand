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
namespace Ves\Brand\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveProductBrand implements ObserverInterface
{
    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @param \Magento\Catalog\Helper\Data $catalogData
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
        )
    {
        $this->_resource = $resource;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $connection = $this->_resource->getConnection();
        $table_name = $this->_resource->getTableName('ves_brand_product');
        $productController = $observer->getController();
        $productId = $productController->getRequest()->getParam('id');
        $data = $productController->getRequest()->getPostValue();

        $connection->query('DELETE FROM ' . $table_name . ' WHERE product_id =  ' . (int)$productId . ' ');
        if(isset($data['product']['product_brand']) && $productId){
            $productBrands = $data['product']['product_brand'];
            if(!is_array($productBrands)){
                $productBrands = array();
                $productBrands[] = (int)$data['product']['product_brand'];
            }
            foreach ($productBrands as $k => $v) {
                $connection->query('INSERT INTO ' . $table_name . ' VALUES ( ' . $v . ', ' . (int)$productId . ',0)');
            }
        }
    }
}

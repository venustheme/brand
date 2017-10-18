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
namespace Ves\Brand\Setup;

use Ves\Brand\Model\Brand;
use Ves\Brand\Model\BrandFactory;
use Ves\Brand\Model\Group;
use Ves\Brand\Model\GroupFactory;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{
	/**
	 * Brand Factory
	 *
	 * @var BrandFactory
	 */
	private $brandFactory;

	/**
	 * @param BrandFactory $brandFactory 
	 * @param GroupFactory $groupFactory 
	 */
	public function __construct(
		BrandFactory $brandFactory,
		GroupFactory $groupFactory,
		EavSetupFactory $eavSetupFactory
		)
	{
		$this->brandFactory = $brandFactory;
		$this->groupFactory = $groupFactory;
		$this->eavSetupFactory = $eavSetupFactory;
	}

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
 		$data = array(
 			'group' => 'General',
 			'type' => 'varchar',
 			'input' => 'multiselect',
 			'default' => 1,
 			'label' => 'Product Brand',
 			'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
 			'frontend' => '',
 			'source' => 'Ves\Brand\Model\Brandlist',
 			'visible' => 1,
 			'required' => 1,
 			'user_defined' => 1,
 			'used_for_price_rules' => 1,
 			'position' => 2,
 			'unique' => 0,
 			'default' => '',
 			'sort_order' => 100,
 			'is_global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
 			'is_required' => 0,
 			'is_configurable' => 1,
 			'is_searchable' => 0,
 			'is_visible_in_advanced_search' => 0,
 			'is_comparable' => 0,
 			'is_filterable' => 0,
 			'is_filterable_in_search' => 1,
 			'is_used_for_promo_rules' => 1,
 			'is_html_allowed_on_front' => 0,
 			'is_visible_on_front' => 1,
 			'used_in_product_listing' => 1,
 			'used_for_sort_by' => 0,
 			);
 		$eavSetup->addAttribute(
 			\Magento\Catalog\Model\Product::ENTITY,
 			'product_brand',
 			$data);
	}
}

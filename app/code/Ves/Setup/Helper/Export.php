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
namespace Ves\Setup\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Module\Dir;

class Export extends \Magento\Framework\App\Helper\AbstractHelper
{
	/** @var \Magento\Framework\Xml\Parser */
	protected $parser;

	/** @var \Magento\Store\Model\StoreManagerInterface */
	protected $_storeManager;

    /**
     * DB connection
     *
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_conn;

	/**
	 * @var \Magento\Framework\Module\Dir
	 */
	protected $_moduleDir;

	/**
	 * @param \Magento\Framework\App\Helper\Context              $context      
	 * @param \Magento\Store\Model\StoreManagerInterface         $storeManager 
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig  
	 * @param \Magento\Framework\App\ResourceConnection          $resource     
	 * @param Dir                                                $moduleDir    
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\ResourceConnection $resource,
		Dir $moduleDir
		) {
		parent::__construct($context);
		$this->_moduleDir = $moduleDir;
		$this->parser = new \Magento\Framework\Xml\Parser();
		$this->_storeManager = $storeManager;
		$this->_resource = $resource;
	}

	public function exportModules($data){
		$moduleTables = $this->getModuleTables();
		$configs = [];
		if(!empty($data['modules'])){
			$store = $this->_storeManager->getStore($data['store_id']);
			foreach ($data['modules'] as $k => $v) {
				if(isset($moduleTables[$v])){
					$tables = $moduleTables[$v];
				}
				$systemFileDir = $this->_moduleDir->getDir($v,Dir::MODULE_ETC_DIR). DIRECTORY_SEPARATOR . 'adminhtml' . DIRECTORY_SEPARATOR . 'system.xml';

				if(file_exists($systemFileDir)){

					$systemConfigs = $this->parser->load($systemFileDir)->xmlToArray();
					if($systemConfigs['config']['_value']['system']['section']){
						foreach ($systemConfigs['config']['_value']['system']['section'] as $_section) {
							$groups = [];
							if(isset($_section['_value']['group'])){
								$groups = $_section['_value']['group'];
							}elseif(isset($_section['group'])){
								$groups = $_section['group'];
							}

							$_sectionId = '';
							if(isset($_section['_attribute']['id'])){
								$_sectionId = $_section['_attribute']['id'];
							}elseif(isset($systemConfigs['config']['_value']['system']['section']['_attribute']['id'])){
								$_sectionId = $systemConfigs['config']['_value']['system']['section']['_attribute']['id'];
							}

							if(empty($groups)) continue;
							foreach ($groups as $_group) {
								if(!isset($_group['_value']['field'])) continue;
								foreach ($_group['_value']['field'] as $_field) {
									if(isset($_sectionId) && isset($_group['_attribute']['id']) && isset($_field['_attribute']['id'])){
										$key = $_sectionId . '/' . $_group['_attribute']['id'] . '/' . $_field['_attribute']['id'];
										$result = $this->scopeConfig->getValue(
											$key,
											\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
											$store);
										if($result=='') continue;
										$configs[$v]['system_configs'][] = [
										'key' => $key,
										'value' => $result
										];
									}
								}
							}
						}
					}
				}
				if(isset($moduleTables[$v]) && is_array($moduleTables[$v])){
					foreach ($moduleTables[$v] as $key => $tableName) {
						$connection = $this->_resource->getConnection();
						if($this->checkTableExists($tableName, $connection)){
							$select = 'SELECT * FROM ' . $this->_resource->getTableName($tableName);
							$select .= $this->buildCondition($tableName, $data, $v);
							$rows = $connection->fetchAll($select);
							$configs[$v]['tables'][$tableName] = $rows;
						}
					}
				}
			}
		}
		return $configs;
	}

	public function checkTableExists($tableName,  $connection = null) {
		if(!$connection) {
			$connection = $this->_resource->getConnection();
		}

		$select = "SHOW TABLES LIKE '" . $this->_resource->getTableName($tableName)."'";
		$rows = $connection->fetchAll($select);
		if(count($rows) > 0 ){
			return true;
		}

		return false;
	}

	public function exportCmsPages($data){
		$configs = [];
		if(!empty($data['cmspages'])){
			$pageIds = implode(',', $data['cmspages']);
			$moduleTables = $this->getModuleTables();
			if(isset($moduleTables["Magento_Cms_Page"])){
				foreach ($moduleTables["Magento_Cms_Page"] as $k => $tableName) {
					$connection = $this->_resource->getConnection();
					$select = 'SELECT * FROM ' . $this->_resource->getTableName($tableName) . ' WHERE page_id IN (' . $pageIds . ') ';
					$rows = $connection->fetchAll($select);
					$configs['Magento_Cms_Page']['tables'][$tableName] = $rows;
				}
			}
		}
		return $configs;
	}

	public function exportStaticBlocks($data){
		$configs = [];
		if(!empty($data['cmsblocks'])){
			$blockIds = implode(',', $data['cmsblocks']);
			$moduleTables = $this->getModuleTables();
			if(isset($moduleTables["Magento_Cms_Block"])){
				foreach ($moduleTables["Magento_Cms_Block"] as $k => $tableName) {
					$connection = $this->_resource->getConnection();
					$select = 'SELECT * FROM ' . $this->_resource->getTableName($tableName) . ' WHERE block_id IN (' . $blockIds . ') ';
					$rows = $connection->fetchAll($select);
					$configs['Magento_Cms_Block']['tables'][$tableName] = $rows;
				}
			}
		}
		return $configs;
	}

	public function exportWidgets($data){
		$configs = [];
		if(!empty($data['widgets'])){
			$moduleTables = $this->getModuleTables();
			if(isset($moduleTables["Magento_Widget"])){

				// Widget Instance
				$connection = $this->_resource->getConnection();
				$select = 'SELECT * FROM ' . $this->_resource->getTableName('widget_instance') . ' WHERE instance_id IN (' .  implode(',', $data['widgets']) . ') ';
				$rows = '';
				$configs['Magento_Widget']['tables']['widget_instance'] = $connection->fetchAll($select);
				$widgetInstanceIds = [];
				foreach ($configs['Magento_Widget']['tables']['widget_instance'] as $k => $v) {
					$widgetInstanceIds[] = $v['instance_id'];
				}

				// Widget Instance Page
				if(!empty($widgetInstanceIds)){
					$connection = $this->_resource->getConnection();
					$select = 'SELECT * FROM ' . $this->_resource->getTableName('widget_instance_page') . ' WHERE instance_id IN (' .  implode(',', $widgetInstanceIds) . ') ';
					$rows = '';
					$configs['Magento_Widget']['tables']['widget_instance_page'] = $connection->fetchAll($select);
					$widgetInstancePageIds = [];
					foreach ($configs['Magento_Widget']['tables']['widget_instance_page'] as $k => $v) {
						$widgetInstancePageIds[] = $v['page_id'];
					}
				}

				// Widget Instance Page Layout
				$widgetInstancePageLayoutIds = [];
				if(!empty($widgetInstancePageIds)){
					$connection = $this->_resource->getConnection();
					$select = 'SELECT * FROM ' . $this->_resource->getTableName('widget_instance_page_layout') . ' WHERE page_id IN (' . implode(',', $widgetInstancePageIds) . ') ';
					$rows = '';
					$configs['Magento_Widget']['tables']['widget_instance_page_layout'] = $connection->fetchAll($select);
					foreach ($configs['Magento_Widget']['tables']['widget_instance_page_layout'] as $k => $v) {
						$widgetInstancePageLayoutIds[] = $v['layout_update_id'];
					}
				}

				// Widget Core Layout Link
				$widgetLayoutUpdateId = [];
				if(!empty($widgetInstancePageLayoutIds)){
					$connection = $this->_resource->getConnection();
					$select = 'SELECT * FROM ' . $this->_resource->getTableName('layout_link') . ' WHERE layout_link_id IN (' .  implode(',', $widgetInstancePageLayoutIds) . ') ';
					$rows = '';
					$configs['Magento_Widget']['tables']['layout_link'] = $connection->fetchAll($select);
					$widgetInstancePageLayoutIds = [];
					foreach ($configs['Magento_Widget']['tables']['layout_link'] as $k => $v) {
						$widgetLayoutUpdateId[] = $v['layout_update_id'];
					}
				}

				// Widget Core Layout Update
				if(!empty($widgetLayoutUpdateId)){
					$connection = $this->_resource->getConnection();
					$select = 'SELECT * FROM ' . $this->_resource->getTableName('layout_update') . ' WHERE layout_update_id IN (' .  implode(',', $widgetLayoutUpdateId) . ') ';
					$configs['Magento_Widget']['tables']['layout_update'] = $connection->fetchAll($select);
				}
			}
		}
		return $configs;
	}

	public function buildCondition($tableName, $data, $module_key) {
		$where = "";
		if($module_key == "Ves_PageBuilder") {
			$page_ids = isset($data['pageprofiles'])?$data['pageprofiles']:array();
			$element_ids = isset($data['elementprofiles'])?$data['elementprofiles']:array();
			$block_ids = array_merge($page_ids, $element_ids);
			if($block_ids) {
				switch ($tableName) {
					case 'ves_blockbuilder_block':
					case 'ves_blockbuilder_widget':
						$where = " WHERE `block_id` IN (".implode(",", $block_ids).")";
						break;
					default:
						# code...
						break;
				}
			}
		}

		if($module_key == "Ves_Megamenu") {
			$menu_ids = isset($data['megamenu'])?$data['megamenu']:array();
			if($menu_ids) {
				switch ($tableName) {
					case 'ves_megamenu_menu':
					case 'ves_megamenu_menu_store':
					case 'ves_megamenu_item':
						$where = " WHERE `menu_id` IN (".implode(",", $menu_ids).")";
						break;
					default:
						# code...
						break;
				}
			}
		}
		
		return $where;
	}

	public function getModuleTables() {
		$sql_tables = [
		"Ves_Blog" => ["ves_blog_category", "ves_blog_category_store","ves_blog_post", "ves_blog_post_author", "ves_blog_post_category", "ves_blog_post_tag", "ves_blog_post_related", "ves_blog_post_store", "ves_blog_comment", "ves_blog_comment_store", "ves_blog_post_vote", "ves_blog_post_products_related"],
		"Ves_PageBuilder" => ["ves_blockbuilder_block", "ves_blockbuilder_cms","ves_blockbuilder_page","ves_blockbuilder_widget"],
		"Ves_Brand" => ["ves_brand_group", "ves_brand","ves_brand_store"],
		"Ves_Megamenu" => ["ves_megamenu_menu", "ves_megamenu_menu_store","ves_megamenu_item","ves_megamenu_menu_customergroup"],
		"Ves_Testimonial" => ["ves_testimonial_testimonial","ves_testimonial_testimonial_store","ves_testimonial_category","ves_testimonial_testimonial_category","ves_testimonial_testimonial_product"],
		"Magento_Cms_Page" => ["cms_page", "cms_page_store"],
		"Magento_Cms_Block" => ["cms_block", "cms_block_store"],
		"Magento_Widget" => ["widget", "widget_instance", "widget_instance_page", "widget_instance_page_layout", "core_layout_link", "core_layout_update"]
		];
		return $sql_tables;
	}
}
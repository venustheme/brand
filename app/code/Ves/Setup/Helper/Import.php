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

class Import extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * @var \Ves\Setup\Helper\Data
	 */
	protected $_vesData;

	/**
	 * @param \Magento\Framework\App\Helper\Context     $context  
	 * @param \Magento\Framework\App\ResourceConnection $resource 
	 * @param \Ves\Setup\Helper\Data                    $vesData  
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\App\ResourceConnection $resource,
		\Ves\Setup\Helper\Data $vesData
		) {
		parent::__construct($context);
		$this->_resource = $resource;
		$this->_vesdata = $vesData;
	}

	public function buildQueryImport($data = array(), $table_name = "", $override = true, $store_id = 0, $where = '') {
		$query = false;
		$binds = array();
		if($data) {
			$table_name = $this->_resource->getTableName($table_name);
			if($override) {
				$query = "REPLACE INTO `".$table_name."` ";
			} else {
				$query = "INSERT IGNORE INTO `".$table_name."` ";
			}
			$stores = $this->_vesdata->getAllStores();
			$fields = $values = array();
			foreach($data as $key=>$val) {
				if($val) {
					if($key == "store_id" && !in_array($val, $stores)){
						$val = $store_id;
					}
					$fields[] = "`".$key."`";
					$values[] = ":".strtolower($key);
					$binds[strtolower($key)] = $val;
				}
			}
			$query .= " (".implode(",", $fields).") VALUES (".implode(",", $values).")";
		}
		return array($query, $binds);
	}
}
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
namespace Ves\Brand\Model\ResourceModel;

class Brand extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
	 * Store model
	 *
	 * @var \Magento\Store\Model\Store
	 */
	protected $_store = null;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $_date;

	/**
	 * Store manager
	 */
	protected $_storeManager;

	/**
	 * @var \Magento\Framework\Stdlib\Datetime
	 */
	protected $dateTime;

	/**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string $connectionName
     */
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Stdlib\DateTime $dateTime,
		$connectionName = null
		) {
		parent::__construct($context, $connectionName);
		$this->_date = $date;
		$this->_storeManager = $storeManager;
		$this->dateTime = $dateTime;
	}

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(){
    	$this->_init('ves_brand','brand_id');
    }

    /**
     *  Check whether brand url key is numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isNumericBrandUrlKey(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('url_key'));
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Cms\Model\Page $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int)$object->getStoreId()];
            $select->join(
                ['ves_brand_store' => $this->getTable('ves_brand_store')],
                $this->getMainTable() . '.brand_id = ves_brand_store.brand_id',
                []
                )->where(
                'status = ?',
                1
                )->where(
                'ves_brand_store.store_id IN (?)',
                $storeIds
                )->order(
                'ves_brand_store.store_id DESC'
                )->limit(
                1
                );
            }

            return $select;
        }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['cp' => $this->getMainTable()]
            )->join(
            ['cps' => $this->getTable('ves_brand_store')],
            'cp.brand_id = cps.brand_id',
            []
            )->where(
            'cp.identifier = ?',
            $identifier
            )->where(
            'cps.store_id IN (?)',
            $store
            );

            if (!is_null($isActive)) {
                $select->where('cp.status = ?', $isActive);
            }

            return $select;
        }

    /**
     * Check if brand url key exist for specific store
     * return brand id if brand exists
     *
     * @param string $url_key
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($url_key, $storeId)
    {
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($url_key, $stores, 1);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)->columns('cp.brand_id')->order('cps.store_id DESC')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Process brand data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['brand_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('ves_brand_store'), $condition);

        $condition = ['brand_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('ves_brand_product'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Process brand data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {

        $result = $this->checkUrlExits($object); 

        if ($object->isObjectNew() && !$object->hasCreationTime()) {
            $object->setCreationTime($this->_date->gmtDate());
        }

        $object->setUpdateTime($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }

    /**
     * Assign brand to store views
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table = $this->getTable('ves_brand_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['brand_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['brand_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }


        // Posts Related
        if(null !== ($object->getData('products'))){
            $table = $this->getTable('ves_brand_product');
            $where = ['brand_id = ?' => (int)$object->getId()];
            $this->getConnection()->delete($table, $where);

            if($quetionProducts = $object->getData('products')){
                $where = ['brand_id = ?' => (int)$object->getId()];
                $this->getConnection()->delete($table, $where);
                $data = [];
                foreach ($quetionProducts as $k => $_post) {
                    $data[] = [
                    'brand_id' => (int)$object->getId(),
                    'product_id' => $k,
                    'position' => $_post['product_position']
                    ];
                }
                $this->getConnection()->insertMultiple($table, $data);
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Load an object using 'url_key' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'url_key';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }

        if ($id = $object->getId()) {
                $connection = $this->getConnection();
                $select = $connection->select()
                ->from($this->getTable('ves_brand_product'))
                ->where(
                    'brand_id = '.(int)$id
                    );
                $products = $connection->fetchAll($select);
                $object->setData('products', $products);
            } 

        return parent::_afterLoad($object);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $brandId
     * @return array
     */
    public function lookupStoreIds($brandId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('ves_brand_store'),
            'store_id'
            )
        ->where(
            'brand_id = ?',
            (int)$brandId
            );
        return $connection->fetchCol($select);
    }

    public function checkUrlExits(\Magento\Framework\Model\AbstractModel $object)
    {
        $stores = $object->getStores();
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('ves_brand'),
            'brand_id'
            )
        ->where(
            'url_key = ?',
            $object->getUrlKey()
            )
        ->where(
            'brand_id != ?',
            $object->getId()
            );

        $brandIds = $connection->fetchCol($select);
        if(count($brandIds)>0 && is_array($stores)){
            if(in_array('0', $stores)){
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('URL key for specified store already exists.')
                    );  
            }
            $stores[] = '0';
            $select = $connection->select()->from(
                $this->getTable('ves_brand_store'),
                'brand_id'
                )
            ->where(
                'brand_id IN (?)',
                $brandIds
                )
            ->where(
                'store_id IN (?)',
                $stores
                );
            $result = $connection->fetchCol($select);
            if(count($result)>0){
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('URL key for specified store already exists.')
                    );  
            }
        }
        return $this;
    }
}
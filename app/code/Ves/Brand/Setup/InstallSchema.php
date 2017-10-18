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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
	/**
     * @var \Magento\Eav\Model\Entity\Type
     */
	protected $_entityTypeModel;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $_catalogAttribute;
    
    /**
     * @var \Magento\Eav\Setup\EavSetupe
     */
    protected $_eavSetup;

    /**
     * @param \Magento\Eav\Setup\EavSetup         $eavSetup         
     * @param \Magento\Eav\Model\Entity\Type      $entityType       
     * @param \Magento\Eav\Model\Entity\Attribute $catalogAttribute 
     */
    public function __construct(
    	\Magento\Eav\Setup\EavSetup $eavSetup,
    	\Magento\Eav\Model\Entity\Type $entityType,
    	\Magento\Eav\Model\Entity\Attribute $catalogAttribute
    	) {
    	$this->_eavSetup = $eavSetup;
    	$this->_entityTypeModel = $entityType;
    	$this->_catalogAttribute = $catalogAttribute;
    }

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    	$entityTypeModel = $this->_entityTypeModel;
    	$catalogAttributeModel = $this->_catalogAttribute;
    	$installer =  $this->_eavSetup;

    	$setup->startSetup();

		/**
		 * Drop table if exists
		 */
		$setup->getConnection()->dropTable($setup->getTable('ves_brand_group'));
		$setup->getConnection()->dropTable($setup->getTable('ves_brand'));
		$setup->getConnection()->dropTable($setup->getTable('ves_brand_store'));

 		/**
 		 * Create table 'ves_brand_group'
 		 */
 		$table = $setup->getConnection()
 		->newTable($setup->getTable('ves_brand_group'))
 		->addColumn(
 			'group_id',
 			Table::TYPE_INTEGER,
 			11,
 			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
 			'Group ID'
 			)
 		->addColumn(
 			'name',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Group Name'
 			)
 		->addColumn(
 			'url_key',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Group Url Key'
 			)
        ->addColumn(
            'position',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Position'
            )
        ->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Status'
            )
        ->addColumn(
            'shown_in_sidebar',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Show In Sidebar'
            )
        ->setComment('Brand Group');
        $setup->getConnection()->createTable($table);

 		/**
 		 * Create table 'ves_brand'
 		 */
 		$table = $setup->getConnection()
 		->newTable($setup->getTable('ves_brand'))
 		->addColumn(
 			'brand_id',
 			Table::TYPE_INTEGER,
 			null,
 			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
 			'Brand ID'
 			)
 		->addColumn(
 			'name',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Brand Name'
 			)
 		->addColumn(
 			'url_key',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Brand Url Key'
 			)
 		->addColumn(
 			'description',
 			Table::TYPE_TEXT,
 			'64k',
 			['nullable' => false],
 			'Brand Description'
 			)
 		->addColumn(
 			'group_id',
 			Table::TYPE_INTEGER,
 			11,
 			['unsigned' => true, 'nullable' => false],
 			'Group ID'
 			)
 		->addColumn(
 			'image',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Brand Image'
 			)
 		->addColumn(
 			'thumbnail',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Brand Thumbnail'
 			)
 		->addColumn(
 			'page_title',
 			Table::TYPE_TEXT,
 			255,
 			['nullable' => false],
 			'Brand Page Title'
 			)
 		->addColumn(
 			'meta_keywords',
 			Table::TYPE_TEXT,
 			'64k',
 			['nullable' => false],
 			'Meta Keywords'
 			)
 		->addColumn(
 			'meta_description',
 			Table::TYPE_TEXT,
 			'64k',
 			['nullable' => false],
 			'Meta Description'
 			)->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Brand Creation Time'
            )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Brand Modification Time'
            )->addColumn(
            'page_layout',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Page Layout'
            )->addColumn(
            'layout_update_xml',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Page Layout Update Content'
            )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Status'
            )
            ->addColumn(
                'position',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Position'
                )
            ->addIndex(
                $setup->getIdxName('ves_brand', ['group_id']),
                ['group_id']
                )
            ->addForeignKey(
                $setup->getFkName('ves_brand', 'group_id', 'ves_brand', 'group_id'),
                'group_id',
                $setup->getTable('ves_brand_group'),
                'group_id',
                Table::ACTION_CASCADE
                )
            ->setComment('Brand Information');
            $setup->getConnection()->createTable($table);

 		/**
         * Create table 'ves_brand_store'
         */
 		$table = $setup->getConnection()
 		->newTable($setup->getTable('ves_brand_store'))
 		->addColumn(
 			'brand_id',
 			Table::TYPE_INTEGER,
 			null,
 			['unsigned' => true, 'nullable' => false, 'primary' => true],
 			'Brand Id'
 			)
 		->addColumn(
 			'store_id',
 			Table::TYPE_SMALLINT,
 			null,
 			['unsigned' => true, 'nullable' => false, 'primary' => true],
 			'Store Id'
 			)
 		->addIndex(
 			$setup->getIdxName('ves_brand_store', ['store_id']),
 			['store_id']
 			)
 		->addForeignKey(
 			$setup->getFkName('ves_brand_store', 'brand_id', 'ves_brand', 'brand_id'),
 			'brand_id',
 			$setup->getTable('ves_brand'),
 			'brand_id',
 			Table::ACTION_CASCADE
 			)
 		->addForeignKey(
 			$setup->getFkName('ves_brand_store', 'store_id', 'store', 'store_id'),
 			'store_id',
 			$setup->getTable('store'),
 			'store_id',
 			Table::ACTION_CASCADE
 			)
 		->setComment('Brand Store');
 		$setup->getConnection()->createTable($table);

 		$setup->endSetup();
 	}
 }

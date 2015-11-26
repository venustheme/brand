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
namespace Ves\Brand\Model\ResourceModel\Brand\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;
use Ves\Brand\Model\ResourceModel\Brand\Collection as BrandCollection;

/**
 * Class Collection
 * Collection for displaying grid of sales documents
 */
class Collection extends BrandCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory 
     * @param \Psr\Log\LoggerInterface                                     $logger        
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy 
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager  
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager  
     * @param [type]                                                       $mainTable     
     * @param [type]                                                       $eventPrefix   
     * @param [type]                                                       $eventObject   
     * @param [type]                                                       $resourceModel 
     * @param string                                                       $model         
     * @param [type]                                                       $connection    
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null         $resource      
     */
    public function __construct(
    	\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
    	\Psr\Log\LoggerInterface $logger,
    	\Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
    	\Magento\Framework\Event\ManagerInterface $eventManager,
    	\Magento\Store\Model\StoreManagerInterface $storeManager,
    	$mainTable,
    	$eventPrefix,
    	$eventObject,
    	$resourceModel,
    	$model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
    	$connection = null,
    	\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    	) {
    	parent::__construct(
    		$entityFactory,
    		$logger,
    		$fetchStrategy,
    		$eventManager,
    		$storeManager,
    		$connection,
    		$resource
    		);
    	$this->_eventPrefix = $eventPrefix;
    	$this->_eventObject = $eventObject;
    	$this->_init($model, $resourceModel);
    	$this->setMainTable($mainTable);
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
    	return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
    	$this->aggregations = $aggregations;
    }


    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
    	return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
    	return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
    	return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
    	return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
    	return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
    	return $this;
    }
}
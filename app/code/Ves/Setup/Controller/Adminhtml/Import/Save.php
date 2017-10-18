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
namespace Ves\Setup\Controller\Adminhtml\Import;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Ves\Setup\Helper\Import
     */
    protected $_vesImport;

    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    protected $_configResource;

    /**
     * @param \Magento\Backend\App\Action\Context                          $context           
     * @param \Magento\Framework\View\Result\PageFactory                   $resultPageFactory 
     * @param \Ves\Setup\Helper\Import                                     $vesImport           
     * @param \Magento\Framework\Filesystem                                $filesystem        
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager      
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig       
     * @param \Magento\Framework\App\ResourceConnection                    $resource          
     * @param \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configResource    
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ves\Setup\Helper\Import $vesImport,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $configResource,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_filesystem       = $filesystem;
        $this->_storeManager     = $storeManager;
        $this->_scopeConfig      = $scopeConfig;
        $this->_configResource   = $configResource;
        $this->_resource         = $resource;
        $this->_vesImport        = $vesImport;
        $this->mediaConfig       = $mediaConfig;
    }

    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        try {
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'data_import_file']
            );

            $fileContent = '';
            if($uploader) {
                $tmpDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::TMP);
                $savePath     = $tmpDirectory->getAbsolutePath('ves/import');
                $uploader->setAllowRenameFiles(true);
                $result       = $uploader->save($savePath);
                $fileContent  = file_get_contents($tmpDirectory->getAbsolutePath('ves/import/' . $result['file']));
            }else if(isset($data['folder'])){
                $folder = $data['folder'];
                if(isset($data[$folder])) {
                    $filePath = $data[$folder];
                }
                if($filePath!='') {
                    $fileContent = file_get_contents($filePath);
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__("Can't import data<br/> %1", $e->getMessage()));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }

        $importData = \Zend_Json::decode($fileContent);

        $overwrite = false;
        if($data['overwrite_blocks']) {
            $overwrite = true;
        }

        $store = $this->_storeManager->getStore($data['store_id']);
        $connection = $this->_resource->getConnection();
        if(!empty($importData)) {
            try{
                foreach ($importData as $_module) {
                    if(isset($_module['tables'])) {
                        $tables = $_module['tables'];
                        foreach ($tables as $tablename => $rows) {
                            $table_name = $this->_resource->getTableName($tablename);
                            $connection->query("SET FOREIGN_KEY_CHECKS=0;");
                            $exist = false;
                            $check_query = "SHOW TABLES LIKE '".$table_name."'";
                            $total = $connection->fetchAll($check_query);
                            if(count($total) > 0) {
                                $exist = true;
                            }
                            if(!$overwrite && $exist) {
                                $connection->query("TRUNCATE `".$table_name."`");
                            }
                            if($overwrite) {
                                // Overide CMS Page, Static Block
                                if($table_name == 'cms_page_store' ) {
                                    $connection->query(" DELETE FROM ".$table_name." WHERE page_id = ".$row['page_id']);
                                }
                                if($table_name == 'cms_block_store' ) {
                                    $connection->query(" DELETE FROM ".$table_name." WHERE block_id = ".$row['block_id']);
                                }
                            }
                            foreach ($rows as $row) {
                                if($exist) {
                                    $where = '';
                                    $query_data = $this->_vesImport->buildQueryImport($row, $table_name, $overwrite, $data['store_id']); 
                                    $connection->query($query_data[0].$where, $query_data[1]);
                                }
                            }
                        }
                        $connection->query("SET FOREIGN_KEY_CHECKS=1;");
                    }
                    if(isset($_module['system_configs'])) {
                        foreach ($_module['system_configs'] as $_config) {
                            if(isset($_config['key'])) {
                                $result = $this->_scopeConfig->getValue(
                                    $_config['key'],
                                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                                    $store
                                );
                                if($result != $_config['value']) {
                                    if((int)$data['store_id'] == 0) {
                                        $this->_configResource->saveConfig($_config['key'], $_config['value'], "default", (int)$data['store_id']);
                                    }else{
                                        $this->_configResource->saveConfig($_config['key'], $_config['value'], "stores", (int)$data['store_id']);
                                    }
                                }
                            }
                        }
                    }
                }
                $this->messageManager->addSuccess(__("Import successfully"));
            }catch(\Exception $e){
                $this->messageManager->addSuccess(__("Can't import data<br/> %1", $e->getMessage()));
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}

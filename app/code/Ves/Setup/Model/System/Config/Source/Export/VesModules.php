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
namespace Ves\Setup\Model\System\Config\Source\Export;
use Magento\Framework\App\Filesystem\DirectoryList;

class VesModules implements \Magento\Framework\Option\ArrayInterface
{
	/**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $_moduleList;
    /**
     * @param \Magento\Cms\Model\Block $blockModel
     */
    public function __construct(
        \Magento\Framework\Module\ModuleListInterface $moduleList
        ) {
    	$this->_moduleList = $moduleList;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $output = [];
        $modules = $this->_moduleList->getNames();
        sort($modules);
        foreach ($modules as $k => $v) {
            if(preg_match("/Ves/", $v)){
                $output[$k] = [
                'value' => $v,
                'label' => $v
                ];
            }
        }
        return $output;
    }

    protected function getInstallConfig()
    {
        $file = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('etc/config.php');
        $installConfig = include $file;
        return $installConfig;
    }
}
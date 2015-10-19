<?php
namespace Ves\Brand\Controller\Adminhtml\Brand;

class Index extends \Ves\Brand\Controller\Adminhtml\Brand
{
	/**
	 * Brand list action
	 *
	 * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
	 */
	public function execute()
	{

		$resultPage = $this->resultPageFactory->create();

		/**
		 * Set active menu item
		 */
		$resultPage->setActiveMenu("Ves_Brand::brand_manage");
		$resultPage->getConfig()->getTitle()->prepend(__('Brands'));

		/**
		 * Add breadcrumb item
		 */
		$resultPage->addBreadcrumb(__('Brands'),__('Brands'));
		$resultPage->addBreadcrumb(__('Manage Brands'),__('Manage Brands'));

		return $resultPage;
	}
	
}
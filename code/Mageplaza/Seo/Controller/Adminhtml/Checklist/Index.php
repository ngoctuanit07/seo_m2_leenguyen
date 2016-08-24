<?php

namespace Mageplaza\Seo\Controller\Adminhtml\Checklist;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Check if user has enough privileges
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Skyfronts_Seo::checklist');
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();

        $this->_setActiveMenu('Mageplaza_Seo::checklist');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('SEO Checklist'));

        $this->_addBreadcrumb(__('Stores'), __('Stores'));
        $this->_addBreadcrumb(__('SEO Checklist'), __('SEO Checklist'));

        $this->_view->renderLayout();
    }
}

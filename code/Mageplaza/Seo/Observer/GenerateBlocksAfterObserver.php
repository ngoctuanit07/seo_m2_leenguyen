<?php

namespace Mageplaza\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Mageplaza\Seo\Helper\Data as SeoHelper;
use Magento\Framework\Registry;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Context;
use Magento\Framework\App\Request\Http as Url;
use Magento\Store\Model\Group;

class GenerateBlocksAfterObserver implements ObserverInterface
{
    protected $helper;
    protected $registry;
    protected $objectManager;
    protected $urlManager;
    protected $context;
    protected $url;
    protected $storeGroup;
    protected $_config;
     protected $_title;
     protected $_storeManager;
    public function __construct(
        SeoHelper $helper,
        Registry $registry,
        ObjectManagerInterface $objectManager,
        UrlInterface $urlManager,
        Context $context,
        Url $url,

        \Magento\Framework\View\Page\Config $config,
        \Magento\Framework\View\Page\Title $title,
         \Magento\Store\Model\StoreManagerInterface $storeManager,
        Group $storeGroup
    )
    {
        $this->helper = $helper;
        $this->registry = $registry;
        $this->objectManager = $objectManager;
        $this->urlManager = $urlManager;
        $this->context = $context;
        $this->url = $url;
        $this->storeGroup=$storeGroup;
        $this->_storeManager = $storeManager;
        $this->_config = $config;
        $this->_title = $title;
    }

    public function execute(Observer $observer)
    {
        $this->basicSetup($observer);

        return $this;
    }

    public function getBaseUrl()
    {
        return $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')
            ->getStore()
            ->getBaseUrl();
    }

    public function basicSetup($observer)
    {
      //die('69');
//        $action = $this->url->getFullActionName();
        $layout = $observer->getEvent()->getLayout();
        $action = $observer->getEvent()->getFullActionName();

        //var_dump($action); die();
        /**
         * catalog_category_view
         */
        if ($action == 'catalog_category_view') {
            $category = $this->registry->registry('current_category');
            $pageTitle = $category->getName();
            $pageDescription = $category->getMetaDescription();
            $pageKeywords = $category->getMetaKeywords();
            $pageRobots = $category->getMetaRobots();
            $url = $category->getUrl();

        }
        /**
         * catalogsearch_result_index
         */
        if($action == 'catalogsearch_result_index'){
          if($this->getGeneralConfig('nofollow_sr')){
            $this->_config->setRobots('NOINDEX, FOLLOW');
          }
        }

        /**
         * catalog_product_view
         */
        if ($action == 'catalog_product_view') {
            $product = $this->registry->registry('current_product');
            $pageTitle = $product->getName();

            /**
             * Auto set page title, meta description
             */
            if (empty($product->getMetaDescription())) {
                $pageDescription = trim(strip_tags($product->getShortDescription()));
            } else {
                $pageDescription = trim(strip_tags($product->getMetaDescription()));
            }
            $pageKeywords = $product->getMetaKeywords();
            $pageRobots = $product->getMetaRobots();
            $url = $product->getUrl();
        }
        if ($action == 'cms_index_index' OR $action == 'cms_page_view') {
            $page = $this->objectManager->get('Magento\Cms\Model\Page');
            $pageTitle = $page->getTitle();
            $pageDescription = $page->getMetaDescription();
            $pageKeywords = $page->getMetaKeywords();
            $pageRobots = $page->getMetaRobots();
            if ($action == 'cms_index_index') {
                $url = $this->urlManager->getBaseUrl();
            } else {
                $url = $this->urlManager->getUrl($page->getIdentifier());
            }
        }

        //if ($head = $layout->getBlock('head')) {
            // $this->_title->set($pageTitle);
               $this->_config->setDescription(strip_tags($pageDescription));
               $this->_config->setKeywords($pageKeywords);
            $this->_config->setRobots('NOINDEX, FOLLOW');
            //if (!empty($url)) $head->addItem('link_rel', $url, 'rel="alternate" hreflang="' . $this->getLangCode() . '"');
        //}
//        $layout->generateXml();

    }

    public function getLangCode()
    {
        $code = $this->storeGroup->getDefaultStore()->getLocaleCode();
        $code = strtolower($code);

        return $code;
    }

    protected function getGeneralConfig($code)
   {
       return $this->helper->getGeneralConfig($code);
   }
   protected function getMetaConfig($code)
   {
       return $this->helper->getMetaConfig($code);
       //return $this->getConfigValue(self::XML_PATH_META . $code, $storeId);
   }
   /**
    * Get Store name
    *
    * @return string
    */
   protected function getStoreName()
   {
       return $this->_storeManager->getStore()->getName();
   }
}

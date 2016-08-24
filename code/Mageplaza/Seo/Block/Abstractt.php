<?php

namespace Mageplaza\Seo\Block;

use Magento\Framework\View\Element\Template;
use Mageplaza\Seo\Helper\Data as HelperData;
use Magento\Framework\ObjectManagerInterface;
use Magento\Checkout\Model\Session;
use Magento\Theme\Block\Html\Header\Logo;
use Magento\Framework\Registry;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Magento\Review\Model\ReviewFactory;
class Abstractt extends Template
{
    protected $objectManager;
    protected $helperData;
    protected $objectFactory;
    protected $checkoutSession;
    protected $logo;
    protected $registry;
    protected $reviewCollection;
    protected $reviewCollectionFactory;
    protected $reviewFactory;
    protected $reviewRederer;
     protected $_storeManager;
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    HelperData $helperData,
    ObjectManagerInterface $objectManager,
    Session $session,
    Registry $registry,
    Logo $logo,
    \Magento\Store\Api\Data\StoreConfigInterface $storeConfig,
     \Magento\Store\Model\StoreManagerInterface $storeManager,
    CollectionFactory $reviewCollectionFactory,
    ReviewFactory $reviewFactory,
    array $data = []
    )
    {
      $this->_storeManager = $storeManager;
    $this->helperData = $helperData;
    $this->objectManager = $objectManager;
    $this->checkoutSession = $session;
    $this->registry = $registry;
    $this->logo = $logo;
    $this->reviewCollectionFactory = $reviewCollectionFactory;
    $this->reviewFactory=$reviewFactory;
    $this->storeConfig = $storeConfig;
    parent::__construct($context, $data);
    }

    public function getHelper()
    {
        return $this->helperData;
    }

    public function getBusinessName()
    {
        return $this->helperData->getConfigValue('general/store_information/name');
    }


    public function getBusinessPhone()
    {
        return $this->helperData->getConfigValue('general/store_information/phone');
    }

    public function getTwitterAccount()
    {
        $prefix = '@';
        $account = $this->helperData->getGeneralConfig('twitter_account');
        return $prefix . $account;
    }

    public function getLangCode()
    {
        /** @var \Magento\Framework\ObjectManagerInterface $om */
        $om = $this->objectManager;
        /** @var \Magento\Framework\Locale\Resolver $resolver */
        $resolver = $om->get('Magento\Framework\Locale\Resolver');
        $resolver = strtolower($resolver);
        return $resolver;
    }

    public function getCoreObject($helper)
    {
        return $this->objectManager->create($helper);
    }

    public function getCanonicalUrl(){

        $url = $this->getCurrentUrl();

        if($this->getGeneralConfig('https_canonical')){
            if($this->isFrontUrlSecure()){
              $url = str_replace('http:','https:',$url);
            }
        }


        return $url;
    }
    /**
    * Check if frontend URL is secure
    *
    * @return boolean
    */
   public function isFrontUrlSecure()
   {
       return $this->_storeManager->getStore()->isFrontUrlSecure();
   }
}

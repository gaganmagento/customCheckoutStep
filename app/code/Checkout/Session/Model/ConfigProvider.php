<?php

namespace Checkout\Session\Model;


use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /** @var LayoutInterface  */
    protected $_layout;
    protected $_customerSession;
    protected $_customerFactory;
    protected $_customerRepository;

    public function __construct(
        LayoutInterface $layout,
        session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        $this->_layout = $layout;
        $this->_customerSession = $customerSession;
        $this->_customerFactory = $customerFactory;
        $this->_customerRepository= $customerRepository;

    }

    public function getConfig()
    {
        $session =[];
        if(isset($_COOKIE["dealer-selection"]) && $_COOKIE['dealer-selection'] != 0){
            $id = $_COOKIE['dealer-selection'];
            $customer = $this->_customerRepository->getById($id);
            $customerType = attributeFallback($customer->getCustomAttribute('elmag_customer_kind'));
            $customerDealerView = attributeFallback($customer->getCustomAttribute('elmag_dealerview_visible'));
            
            if($customerType == 'dealer' && $customerDealerView == 4){
                $session['dealer'] = "partner_".$_COOKIE["dealer-selection"];
                if($customer->getCustomAttribute('elmag_dealerview_company') !== null){
                        $company = attributeFallback($customer->getCustomAttribute('elmag_dealerview_company'));
                        $session['company'] =  $company;
                    }
            }
        }
        if(isset($_COOKIE["refere-selection"])){
            if($_COOKIE["refere-selection"] != '0'){
                $referer1 =  preg_replace("(^https?://)", "", $_COOKIE["refere-selection"]);
                $referer  = rtrim($referer1, "/");
             $customerCollection = $this->_customerFactory->create()->getCollection()->addFieldToFilter('elmag_customer_kind','dealer')->addFieldToFilter('elmag_dealerview_visible','4');
             foreach($customerCollection as $data){
                $customer = $this->_customerRepository->getById($data->getId());
                if($customer->getCustomAttribute('elmag_dealerview_web') !== null){
                    if($customer->getCustomAttribute('elmag_dealerview_company') !== null){
                        $company = attributeFallback($customer->getCustomAttribute('elmag_dealerview_company'));
                    }
                    $vendors = attributeFallback($customer->getCustomAttribute('elmag_dealerview_web'));
                    if(preg_replace("(^https?://)", "", $vendors) == $referer){
                        $session['dealer'] = "partner_".$data->getId();
                        $session['company'] =  $company;
                    }
                }
             }
          }
        }
        return $session;
    }
}

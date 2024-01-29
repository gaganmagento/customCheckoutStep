<?php

namespace Checkout\Customer\Model;


use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /** @var LayoutInterface  */
    protected $_layout;
    protected $_customerSession;
    protected $_customerRepository;

    public function __construct(LayoutInterface $layout,session $customerSession,CustomerRepositoryInterface $customerRepository)
    {
        $this->_layout = $layout;
        $this->_customerSession = $customerSession;
        $this->_customerRepository= $customerRepository;

    }

    public function getConfig()
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/LOADLASAN.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('customloggenerate', true);
        

        $config = [];
        if($this->_customerSession->isLoggedIn()){
        $id = $this->_customerSession->getId();

        $customer = $this->_customerRepository->getById($id);
        if(!empty($customer->getCustomAttribute('elmag_customer_kind')) && !empty($customer->getCustomAttribute('elmag_dealerview_visible'))){
                if($customer->getCustomAttribute('elmag_dealerview_visible')->getValue() == 4){
                    $customerType = $customer->getCustomAttribute('elmag_customer_kind')->getValue(); 
                } else{
                    $customerType = '';
                }
        }else{
            $customerType = '';
        }
                                   

        if($customerType == 'dealer'){
            $config['text'] = $customerType;
        }else{
            $config['text'] = '';
        }
        }else{
            $config['text'] = '';
        }
        return $config;

    }
}

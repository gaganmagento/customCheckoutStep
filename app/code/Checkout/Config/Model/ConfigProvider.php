<?php

namespace Checkout\Config\Model;


use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /** @var LayoutInterface  */
    protected $_layout;
     protected $_storeManager;

    public function __construct(LayoutInterface $layout, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->_layout = $layout;
        $this->_storeManager = $storeManager;
    }

    public function getConfig()
    {

        return [
            'cms_block_message' => $this->_layout->createBlock('Magento\Cms\Block\Block')->setBlockId('checkout_block')->toHtml(),
            'baseUrl' => $this->_storeManager->getStore()->getBaseUrl().'/customer/account/login'
        ];
    }
}
<?php
namespace Checkout\Step\Controller\Index;

use Magento\Framework\App\RequestInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;

class Save extends \Magento\Framework\App\Action\Action
{
	protected $_request;
	protected $_customerFactory;
	protected $customerRepository;
	protected $_checkoutSession;
	protected $_json;
	private $quote;
	protected $quoteFactory;
	 protected $_moduleFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		CustomerRepositoryInterface $customerRepository,
		\Magento\Quote\Model\Quote $quote,
		QuoteFactory $quoteFactory,
		\Checkout\Step\Model\Dealer $moduleFactory,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Framework\App\Config\ScopeConfigInterface $scope_config,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		RequestInterface $request
		)
	{
		 $this->_request = $request;
		 $this->quoteFactory = $quoteFactory;
		  $this->_checkoutSession = $checkoutSession;
		  $this->_moduleFactory = $moduleFactory;
		 $this->resultJsonFactory = $resultJsonFactory;
		 $this->customerRepository = $customerRepository;
		 $this->quote = $quote;
		 $this->_customerFactory = $customerFactory;
		 $this->_googleAPIKey = $scope_config->getValue("system/api/google_key", \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		return parent::__construct($context);
	}

	public function execute()
	{		

			$quoteId = $this->_checkoutSession->getQuote()->getId();
			$key = $this->_request->getParam('key');	
			$customer = $this->_request->getParam('customer');
			if($customer != 'default-choice'){
				$whatIWant = substr($customer, strpos($customer, "_") + 1);   
			}else{
				$whatIWant = $customer;
			}
			$model = $this->_moduleFactory->getCollection();
			$data = $model->addFieldToFilter(
			    array('order_id'),
			    array(
			        array('eq'=>$quoteId)
			    )
			);
			if(!empty($data->getData())){
				foreach($data as $value){
					$model = $this->_moduleFactory->load($value['entity_id']);
					$model->setKey($key);
					$model->setOrderId($quoteId);
					$model->setValue($whatIWant);
					$model->save();
				}
			}else{

				$model = $this->_moduleFactory;
				$model->setKey($key);
				$model->setOrderId($quoteId);
				$model->setValue($whatIWant);
				$model->save();
			}
			return '';
    }
}
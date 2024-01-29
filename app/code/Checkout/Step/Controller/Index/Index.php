<?php
namespace Checkout\Step\Controller\Index;

use Magento\Framework\App\RequestInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_request;
	protected $_customerFactory;
	protected $customerRepository;
	protected $_json;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		CustomerRepositoryInterface $customerRepository,
		\Magento\Framework\App\Config\ScopeConfigInterface $scope_config,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		RequestInterface $request
		)
	{
		 $this->_request = $request;
		 $this->resultJsonFactory = $resultJsonFactory;
		 $this->customerRepository = $customerRepository;
		 $this->_customerFactory = $customerFactory;
		 $this->_googleAPIKey = $scope_config->getValue("system/api/google_key", \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		return parent::__construct($context);
	}

	public function execute()
	{
			$addressLocation = array();
			$result1 = $this->resultJsonFactory->create();
			$vendorsArray = array();
            $vendorsFirstArray = array();
            $vendorsSecondArray = array();
			$customerCollection = $this->_customerFactory->create()->getCollection()->addFieldToFilter('elmag_customer_kind','dealer')->addFieldToFilter('elmag_dealerview_visible','4');


			$location = $this->_request->getParam('location');
			$distance = $this->_request->getParam('distance');
            $company = $this->_request->getParam('company');
			$distanceInM = $distance * 1000;
            $useCompanyFilter = ($company !== null && $company !== '');
             if($useCompanyFilter){
                foreach($customerCollection as $key=>$customerData){
                $customerId = $customerData->getEntityId();
                $customer = $this->customerRepository->getById($customerId);
                if($customer->getCustomAttribute('elmag_dealerview_company') !== null){
                    $customerCompany= $customer->getCustomAttribute('elmag_dealerview_company')->getValue();
                }
                    $urlCompany = str_replace(' ', '', strtolower($company));
                    $checkCompanny= str_replace(' ', '', strtolower($customerCompany)); 
                    if(strpos($checkCompanny, $urlCompany) === FAlSE){ }else{
                            $vendorData = $customer;
                            $vendorLat = $customer->getCustomAttribute('elmag_dealerview_latitude')->getValue();
                            $vendorLng = $customer->getCustomAttribute('elmag_dealerview_longitude')->getValue();
                                $vendorsArray[$key]['id'] = $customerId;
                                if($customer->getCustomAttribute('elmag_dealerview_company') !== null){
                                    $vendorsArray[$key]['name'] = $customer->getCustomAttribute('elmag_dealerview_company')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_dealerview_latitude') !== null){
                                    $vendorsArray[$key]['lat'] = $customer->getCustomAttribute('elmag_dealerview_latitude')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_dealerview_longitude') !== null){
                                    $vendorsArray[$key]['lng'] = $customer->getCustomAttribute('elmag_dealerview_longitude')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_premium_partner') !== null){
                                    $vendorsArray[$key]['premium'] = $customer->getCustomAttribute('elmag_premium_partner')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_dealerview_company') !== null){
                                    $vendorsArray[$key]['company'] = $customer->getCustomAttribute('elmag_dealerview_company')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_dealerview_street') !== null){
                                        $vendorsArray[$key]['street'] = $customer->getCustomAttribute('elmag_dealerview_street')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_dealerview_zip') !== null){
                                    $vendorsArray[$key]['postal_code'] = $customer->getCustomAttribute('elmag_dealerview_zip')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_dealerview_city') !== null){
                                    $vendorsArray[$key]['city'] = $customer->getCustomAttribute('elmag_dealerview_city')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_dealerview_country') !== null){
                                    $vendorsArray[$key]['country'] = $customer->getCustomAttribute('elmag_dealerview_country')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_kontaktformular') !== null){
                                    $vendorsArray[$key]['contact_form'] = $customer->getCustomAttribute('elmag_kontaktformular')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_dealerview_phone') !== null){
                                    $vendorsArray[$key]['phone'] = $customer->getCustomAttribute('elmag_dealerview_phone')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_dealerview_additional') !== null){
                                    $vendorsArray[$key]['additional'] = $customer->getCustomAttribute('elmag_dealerview_additional')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_dealerview_fax') !== null){
                                    $vendorsArray[$key]['fax'] = $customer->getCustomAttribute('elmag_dealerview_fax')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_dealerview_email') !== null){
                                    $vendorsArray[$key]['email'] = $customer->getCustomAttribute('elmag_dealerview_email')->getValue();
                                }
                                if($customer->getCustomAttribute('elmag_dealerview_web') !== null){
                                    $vendorsArray[$key]['website'] = $customer->getCustomAttribute('elmag_dealerview_web')->getValue();
                                }
                    }
                }
			} else if($location != null && $location != '' ) {

				$url = 'https://maps.googleapis.com/maps/api/geocode/json?address='. urlencode($location) .'&sensor=false&key=AIzaSyDcMJO-opei-RtgLlH4589UTH5xmLRST5w';
            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    "header"  => "User-Agent: Nominatim-Test",
                    'method'  => 'GET'
                )
            );
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $jsonResponse = json_decode($result, true);
			if($jsonResponse['status'] === 'ZERO_RESULTS') { /* Handle error */}
				else{
					if(count($jsonResponse['results']) > 0){
						$lat = $jsonResponse['results'][0]['geometry']['location']['lat'];
                    	$lng = $jsonResponse['results'][0]['geometry']['location']['lng'];
                    	$addressLocation['lat'] = $lat;
                    	$addressLocation['lng'] = $lng;
                    	foreach($customerCollection as $key=>$customerData){
                        $customerId = $customerData->getEntityId();
                        $customer = $this->customerRepository->getById($customerId);
                            $vendorData = $customer;
                            $vendorLat = attributeFallback($customer->getCustomAttribute('elmag_dealerview_latitude'));
                            $vendorLng = attributeFallback($customer->getCustomAttribute('elmag_dealerview_longitude'));
                            if($this->haversineGreatCircleDistance($vendorLat, $vendorLng, $lat, $lng) < $distanceInM){
                                if((attributeFallback($customer->getCustomAttribute('elmag_premium_partner'))) =='11'){
                                $vendorsFirstArray[$key]['id'] = $customerId;
                                $vendorsFirstArray[$key]['name'] =attributeFallback($customer->getCustomAttribute('elmag_dealerview_company'));
                                $vendorsFirstArray[$key]['lat'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_latitude'));
                                $vendorsFirstArray[$key]['lng'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_longitude'));
                                $vendorsFirstArray[$key]['premium'] = attributeFallback($customer->getCustomAttribute('elmag_premium_partner'));
                               // $vendorsFirstArray[$key]['street'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_street')->getValue();
                                $vendorsFirstArray[$key]['postal_code'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_zip'));
                                $vendorsFirstArray[$key]['city'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_city'));
                                $vendorsFirstArray[$key]['country'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_country'));
                                $vendorsFirstArray[$key]['contact_form'] = 'static contact form';
                                $vendorsFirstArray[$key]['phone'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_phone'));
                                //$vendorsFirstArray[$key]['fax'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_fax')->getValue();
                                $vendorsFirstArray[$key]['email'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_email'));
                                $vendorsFirstArray[$key]['company'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_company'));
                               // $vendorsFirstArray[$key]['website'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_web')->getValue();
                                }else{
                                    $vendorsSecondArray[$key]['id'] = $customerId;
                                $vendorsSecondArray[$key]['name'] =attributeFallback($customer->getCustomAttribute('elmag_dealerview_company'));
                                $vendorsSecondArray[$key]['lat'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_latitude'));
                                $vendorsSecondArray[$key]['lng'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_longitude'));
                                $vendorsSecondArray[$key]['premium'] = attributeFallback($customer->getCustomAttribute('elmag_premium_partner'));
                               // $vendorsSecondArray[$key]['street'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_street')->getValue();
                                $vendorsSecondArray[$key]['postal_code'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_zip'));
                                $vendorsSecondArray[$key]['city'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_city'));
                                $vendorsSecondArray[$key]['country'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_country'));
                                $vendorsSecondArray[$key]['contact_form'] = 'static contact form';
                                $vendorsSecondArray[$key]['phone'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_phone'));
                                //$vendorsSecondArray[$key]['fax'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_fax')->getValue();
                                $vendorsSecondArray[$key]['email'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_email'));
                                $vendorsSecondArray[$key]['company'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_company'));
                               // $vendorsArray[$key]['website'] = attributeFallback($customer->getCustomAttribute('elmag_dealerview_web')->getValue();
                                }
                        	}
                    	}
                        $vendorsArray = array_merge($vendorsFirstArray,$vendorsSecondArray);
					}
				}

				 
			}
            $data = array_merge($addressLocation,$vendorsArray);
            // echo "<pre>"; print_r($data); die('test');
            return $result1->setData($data);
	}
	private function haversineGreatCircleDistance(
        $latitudeFrom,
        $longitudeFrom,
        $latitudeTo,
        $longitudeTo,
        $earthRadius = 6371000
    )
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
         $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}

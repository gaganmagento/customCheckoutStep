<?php
namespace Checkout\Step\Model\Api\Data;
interface DealerInterface
{
	public function getEntityId();
	
	public function getOrderId();
	public function setOrderId($orderId);
	
	public function getKey();
	public function setKey($key);
	
	public function getValue();
	public function setValue($value);
}
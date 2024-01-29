<?php 
namespace Checkout\Step\Model;
class Dealer extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface,
\Checkout\Step\Model\Api\Data\DealerInterface
{
	const CACHE_TAG = 'sales_order_custom';

	protected function _construct()
	{
		$this->_init('Checkout\Step\Model\ResourceModel\Dealer');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getEntityId()];
	}
	 public function getEntityId()
    {
        return $this->getData('entity_id');
    }
    public function getOrderId()
    {
        return $this->getData('order_id');
    }
    public function setOrderId($orderId)
    {
        return $this->setData('order_id', $orderId);
    }
    public function getKey()
    {
        return $this->getData('key');
    }
    public function setKey($key)
    {
        return $this->setData('key', $key);
    }
    public function getValue()
    {
        return $this->getData('value');
    }
    public function setValue($value)
    {
        return $this->setData('value', $value);
    }
}
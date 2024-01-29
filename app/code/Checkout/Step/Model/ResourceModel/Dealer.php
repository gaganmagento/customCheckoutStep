<?php
namespace Checkout\Step\Model\ResourceModel;

/**
 * Class Cars
 */
class Dealer extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Init
     */
    protected function _construct() // phpcs:ignore PSR2.Methods.MethodDeclaration
    {
        $this->_init('sales_order_custom', 'entity_id');
    }
}
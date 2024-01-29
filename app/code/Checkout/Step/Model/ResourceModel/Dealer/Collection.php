<?php
namespace Checkout\Step\Model\ResourceModel\Dealer;

use Checkout\Step\Model\Dealer as Model;
use Checkout\Step\Model\ResourceModel\Dealer as ResourceModel;

/**
 * Class Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Init
     */
    protected function _construct() // phpcs:ignore PSR2.Methods.MethodDeclaration
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
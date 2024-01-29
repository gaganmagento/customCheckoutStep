<?php
namespace Checkout\Step\Observer;

use Magento\Framework\Event\ObserverInterface;

class Sessiondestroy implements ObserverInterface
{
      public function execute(\Magento\Framework\Event\Observer $observer)
          {
            $param = 0;
          setcookie("dealer-selection",$param, time() + (86400 * 30), "/");
          if(isset($_COOKIE['refere-selection']) || $_COOKIE['refere-selection'] != 0){
            setcookie("refere-selection",$param, time() + (86400 * 30), "/");
          }
          }

}
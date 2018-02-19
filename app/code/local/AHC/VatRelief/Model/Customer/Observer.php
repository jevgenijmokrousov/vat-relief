<?php
class AHC_VatRelief_Model_Customer_Observer extends Mage_Core_Model_Abstract
{
    public function updateCustomerGroup($observer)
    {
        //Mage::getSingleton('checkout/session')->getQuote()->setCustomerGroupId(4);
    }
}
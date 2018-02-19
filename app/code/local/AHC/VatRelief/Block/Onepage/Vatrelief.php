<?php

class AHC_VatRelief_Block_Onepage_Vatrelief extends Mage_Checkout_Block_Onepage_Abstract {
    protected function _construct() {
        $this->getCheckout()->setStepData('vatrelief', array(
        'label'     => Mage::helper('checkout')->__('VAT Relief'),
        'is_show'   => true
        ));
        parent::_construct();
    }
}
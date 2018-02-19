<?php

class AHC_VatRelief_Model_Customerdata extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vatrelief/customerdata');
    }
}
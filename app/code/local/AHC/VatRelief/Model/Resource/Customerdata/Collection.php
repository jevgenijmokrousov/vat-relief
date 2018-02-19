<?php


class AHC_VatRelief_Model_Resource_Customerdata_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('vatrelief/customerdata');
    }
}
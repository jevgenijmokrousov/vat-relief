<?php
class AHC_VatRelief_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_CONFIG_PATH = 'vatrelief/settings/';
	public function isEnabled() {
		return (bool) $this->_getConfigValue('enabled');
	}

	public function setVatRelief($bool) {
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
			if($customerGroupId == '4') {
	            Mage::getSingleton('checkout/session')->getQuote()->setData('vatrelief', true);
		        Mage::getSingleton('checkout/cart')->init()->save();
		        Mage::getSingleton('checkout/session')->getQuote()->save();
				return true;
			}
		}
		if($bool) {
			Mage::getSingleton('checkout/session')->getQuote()->setCustomerGroupId('4');
		} else {
			if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
				Mage::getSingleton('checkout/session')->getQuote()->setCustomerGroupId('0');
			}
		}
        Mage::getSingleton('checkout/cart')->init()->save();
        Mage::getSingleton('checkout/session')->getQuote()->save();
		return Mage::getSingleton('checkout/session')->getQuote()->setData('vatrelief', $bool);
	}
	protected function _getConfigValue($key) {
		return Mage::getStoreConfig(self::XML_CONFIG_PATH . $key);
	}
}

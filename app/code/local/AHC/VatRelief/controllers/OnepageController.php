<?php

require_once 'Mage/Checkout/controllers/OnepageController.php';

class AHC_VatRelief_OnepageController extends Mage_Checkout_OnepageController {

    public function doSomestuffAction()
    {
        if(true) {
            $result['update_section'] = array(
                'name' => 'payment-method',
                'html' => $this->_getPaymentMethodsHtml()
            );                  
        }
        else {
            $result['goto_section'] = 'shipping';
        }       
    }    

    public function savePaymentAction()
    {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('payment', array());

             try {
                $result = $this->getOnepage()->savePayment($data);
            }
            catch (Mage_Payment_Exception $e) {
                if ($e->getFields()) {
                    $result['fields'] = $e->getFields();
                }
                $result['error'] = $e->getMessage();
            }
            catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
            $redirectUrl = $this->getOnePage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error']) && !$redirectUrl) {
                //check the module is enable or not
                if (Mage::helper('vatrelief')->isEnabled()) {
                    $this->loadLayout('checkout_onepage_vatrelief');
                    $result['goto_section'] = 'vatrelief';
                } else {
                    $this->loadLayout('checkout_onepage_review');
                    
                    $result['goto_section'] = 'review';
                    $result['update_section'] = array(
                            'name' => 'review',
                            'html' => $this->_getReviewHtml()
                    );
                }
            }

            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }

    public function saveVatReliefAction() {
        $this->_expireAjax();
        if ($this->getRequest()->isPost()) {

            //Grab the submited value 
            $_claim = $this->getRequest()->getPost('vatreliefclaim',"");
            $_type = $this->getRequest()->getPost('vatrelieftype',"");
            $_individual_name = $this->getRequest()->getPost('individual_name',"");
            $_individual_condition = $this->getRequest()->getPost('individual_condition',"");
            $_charity_name = $this->getRequest() ->getPost('charity_name',"");
            $_charity_number = $this->getRequest()->getPost('charity_number',"");

            if($_claim === 'true') {
                if($_type == 'individual' && !empty($_individual_name) && !empty($_individual_condition)) {
                    Mage::getSingleton('core/session')->setAHCVatRelief(serialize(array(
                        'vatrelieftype' =>$_type,
                        'name' =>$_individual_name,
                        'individual_condition' =>$_individual_condition
                        )));
                    Mage::helper('vatrelief')->setVatRelief(true);
                } else if($_type == 'charity' && !empty($_charity_name) && !empty($_charity_number)) {
                    Mage::getSingleton('core/session')->setAHCVatRelief(serialize(array(
                        'vatrelieftype' =>$_type,
                        'name' =>$_charity_name,
                        'charity_number' =>$_charity_number
                        )));
                    Mage::helper('vatrelief')->setVatRelief(true);
                } else {
                    Mage::helper('vatrelief')->setVatRelief(false);
                }
            } else {
                Mage::helper('vatrelief')->setVatRelief(false);
            }

            $result = array();
            $redirectUrl = $this->getOnePage()->getQuote()->getPayment() ->getCheckoutRedirectUrl();
            if (!$redirectUrl) {
                $this->loadLayout('checkout_onepage_review');
                $result['goto_section'] = 'review';
                $result['update_section'] = array(
                    'name' => 'review',
                    'html' => $this->_getReviewHtml()
                    );
            }

            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }

            $this->getResponse()->setBody(Zend_Json::encode($result));
        }
    }
}
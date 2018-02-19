<?php
class AHC_VatRelief_Model_Observer {

	const ORDER_ATTRIBUTE_FHC_ID = 'vatrelief';

	public function hookToOrderSaveEvent() {

		if (Mage::helper('vatrelief')->isEnabled()) {
			if(Mage::getSingleton('core/session')->getAHCVatRelief()) {
				$order = new Mage_Sales_Model_Order ();
				$incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
				$order->loadByIncrementId($incrementId);

				// Fetch the data 
				$_vatrelief_data = null;
				$_vatrelief_data = Mage::getSingleton('core/session')->getAHCVatRelief();
				$_vatrelief_data = unserialize($_vatrelief_data);
				$model = Mage::getModel('vatrelief/customerdata')->setData($_vatrelief_data);
				$model->setData("order_id",$order["entity_id"]);
				try {
					$insertId = $model->save ()->getId ();
					//Mage::log("Data successfully inserted. Insert ID: " . $insertId);
				} catch ( Exception $e ) {
					Mage::log("EXCEPTION " . $e->getMessage ());
				}

				if($_vatrelief_data) {

					//hq73m7end4gxjx3ii0ygdr2bc
					//
					/*
						"id":8335785171675012,"index":0,"title":"Invoice No.","type":"TEXT_NUMBER","width":119
						"id":4045765678000004,"index":1,"title":"Date","type":"DATE","width":98
						"id":5171665584842628,"index":2,"title":"Name","type":"TEXT_NUMBER","primary":true,"width":310
						"id":2919865771157380,"index":3,"title":"Address","type":"TEXT_NUMBER","width":187
						"id":6443995422320516,"index":4,"title":"Charity Number","type":"TEXT_NUMBER","width":41
						"id":7423465398527876,"index":5,"title":"Illness","type":"TEXT_NUMBER","width":150
						"id":1793965864314756,"index":6,"title":"Goods","type":"TEXT_NUMBER","width":150
						"id":5194480451118980,"index":7,"title":"Services","type":"TEXT_NUMBER","width":150
						"id":7534241195026308,"index":8,"title":"Check","type":"CHECKBOX","width":150
					*/

					$orderedItems = $order->getAllVisibleItems();
					$orderedProductIds = array();
					$address = $order->getShippingAddress()->getData();
					Mage::log(var_export($address, true), NULL, 'vatrelief.log');


					foreach ($orderedItems as $item) {
					    array_push($orderedProductIds, $item->getData('product_id'));
					}

					$productCollection = Mage::getModel('catalog/product')->getCollection();
					$productCollection->addAttributeToSelect('*');
					$productCollection->addAttributeToFilter('is_service', array('eq' => Mage::getResourceModel('catalog/product')->getAttribute('is_service')->getSource()->getOptionId('no')));
					$productCollection->addIdFilter($orderedProductIds);
					$products = array();

					foreach($productCollection as $product) {
					    $products[] = $product->getSku();
					}

					$serviceCollection = Mage::getModel('catalog/product')->getCollection();
					$serviceCollection->addAttributeToSelect('*');
					$serviceCollection->addAttributeToFilter('is_service', array('eq' => Mage::getResourceModel('catalog/product')->getAttribute('is_service')->getSource()->getOptionId('yes')));
					$serviceCollection->addIdFilter($orderedProductIds);
					$services = array();

					foreach($serviceCollection as $service) {
					    $services[] = $service->getSku();
					}

					$rows = array();
					$rowOne = new Row();
					$rowOneCells = array();

					$cellOne = new Cell();
					$cellOne->value = $order->getIncrementId();
					$cellOne->columnId = '8335785171675012';
					array_push($rowOneCells, $cellOne);

					$cellTwo = new Cell();
					$cellTwo->value = date('Y-m-d\TH:i:s\Z', time());
					$cellTwo->columnId = '4045765678000004';
					array_push($rowOneCells, $cellTwo);

					$cellThree = new Cell();
					$cellThree->value = $_vatrelief_data['name'];
					$cellThree->columnId = '5171665584842628';
					array_push($rowOneCells, $cellThree);

					$cellFour = new Cell();
					$cellFour->value = $address['street'].','.$address['city'].','.$address['country_id'].','.$address['postcode'];
					$cellFour->columnId = '2919865771157380';
					array_push($rowOneCells, $cellFour);

					$cellFive = new Cell();
					if($_vatrelief_data['vatrelieftype'] == 'charity') {
						$cellFive->value = $_vatrelief_data['charity_number'];
					} else {
						$cellFive->value = '';
					}
					$cellFive->columnId = '6443995422320516';
					array_push($rowOneCells, $cellFive);

					$cellSix = new Cell();
					if($_vatrelief_data['vatrelieftype'] == 'individual') {
						$cellSix->value = $_vatrelief_data['individual_condition'];
					} else {
						$cellSix->value = '';
					}
					$cellSix->columnId = '7423465398527876';
					array_push($rowOneCells, $cellSix);

					$cellSeven = new Cell();
					$cellSeven->value = implode(",",$products);
					$cellSeven->columnId = '1793965864314756';
					array_push($rowOneCells, $cellSeven);

					$cellEight = new Cell();
					$cellEight->value = implode(",",$services);
					$cellEight->columnId = '5194480451118980';
					array_push($rowOneCells, $cellEight);

					$rowOne->cells = $rowOneCells;
					$rowOne->toBottom = TRUE;
					array_push($rows, $rowOne);

					$post = json_encode($rows);

					$useragent="Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1";
					$url = 'https://api.smartsheet.com/2.0/sheets/8523317872224132/rows';
					$headers = array('Authorization: Bearer 3y8nogrop6s0e2ivvtcq3p2iun');
					$headers[] = 'Content-Type: application/json';
					$headers[] = 'Content-length: '.strlen($post);

					$ch = curl_init();
					curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					curl_setopt($ch, CURLOPT_POST, TRUE);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
					$data = curl_exec ($ch);
					curl_close ($ch);

					Mage::log(var_export($data, true), NULL, 'vatrelief.log');

				}

			}
		}
	}

}

// Classes
class Sheet{
    public $id;
    public $name;
    public $columns;
}
class Column{
    public $id;
    public $sheetId;
    public $title;
    public $type;
    public $symbol;
    public $primary;
    public $options;
    public $index;
} 
class ColumnModify{
    public $index;
    public $title;
    public $sheetId;
    public $type;
    public $options;
    public $symbol;
    public $systemColumnType;
    public $autoNumberFormat;
}
class Row{
    public $id;
    public $toTop;
    public $toBottom;
    public $cells;
}
class Cell{
    public $value;
    public $displayValue;
    public $columnId;
    public $strict;
}
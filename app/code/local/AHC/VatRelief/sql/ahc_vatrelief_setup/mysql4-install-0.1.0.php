<?php

$installer = $this;
 
$installer->startSetup();
 
$installer->run("
DROP TABLE IF EXISTS {$installer->getTable('vatrelief/customerdata')};
CREATE TABLE {$installer->getTable('vatrelief/customerdata')} (
 `id` int(11) unsigned NOT NULL auto_increment,
  `vatrelieftype` varchar(100),
  `name` varchar(100),
  `individual_condition` varchar(255) NULL,
  `charity_number`varchar(255) NULL,
  `order_id` int(11) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();

<?php
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE `{$installer->getTable('sales/quote_payment')}` 
ADD `mbway_id_pedido` VARCHAR( 255 ) NOT NULL,
ADD `mbway_phone` VARCHAR( 255 ) NOT NULL;
  
ALTER TABLE `{$installer->getTable('sales/order_payment')}` 
ADD `mbway_id_pedido` VARCHAR( 255 ) NOT NULL,
ADD `mbway_phone` VARCHAR( 255 ) NOT NULL;
");

$installer->run("CREATE TABLE IF NOT EXISTS `ifthenpay_mbway_config` (`id` int(11) NOT NULL AUTO_INCREMENT, `antiphishing` varchar(50) DEFAULT NULL, PRIMARY KEY (`id`))");

$installer->endSetup();

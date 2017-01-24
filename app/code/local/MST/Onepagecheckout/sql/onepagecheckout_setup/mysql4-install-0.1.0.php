<?php

$installer = $this;

$installer->startSetup();

$installer->run("
	CREATE TABLE IF NOT EXISTS {$this->getTable('onepagecheckout_suvery')} (
          `id` int(11) unsigned NOT NULL auto_increment,
          `order_id` varchar(255) NOT NUlL default '',
          `content` varchar(255) NOT NUlL default '',
          `created_time` datetime NULL,
          `update_time` datetime NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	CREATE TABLE IF NOT EXISTS {$this->getTable('onepagecheckout_delivery')} (
          `id` int(11) unsigned NOT NULL auto_increment,
          `order_id` varchar(255) NOT NUlL default '',
          `date` varchar(255) NOT NUlL default '',
          `time` varchar(255) NOT NUlL default '',
          `created_time` datetime NULL,
          `update_time` datetime NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		

		
    ");

$installer->endSetup(); 
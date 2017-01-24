<?php
$installer = $this;
$installer->startSetup();
$installer->run("
		DROP TABLE IF EXISTS {$this->getTable('liveagentbuttons')};
		DROP TABLE IF EXISTS {$this->getTable('liveagentsettings')};
		CREATE TABLE {$this->getTable('liveagentsettings')} (
		`id` int(11) unsigned NOT NULL auto_increment,
		`name` varchar(255) NOT NULL,
		`value` text DEFAULT NULL,
		PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
$installer->endSetup();
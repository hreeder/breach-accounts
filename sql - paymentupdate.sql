CREATE TABLE IF NOT EXISTS `evt_paymentlog` (
  `id` int NOT NULL auto_increment,
  `pct_instance_ref` int default NULL,
  `transactionid` varchar(200) default NULL,
  `paymenttype` varchar(20) default NULL,
  `ordertimestamp` varchar(50) default NULL,
  `totalamount` int default NULL,
  `currency` varchar(3) default NULL,
  `paymentstaus` varchar(250) default NULL,
  `pendingreason` varchar(250) default NULL,
  `pendingcode` varchar(250) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;
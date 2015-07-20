CREATE TABLE  IF NOT EXISTS  `tp_redcash_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `min_value` decimal(10,2) DEFAULT NULL,
  `max_value` decimal(10,2) DEFAULT NULL,
  `fixed_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('0','1','2','3') DEFAULT '0',
  `nick_name` varchar(50) NOT NULL,
  `send_name` varchar(50) NOT NULL,
  `wishing` varchar(50) NOT NULL,
  `act_name` varchar(50) NOT NULL,
  `remark` varchar(100) NOT NULL,
  `token` varchar(30) NOT NULL,
  `start_time` varchar(30) DEFAULT NULL,
  `end_time` varchar(30) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `keyword` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT  EXISTS  `tp_redcash_wxconf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appid` varchar(60) NOT NULL,
  `key` varchar(32) NOT NULL,
  `mchid` varchar(30) NOT NULL,
  `token` varchar(30) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `ssl_cert` varchar(200) DEFAULT '',
  `ssl_key` varchar(200) DEFAULT '',
  `ssl_cainfo` varchar(200) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

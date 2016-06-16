CREATE TABLE `ecs_favourable_act_detail` (
  `act_detail_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `act_id` int(10) unsigned NOT NULL COMMENT '关联活动编号',   
  `buy_amt_number` decimal(10,2) NOT NULL COMMENT '买几件，满减、折扣消费金额',
  `act_amt_number` decimal(10,2) NOT NULL DEFAULT '1' COMMENT '送几件,减少金额，折扣',
  `is_double_give` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否倍增',
  `is_other_goods` varchar(255) NOT NULL DEFAULT '0' COMMENT '是否赠送其它商品',
  `other_goods_id` int(10) unsigned DEFAULT NULL COMMENT '赠送其它商品编号',
  `max_act_number` tinyint(2) NOT NULL DEFAULT '-1' COMMENT '最大赠送数量，最大折扣，减少金额',   
  PRIMARY KEY (`act_detail_id`),
  CONSTRAINT `fk_act_id_ref_fav_act` FOREIGN KEY (`act_id`) REFERENCES `ecs_favourable_activity` (`act_id`),
  CONSTRAINT `fk_other_goods_id_ref_fav_act` FOREIGN KEY (`other_goods_id`) REFERENCES `ecs_goods` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='满减，折扣，买几送几促销活动';


CREATE TABLE `ecs_favourable_act_detail_package` (
  `package_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '礼包编号',
  `act_detail_id` int(10) unsigned NOT NULL COMMENT '关联买赠编号',
  `give_number_activity` tinyint(2) NOT NULL DEFAULT '1' COMMENT '送几件',
  `other_goods_id` mediumint(8) unsigned DEFAULT NULL COMMENT '赠送其它商品编号',
  PRIMARY KEY (`package_id`),
  CONSTRAINT `fk_detail_id_ref_act_detail` FOREIGN KEY (`act_detail_id`) REFERENCES `ecs_favourable_act_detail` (`act_detail_id`),
  CONSTRAINT `fk_other_goods_id_ref_fav_act1` FOREIGN KEY (`other_goods_id`) REFERENCES `ecs_goods` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='优惠活动，买几送几促销赠送礼包';
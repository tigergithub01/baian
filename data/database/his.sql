CREATE TABLE `ecs_buy_give_activity` (	
  `buy_give_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品编号',  
  `buy_number_activity` tinyint(2) NOT NULL COMMENT '买几件',
  `give_number_activity` tinyint(2) NOT NULL COMMENT '送几件' default 1,
  `is_double_give` tinyint(1) NOT NULL COMMENT '是否倍数赠送' default 0,
  `is_other_goods` varchar(255) NOT NULL COMMENT '是否赠送其它商品' default 0,
  `other_goods_id` int(10) unsigned NULL COMMENT '赠送其它商品编号',   
  PRIMARY KEY (`buy_give_id`),
 CONSTRAINT `fk_goods_id_ref_goods` FOREIGN KEY (`goods_id`) REFERENCES `ecs_goods` (`goods_id`),
 CONSTRAINT `fk_other_goods_id_ref_goods` FOREIGN KEY (`other_goods_id`) REFERENCES `ecs_goods` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='买几送几促销活动';

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



CREATE TABLE `ecs_order_back` (	
  `back_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `back_sn` varchar(255) NOT NULL COMMENT '退货申请序列号',   
  `order_sn` varchar(20) NOT NULL COMMENT '订单号',  
  `order_id` mediumint(8) unsigned NOT NULL COMMENT '订单编号',
  `goods_id` mediumint(8) unsigned NULL COMMENT '商品编号',
  `add_time` int(10) NOT NULL COMMENT '申请时间' default 1,
  `reason`  varchar(400)  NULL COMMENT '申请退货理由',
  `user_id` mediumint(8) unsigned NOT NULL COMMENT '申请人',
  `status` varchar(255)  NULL COMMENT '申请状态：0->待审核；1->审核通过;2->审核不通过;3->已撤销,4->已寄出' default 0,
  `invoice_no`  varchar(255)  NULL COMMENT '快递单号',
  `audit_time` int(10)  NULL COMMENT '审核时间（待确定，可以记录ecs_order_action）',   
  `audit_admin_user_id` int(10)  NULL COMMENT '审核管理员（待确定，可以记录ecs_order_action）', 
  `audit_desc` int(10)  NULL COMMENT '审核意见（待确定，可以记录ecs_order_action）',   
  PRIMARY KEY (`back_id`),
 CONSTRAINT `fk_order_id_ref_order` FOREIGN KEY (`order_id`) REFERENCES `ecs_order_info` (`order_id`),
 CONSTRAINT `fk_goods_id_ref_goods` FOREIGN KEY (`goods_id`) REFERENCES `ecs_goods` (`goods_id`),
 CONSTRAINT `fk_user_id_ref_user` FOREIGN KEY (`user_id`) REFERENCES `ecs_users` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='退货申请';

alter table ecs_comment add order_id mediumint(8) unsigned  NULL COMMENT '订单编号';

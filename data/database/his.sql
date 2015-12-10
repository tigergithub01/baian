ALTER TABLE ecs_goods_storeroom add pick_out_point varchar(2000) comment '自提点对应区域';

CREATE TABLE `ecs_gift_giving` (
  `giving_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `user_id` mediumint(8) unsigned NOT NULL COMMENT '申请人',
  `apply_time` int(10) unsigned NOT NULL COMMENT '申请时间',  
  `send_flag` smallint(5) unsigned NOT NULL COMMENT '是否发送' default 0,  
  `sent_time` int(10) unsigned NULL COMMENT '发送时间',
  `order_sn` varchar(255)  NULL COMMENT '关联订单编号',
  `sent_memo` varchar(200) NULL COMMENT '发送描述',
  PRIMARY KEY (`giving_id`),
 CONSTRAINT `fk_gift_giving_ref_user` FOREIGN KEY (`user_id`) REFERENCES `ecs_users` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='礼品索要';

ALTER TABLE ecs_payment add cod_area varchar(2000) comment '货到付款区域';


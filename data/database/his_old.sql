alter table ecs_ad add description varchar(100) null COMMENT '描述，暂时用来存放背景颜色' ;

ALTER TABLE `baianshop`.`ecs_goods` CHANGE COLUMN `goods_title` `goods_title` VARCHAR(120) NULL ;


--baianAdmin@2015
update ecs_admin_user set password = '65a6b21cfb2035884dee86ec670b0fd8' where user_name = 'baian';
update ecs_admin_user set password = '802c8aaf2cd227bd63bc547465eb0c01' where user_name = 'baianbaby123';


alter table ecs_goods add reduce_ship_amt decimal(10,2) COMMENT '可减运费金额' ; 
alter table ecs_goods add give_number_activity tinyint(2) COMMENT '买满赠送件数' ; 

alter table ecs_goods_attr add attr_product_sn varchar(60) COMMENT '货号' ; 
alter table ecs_goods_attr add attr_product_number SMALLINT(5) COMMENT '库存'; 


CREATE TABLE `ecs_goods_attr_sku` (
  `sku_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `attr_price` decimal(10,2) COMMENT '价格',
  `stock_quantity` decimal(10,2) COMMENT '库存',
  `goods_sn` varchar(60) COMMENT '货号',
  `warsehouse_id` int(10) COMMENT '仓库',
  PRIMARY KEY (`sku_id`)
) ENGINE=MyISAM AUTO_INCREMENT=476 DEFAULT CHARSET=utf8 COMMENT='商品sku信息表';


CREATE TABLE `ecs_goods_attr_sku_detail` (
  `sku_detail_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `sku_id` int(10) unsigned NOT NULL COMMENT '关联sku编号',
  `goods_attr_id` int(10) unsigned NOT NULL COMMENT '商品属性编号',  
  PRIMARY KEY (`sku_detail_id`),
 CONSTRAINT `fk_sku_detail_detail_ref_sku` FOREIGN KEY (`sku_id`) REFERENCES `ecs_goods_attr_sku` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=476 DEFAULT CHARSET=utf8 COMMENT='商品sku明细表';

alter table ecs_users add last_sign_time int(10) COMMENT '最后一次签到时间';


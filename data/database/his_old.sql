CREATE USER 'baian'@'%' IDENTIFIED BY 'BaiAnShop2015';
CREATE DATABASE `baianshop` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT all ON baianshop.* TO 'baian'@'%';

CREATE DATABASE `baianwx` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT all ON baianwx.* TO 'baian'@'%';

CREATE DATABASE `baianmy14` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT all ON baianmy14.* TO 'baian'@'%';

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

alter table ecs_goods add pinyin VARCHAR(60) COMMENT '商品拼音码' ; 

alter table ecs_users add is_validated_phone tinyint(3) COMMENT '手机号码是否已经验证?1:是;0:否';

alter table ecs_cart add is_checked tinyint(3) COMMENT '是否选中?1:是;0:否';

INSERT INTO `ecs_shop_config`
(
`parent_id`,
`code`,
`type`,
`store_range`,
`store_dir`,
`value`,
`sort_order`)
VALUES
(
1,
'shop_district',
'manual',
'',
'',
'',
1);

INSERT INTO `ecs_shop_config`
(
`parent_id`,
`code`,
`type`,
`store_range`,
`store_dir`,
`value`,
`sort_order`)
VALUES
(
1,
'shop_town',
'manual',
'',
'',
'',
1);


alter table ecs_products add product_price decimal(10,2) COMMENT '货品价格';
alter table ecs_products add store_id smallint(5)  COMMENT '仓库编号，关联表ecs_goods_storeroom.store_id';


CREATE TABLE `ecs_products_gallery` (
  `img_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `img_url` varchar(255) NOT NULL DEFAULT '',
  `img_desc` varchar(255) NOT NULL DEFAULT '',
  `thumb_url` varchar(255) NOT NULL DEFAULT '',
  `img_original` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`img_id`),
  CONSTRAINT `fk_product_gallery_ref_products` FOREIGN KEY (`product_id`) REFERENCES `ecs_products` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 comment '产品相册';



INSERT INTO `ecs_ad_position`
(`position_name`,`ad_width`,`ad_height`,`position_desc`,`position_style`)
VALUES
(
'导航菜单孕妈专区',225,150,'',
'<table cellpadding="0" cellspacing="0">
 {foreach from=$ads item=ad}
 <tr><td>{$ad}</td></tr>
 {/foreach}
 </table>');

INSERT INTO `ecs_ad_position`
(`position_name`,`ad_width`,`ad_height`,`position_desc`,`position_style`)
VALUES
(
'导航菜单宝宝食品',225,150,'',
'<table cellpadding="0" cellspacing="0">
 {foreach from=$ads item=ad}
 <tr><td>{$ad}</td></tr>
 {/foreach}
 </table>');

INSERT INTO `ecs_ad_position`
(`position_name`,`ad_width`,`ad_height`,`position_desc`,`position_style`)
VALUES
(
'导航菜单宝宝用品',225,150,'',
'<table cellpadding="0" cellspacing="0">
 {foreach from=$ads item=ad}
 <tr><td>{$ad}</td></tr>
 {/foreach}
 </table>');


INSERT INTO `ecs_ad_position`
(`position_name`,`ad_width`,`ad_height`,`position_desc`,`position_style`)
VALUES
(
'导航菜单婴童服饰',225,150,'',
'<table cellpadding="0" cellspacing="0">
 {foreach from=$ads item=ad}
 <tr><td>{$ad}</td></tr>
 {/foreach}
 </table>');

INSERT INTO `ecs_ad_position`
(`position_name`,`ad_width`,`ad_height`,`position_desc`,`position_style`)
VALUES
(
'导航菜单童车童床',225,150,'',
'<table cellpadding="0" cellspacing="0">
 {foreach from=$ads item=ad}
 <tr><td>{$ad}</td></tr>
 {/foreach}
 </table>');


INSERT INTO `ecs_ad_position`
(`position_name`,`ad_width`,`ad_height`,`position_desc`,`position_style`)
VALUES
(
'导航菜单图书玩具',225,150,'',
'<table cellpadding="0" cellspacing="0">
 {foreach from=$ads item=ad}
 <tr><td>{$ad}</td></tr>
 {/foreach}
 </table>');

INSERT INTO `ecs_ad_position`
(`position_name`,`ad_width`,`ad_height`,`position_desc`,`position_style`)
VALUES
(
'导航菜单家居百货',225,150,'',
'<table cellpadding="0" cellspacing="0">
 {foreach from=$ads item=ad}
 <tr><td>{$ad}</td></tr>
 {/foreach}
 </table>');
 
 
 /*alter table ecs_attribute add attr_img_url varchar(255) comment  '属性默认图片';*/
alter table ecs_goods_attr add attr_img_url varchar(255) comment  '属性值对应图片';


CREATE TABLE `ecs_products_attr` (
  `product_attr_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `product_id` int(10) unsigned NOT NULL COMMENT '关联产品编号',
  `goods_attr_id` int(10) unsigned NOT NULL COMMENT '商品属性编号',  
  PRIMARY KEY (`product_attr_id`),
 CONSTRAINT `fk_ecs_products_attr_ref_prod` FOREIGN KEY (`product_id`) REFERENCES `ecs_products` (`product_id`),
 CONSTRAINT `fk_ecs_products_attr_ref_p_attr` FOREIGN KEY (`goods_attr_id`) REFERENCES `ecs_goods_attr` (`goods_attr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=476 DEFAULT CHARSET=utf8 COMMENT='产品属性明细表';


ALTER TABLE ecs_products ADD seq_index tinyint(3)  COMMENT '产品显示序号，用来设置默认显示产品' default 1;

CREATE TABLE `ecs_products_store` (
  `product_store_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `product_id` int(10) unsigned NOT NULL COMMENT '关联产品编号',
  `store_id` int(10) unsigned NOT NULL COMMENT '关联仓库编号',  
  `product_number` smallint(5) unsigned NOT NULL COMMENT '产品库存' default 0,  
  PRIMARY KEY (`product_store_id`),
 CONSTRAINT `fk_products_store_ref_prod` FOREIGN KEY (`product_id`) REFERENCES `ecs_products` (`product_id`),
 CONSTRAINT `fk_products_store_ref_store` FOREIGN KEY (`store_id`) REFERENCES `ecs_goods_storeroom` (`store_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='产品库存表';

alter table ecs_products drop column store_id;


ALTER TABLE ecs_products ADD is_promote tinyint(1) default 0 comment '是否促销';
ALTER TABLE ecs_products ADD promote_start_date  int(11) comment '促销开始日期';
ALTER TABLE ecs_products ADD promote_end_date  int(11) comment '促销结束日期';
ALTER TABLE ecs_products ADD promote_price  decimal(10,2) comment '促销价';
ALTER TABLE ecs_products ADD is_default  tinyint(1) default 0 comment '是否默认规格产品';

CREATE TABLE `ecs_goods_store` (
  `goods_store_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `goods_id` int(10) unsigned NOT NULL COMMENT '关联商品编号',
  `store_id` int(10) unsigned NOT NULL COMMENT '关联仓库编号',  
  `goods_number` smallint(5) unsigned NOT NULL COMMENT '商品库存' default 0,  
  PRIMARY KEY (`goods_store_id`),
 CONSTRAINT `fk_goods_store_ref_goods` FOREIGN KEY (`goods_id`) REFERENCES `ecs_goods` (`goods_id`),
 CONSTRAINT `fk_goods_store_ref_store` FOREIGN KEY (`store_id`) REFERENCES `ecs_goods_storeroom` (`store_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品分仓库库存表';

alter table ecs_package_goods add goods_price decimal(10,2) comment '组合套装单品价格';

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


CREATE TABLE `ecs_comment_photo` (	
  `comment_photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `comment_id` int(10) unsigned NOT NULL COMMENT '评论编号',
  `img_url` varchar(255)  NULL COMMENT '评论图片',
  `thumb_url` varchar(255)  NULL COMMENT '缩略图',
  `img_original` varchar(255)  NULL COMMENT '原始图片',   
  PRIMARY KEY (`comment_photo_id`),
 CONSTRAINT `fk_comment_photo_ref_comment` FOREIGN KEY (`comment_id`) REFERENCES `ecs_comment` (`comment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='评论图片';

alter table ecs_shipping_area add detail_addr varchar(500) comment '详细地址';


alter table ecs_goods add  gift_goods_flag  tinyint(1) default 0  comment '是否赠送其他商品';
alter table ecs_goods add  gift_goods_id mediumint(8)  unsigned  comment '赠送其他商品编号';


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
  `audit_desc` varchar(255)  NULL COMMENT '审核意见（待确定，可以记录ecs_order_action）',   
  PRIMARY KEY (`back_id`),
 CONSTRAINT `fk_order_id_ref_order` FOREIGN KEY (`order_id`) REFERENCES `ecs_order_info` (`order_id`),
 CONSTRAINT `fk_goods_id_ref_goods` FOREIGN KEY (`goods_id`) REFERENCES `ecs_goods` (`goods_id`),
 CONSTRAINT `fk_user_id_ref_user` FOREIGN KEY (`user_id`) REFERENCES `ecs_users` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='退货申请';

alter table ecs_comment add order_id mediumint(8) unsigned  NULL COMMENT '订单编号';


CREATE TABLE `ecs_pick_up_point` (
  `point_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自提点编号',
  `point_name` varchar(255) NOT NULL COMMENT '自提点名称',
  `point_addr` varchar(255) NOT NULL  COMMENT '自提点地址',
  `regions` varchar(2000) DEFAULT NULL  COMMENT '自提点覆盖区域',
  `status` tinyint(1) DEFAULT 1 NOT NULL COMMENT '状态:1->有效;0->无效',   
  PRIMARY KEY (`point_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8  COMMENT='自提点';

alter table ecs_order_info add point_id int(10) comment '自提点';


alter table ecs_user_address modify country smallint unsigned NOT NULL;
alter table ecs_user_address modify province smallint unsigned  NOT NULL;
alter table ecs_user_address modify city smallint unsigned  NOT NULL;
alter table ecs_user_address modify district smallint unsigned NOT NULL;

alter table ecs_cart modify  `goods_attr_id` varchar(255) NOT NULL DEFAULT '';
alter table ecs_goods add is_buy_gift tinyint(1) default 0 comment '买几送几' ;
alter table ecs_goods add gift_start_date int(11)  comment '买几送几开始日期' ;
alter table ecs_goods add gift_end_date int(11)  comment '买几送几结束日期' ;

CREATE TABLE `ecs_buy_give_activity` (	
  `buy_give_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品编号',  
  `buy_number_activity` tinyint(2) NOT NULL COMMENT '买几件',
  `give_number_activity` tinyint(2) NOT NULL COMMENT '送几件' default 1,
  `is_double_give` tinyint(1) NOT NULL COMMENT '是否倍数赠送' default 1,  
  `other_goods_id` int(10) unsigned NULL COMMENT '赠送其它商品编号',   
  PRIMARY KEY (`buy_give_id`),
 CONSTRAINT `fk_goods_id_ref_goods` FOREIGN KEY (`goods_id`) REFERENCES `ecs_goods` (`goods_id`),
 CONSTRAINT `fk_other_goods_id_ref_goods` FOREIGN KEY (`other_goods_id`) REFERENCES `ecs_goods` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='买几送几促销活动';


alter table ecs_goods drop column buy_number_activity; 
alter table ecs_goods drop column give_number_activity;
alter table ecs_goods drop column gift_goods_flag;
alter table ecs_goods drop column gift_goods_id;

ALTER TABLE ecs_buy_give_activity ADD max_give_number tinyint(2) NOT NULL COMMENT '最大赠送数量' default -1;

alter table ecs_order_back drop column goods_id;
alter table ecs_order_back drop column audit_time;
alter table ecs_order_back drop column audit_admin_user_id;
alter table ecs_order_back drop column audit_desc;

alter table ecs_user_rank add birthday_gift tinyint(1) unsigned comment '是否可赠送宝宝生日礼物' default 0;alter table ecs_user_rank add is_birth_gift tinyint(1) unsigned comment '是否可赠送宝宝生日礼物' default 0;

alter table ecs_users add photo_url varchar(255) comment '照片';
alter table ecs_users add baby_photo_url varchar(255) comment '宝宝照片';

INSERT INTO `ecs_shop_config`
(
`parent_id`,
`code`,
`type`,
`store_range`,
`store_dir`,
`value`,
`sort_order`)
VALUES
(
4,
'cancel_order_hours',
'text',
'',
'',
'48',
1);

alter table ecs_category add give_integral int(11) comment '赠送消费积分数';
alter table ecs_category add rank_integral int(11) comment '赠送等级积分数';
alter table ecs_category add integral int(10) unsigned comment '积分购买金额';

update ecs_category set give_integral = -1;
update ecs_category set rank_integral = -1;
update ecs_category set integral = 0;

CREATE TABLE `ecs_sms_code` (
  `sms_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `phone_number` varchar(15) NOT NULL COMMENT '手机号码',
  `code_type` int(11) DEFAULT NULL COMMENT '验证码用途类型（注册、找回密码、绑定手机号码）',
  `sent_time` int(10) unsigned NOT NULL COMMENT '发送时间',
  `expiration_time` int(10) unsigned DEFAULT NULL COMMENT '过期时间',
  `verify_code` varchar(10) NOT NULL COMMENT '验证码',
  `sms_content` varchar(200) DEFAULT NULL COMMENT '手机短信内容',  
  PRIMARY KEY (`sms_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='手机验证码';


CREATE TABLE `ecs_email_code` (
  `email_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键编号',
  `email` varchar(15) NOT NULL COMMENT '邮箱地址',
  `code_type` int(11) DEFAULT NULL COMMENT '验证码用途类型（注册、找回密码、绑定手机号码）',
  `sent_time` int(10) unsigned NOT NULL COMMENT '发送时间',
  `expiration_time` int(10) unsigned DEFAULT NULL COMMENT '过期时间',
  `verify_code` varchar(10) NOT NULL COMMENT '验证码',
  `email_content` varchar(200) DEFAULT NULL COMMENT '邮件内容',  
  PRIMARY KEY (`email_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='邮箱验证码';


ALTER TABLE ecs_sms_code ADD is_success tinyint(1) unsigned default 0 comment '是否成功?1:是,0:否';

ALTER TABLE ecs_sms_code ADD err_msg VARCHAR(500)  comment '发送失败时的返回消息';

CREATE TABLE `ecs_buy_give_package` (
  `package_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '礼包编号',
  `buy_give_id` int(10) unsigned NOT NULL COMMENT '关联买赠编号',
  `give_number_activity` tinyint(2) NOT NULL DEFAULT '1' COMMENT '送几件',
  `is_other_goods` varchar(255) NOT NULL DEFAULT '0' COMMENT '是否赠送其它商品',
  `other_goods_id` mediumint(8) unsigned DEFAULT NULL COMMENT '赠送其它商品编号',
  PRIMARY KEY (`package_id`),
  CONSTRAINT `fk_bg_id_ref_bg_goods` FOREIGN KEY (`buy_give_id`) REFERENCES `ecs_buy_give_activity` (`buy_give_id`),
  CONSTRAINT `fk_other_goods_id_ref_goods1` FOREIGN KEY (`other_goods_id`) REFERENCES `ecs_goods` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='买几送几促销赠送礼包';


alter table ecs_order_back add invoice_name varchar(200)  NULL COMMENT '快递公司';

alter table ecs_users add photo_thumb_url varchar(255) comment '照片-缩略图';
alter table ecs_users add baby_photo_thumb_url varchar(255) comment '宝宝照片-缩略图';
update ecs_users set photo_thumb_url = photo_url where photo_thumb_url is null and photo_url is not null;
update ecs_users set baby_photo_thumb_url = baby_photo_url where baby_photo_thumb_url is null and baby_photo_url is not null;

INSERT INTO `ecs_shop_config`
(
`parent_id`,
`code`,
`type`,
`store_range`,
`store_dir`,
`value`,
`sort_order`)
VALUES
(
4,
'receive_order_days',
'text',
'',
'',
'10',
1);



update ecs_shop_config set store_range = '-1,0,64,128,256,512,1024,2048,4096,5120,6144,8192' where code = 'upload_size_limit';

alter table ecs_goods add promote_limit_num int(11) null COMMENT '每日限购数量' default -1 ;

CREATE TABLE `ecs_category_ad` (
  `img_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `img_url` varchar(255) NOT NULL DEFAULT '',
  `img_desc` varchar(255) NOT NULL DEFAULT '',
  `thumb_url` varchar(255) NOT NULL DEFAULT '',
  `img_original` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`img_id`),
 CONSTRAINT `fk_cat_ad_ref_cat` FOREIGN KEY (`cat_id`) REFERENCES `ecs_category` (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品类别广告图';

alter table ecs_category_ad add ad_img_url varchar(255) null COMMENT '分类广告图' ;
alter table ecs_category_ad add ad_img_original varchar(255) null COMMENT '分类广告原始图' ;
alter table ecs_category_ad add ad_thumb_url varchar(255) null COMMENT '分类广告缩略图' ;


 
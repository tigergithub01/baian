alter table ecs_ad add description varchar(100) null COMMENT '描述，暂时用来存放背景颜色' ;

ALTER TABLE `baianshop`.`ecs_goods` CHANGE COLUMN `goods_title` `goods_title` VARCHAR(120) NULL ;


--baianAdmin@2015
update ecs_admin_user set password = '65a6b21cfb2035884dee86ec670b0fd8' where user_name = 'baian';
update ecs_admin_user set password = '802c8aaf2cd227bd63bc547465eb0c01' where user_name = 'baianbaby123';


alter table ecs_goods add reduce_ship_amt decimal(10,2) COMMENT '可减运费金额' ; 
alter table ecs_goods add give_number_activity tinyint(2) COMMENT '买满赠送件数' ; 

alter table ecs_goods_attr add attr_product_sn varchar(60) COMMENT '货号' ; 
alter table ecs_goods_attr add attr_product_number SMALLINT(5) COMMENT '库存'; 



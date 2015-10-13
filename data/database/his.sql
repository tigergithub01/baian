


alter table ecs_goods add reduce_ship_amt decimal(10,2) COMMENT '可减运费金额' ; 
alter table ecs_goods add give_number_activity tinyint(2) COMMENT '买满赠送件数' ; 

alter table ecs_goods_attr add attr_product_sn varchar(60) COMMENT '货号' ; 
alter table ecs_goods_attr add attr_product_number SMALLINT(5) COMMENT '库存'; 

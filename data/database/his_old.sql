alter table ecs_ad add description varchar(100) null COMMENT '描述，暂时用来存放背景颜色' ;

ALTER TABLE `baianshop`.`ecs_goods` CHANGE COLUMN `goods_title` `goods_title` VARCHAR(120) NULL ;
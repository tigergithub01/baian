


alter table ecs_user_bonus add used_amount DECIMAL(10,2) unsigned comment  '红包已经使用金额' default 0;

alter table ecs_order_info add bonus_used_amount text comment  '红包已经使用金额';


CREATE TABLE `ecs_bonus_used_log` (
  `log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_id` mediumint(8) unsigned NOT NULL,
  `order_id` mediumint(8) unsigned NOT NULL,
  `used_money` decimal(10,2) NOT NULL,
  `use_time` int(10) unsigned NOT NULL,
  `use_desc` varchar(255) NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8  COMMENT='红包使用明细';
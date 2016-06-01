


alter table ecs_user_bonus add used_amount DECIMAL(10,2) unsigned comment  '红包已经使用金额' default 0;

alter table ecs_order_info add bonus_used_amount text comment  '红包已经使用金额';

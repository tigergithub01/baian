alter table ecs_user_rank add birthday_gift tinyint(1) unsigned comment '是否可赠送宝宝生日礼物' default 0;alter table ecs_user_rank add is_birth_gift tinyint(1) unsigned comment '是否可赠送宝宝生日礼物' default 0;

alter table ecs_users add photo_url varchar(255) comment '照片';
alter table ecs_users add baby_photo_url varchar(255) comment '宝宝照片';
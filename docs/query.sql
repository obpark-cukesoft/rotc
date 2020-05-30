select * from users where id=1;
select id, name_ko from codes where parent_id = 2 and name_ko like '서%';

select photo_id from members where id = 17;

select * from `files` where `source_type` = 1 and `files`.`source_id` in (18)

truncate table rotc_dev.posts;

/* create database rotc;
grant all privileges on rotc.* to rotc@localhost identified by '@@Advsvr@@';
grant all privileges on rotc.* to rotc@'%' identified by '@@Advsvr@@';
mysql -urotc -p@@Advsvr@@ -h114.108.181.142 */


create database rotc_dev;
grant all privileges on rotc_dev.* to rotc@localhost identified by '@@Advsvr@@';
grant all privileges on rotc_dev.* to rotc@'%' identified by '@@Advsvr@@';
mysql -urotc -p@@Advsvr@@ -h114.108.181.142



drop table users;
create table users
(
    id                bigint unsigned auto_increment primary key,
    name              varchar(255)     not null,
    email             varchar(255)     not null,
    email_verified_at timestamp        null,
    password          varchar(255)     not null,
    remember_token    varchar(100)     null,
    level             tinyint unsigned COMMENT '로그인 level 관리자:1, 회원: 10',
    status            char default 'N' null comment '계정 상태(N:정상, R:등록, S:중지)',
    created_at        timestamp        null,
    updated_at        timestamp        null,
    constraint users_email_unique unique (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='로그인 정보';
insert into users value (null, '관리자', 'admin@admin.com', null, '$2y$10$eh5BYSDTpVDkP7cYRM8IOum2GK1OQiVkHRRMXwzSLMTVI/puXJR9y', null, 1, 'N', NOW(), NOW());
insert into users value (null, '회원테스트', 'member@member.com', null, '$2y$10$eh5BYSDTpVDkP7cYRM8IOum2GK1OQiVkHRRMXwzSLMTVI/puXJR9y', null, 10, 'N', NOW(), NOW());

create table member_profiles (
    id                 bigint unsigned primary key,
    cardinal_numeral   tinyint unsigned comment 'ROTC 기수: 1 ~ ',
    school_id          bigint unsigned comment '출신학교코드',
    note               varchar(255) comment '상태메모',
    company            varchar(255) comment '상호',
    part               varchar(255) comment '부서',
    duty               varchar(255) comment '직급',
    mobile             varchar(255) comment '휴대폰',
    url                varchar(255) comment 'url',
    photo_id           bigint unsigned comment '사진 id', -- 임시 추후 삭제
    photo_path         varchar(255) comment '사진 path', -- 임시 추후 삭제
    business_card_id   bigint unsigned comment '명함 id', -- 임시 추후 삭제
    business_card_path varchar(255) comment '명함 path', -- 임시 추후 삭제
    push_id            varchar(255) comment 'device id',
    gps                point        comment 'GPS 정보',
    gps_address        varchar(255) comment 'GPS 정보의 주소',
    gps_updated_at     timestamp    comment 'GPS 갱신 시각',
    gps_usage          int default 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='회원 정보';

DROP FUNCTION IF EXISTS st_distance_sphere;
CREATE FUNCTION `st_distance_sphere`(`pt1` point, `pt2` point)
    RETURNS DECIMAL(10,2)
BEGIN
    RETURN 6371000 * 2 * ASIN(SQRT(POWER(SIN((ST_Y(pt2) - ST_Y(pt1)) * pi() / 180 / 2), 2) + COS(ST_Y(pt1) * pi() / 180) * COS(ST_Y(pt2) * pi() / 180) * POWER(SIN((ST_X(pt2) - ST_X(pt1)) * pi() / 180 / 2), 2)));
END;

drop view members;
create view members as
select
    a.*,
    b.cardinal_numeral,
    b.school_id,
    b.note,
    b.company,
    b.part,
    b.duty,
    b.mobile,
    b.url,
    b.photo_id, b.photo_path,
    b.business_card_id, b.business_card_path,
    b.push_id,
    b.gps,
    b.gps_address,
    b.gps_updated_at,
    b.gps_usage
from users a
    left join member_profiles b on a.id = b.id
where a.level = 10;


drop table `files`;
create table `files` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  source_type bigint unsigned,
  source_id bigint unsigned,
  name varchar(255) comment '경로',
  path varchar(255) comment '경로',
  size int unsigned comment '사이즈',
  type varchar(255) comment '타입',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


drop table `codes`;
create table `codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int unsigned DEFAULT NULL,
  `right_id` int unsigned NOT NULL DEFAULT '0',
  `left_id` int unsigned NOT NULL DEFAULT '0',
  `order` int unsigned NOT NULL DEFAULT '0',
  `name_ko` varchar(100) NOT NULL DEFAULT '',
  `name_en` varchar(100),
  `memo` varchar(255),
  `is_use` enum('Y','N') NOT NULL DEFAULT 'Y',
  `is_display` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY codes_parent_id_index (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

create index codes_parent_id_index on codes (parent_id);
--
-- export

/*
mysqldump -uroot -p -t --tab=/var/lib/mysql-files/ yescare codes --fields-enclosed-by=\" --fields-terminated-by=,
sudo mv /var/lib/mysql-files/codes.* /home/obpark/ && chown obpark:obpark /home/obpark/codes.*
*/

--
-- import
truncate table codes;
-- codes.csv 수정, replace , => |
-- import data
update codes set name_ko = replace(name_ko, '|', ',') ,memo = replace(memo, '|', ',') where 1;

-- http://localhost/rebuildCodes
select * from codes order by id desc limit 1;

insert into codes values
(1,0,0,0,0,'코드','code',null,'Y','Y',NOW(), NOW()),
(2,1,0,0,0,'학교','school',null,'Y','Y',NOW(), NOW()),
(3,2,0,0,0,'서울대',null,null,'Y','Y',NOW(),NOW()),
(4,2,0,0,0,'고려대',null,null,'Y','Y',NOW(),NOW()),
(5,2,0,0,0,'성균관대',null,null,'Y','Y',NOW(),NOW()),
(6,2,0,0,0,'전남대',null,null,'Y','Y',NOW(),NOW()),
(7,2,0,0,0,'전남대(여수)',null,null,'Y','Y',NOW(),NOW()),
(8,2,0,0,0,'전북대',null,null,'Y','Y',NOW(),NOW()),
(9,2,0,0,0,'연세대',null,null,'Y','Y',NOW(),NOW()),
(10,2,0,0,0,'경희대',null,null,'Y','Y',NOW(),NOW()),
(11,2,0,0,0,'경북대',null,null,'Y','Y',NOW(),NOW()),
(12,2,0,0,0,'부산대',null,null,'Y','Y',NOW(),NOW()),
(13,2,0,0,0,'중앙대',null,null,'Y','Y',NOW(),NOW()),
(14,2,0,0,0,'동국대',null,null,'Y','Y',NOW(),NOW()),
(15,2,0,0,0,'건국대',null,null,'Y','Y',NOW(),NOW()),
(16,2,0,0,0,'한양대',null,null,'Y','Y',NOW(),NOW()),
(17,2,0,0,0,'충남대',null,null,'Y','Y',NOW(),NOW()),
(18,2,0,0,0,'동아대',null,null,'Y','Y',NOW(),NOW()),
(19,2,0,0,0,'조선대',null,null,'Y','Y',NOW(),NOW()),
(20,2,0,0,0,'한국외대',null,null,'Y','Y',NOW(),NOW()),
(21,2,0,0,0,'인하대',null,null,'Y','Y',NOW(),NOW()),
(22,2,0,0,0,'영남대',null,null,'Y','Y',NOW(),NOW()),
(23,2,0,0,0,'경기대',null,null,'Y','Y',NOW(),NOW()),
(24,2,0,0,0,'충북대',null,null,'Y','Y',NOW(),NOW()),
(25,2,0,0,0,'단국대',null,null,'Y','Y',NOW(),NOW()),
(26,2,0,0,0,'경상대',null,null,'Y','Y',NOW(),NOW()),
(27,2,0,0,0,'강원대',null,null,'Y','Y',NOW(),NOW()),
(28,2,0,0,0,'원광대',null,null,'Y','Y',NOW(),NOW()),
(29,2,0,0,0,'국민대',null,null,'Y','Y',NOW(),NOW()),
(30,2,0,0,0,'명지대',null,null,'Y','Y',NOW(),NOW()),
(31,2,0,0,0,'서강대',null,null,'Y','Y',NOW(),NOW()),
(32,2,0,0,0,'인천대',null,null,'Y','Y',NOW(),NOW()),
(33,2,0,0,0,'홍익대',null,null,'Y','Y',NOW(),NOW()),
(34,2,0,0,0,'공주대',null,null,'Y','Y',NOW(),NOW()),
(35,2,0,0,0,'숭실대',null,null,'Y','Y',NOW(),NOW()),
(36,2,0,0,0,'청주대',null,null,'Y','Y',NOW(),NOW()),
(37,2,0,0,0,'계명대',null,null,'Y','Y',NOW(),NOW()),
(38,2,0,0,0,'아주대',null,null,'Y','Y',NOW(),NOW()),
(39,2,0,0,0,'울산대',null,null,'Y','Y',NOW(),NOW()),
(40,2,0,0,0,'경남대',null,null,'Y','Y',NOW(),NOW()),
(41,2,0,0,0,'광운대',null,null,'Y','Y',NOW(),NOW()),
(42,2,0,0,0,'서울시립대',null,null,'Y','Y',NOW(),NOW()),
(43,2,0,0,0,'전주대',null,null,'Y','Y',NOW(),NOW()),
(44,2,0,0,0,'대구대',null,null,'Y','Y',NOW(),NOW()),
(45,2,0,0,0,'한남대',null,null,'Y','Y',NOW(),NOW()),
(46,2,0,0,0,'관동대',null,null,'Y','Y',NOW(),NOW()),
(47,2,0,0,0,'동의대',null,null,'Y','Y',NOW(),NOW()),
(48,2,0,0,0,'경성대',null,null,'Y','Y',NOW(),NOW()),
(49,2,0,0,0,'교원대',null,null,'Y','Y',NOW(),NOW()),
(50,2,0,0,0,'가천대',null,null,'Y','Y',NOW(),NOW()),
(51,2,0,0,0,'금오공대',null,null,'Y','Y',NOW(),NOW()),
(52,2,0,0,0,'우석대',null,null,'Y','Y',NOW(),NOW()),
(53,2,0,0,0,'군산대',null,null,'Y','Y',NOW(),NOW()),
(54,2,0,0,0,'상지대',null,null,'Y','Y',NOW(),NOW()),
(55,2,0,0,0,'부경대',null,null,'Y','Y',NOW(),NOW()),
(56,2,0,0,0,'수원대',null,null,'Y','Y',NOW(),NOW()),
(57,2,0,0,0,'순천대',null,null,'Y','Y',NOW(),NOW()),
(58,2,0,0,0,'목포대',null,null,'Y','Y',NOW(),NOW()),
(59,2,0,0,0,'안동대',null,null,'Y','Y',NOW(),NOW()),
(60,2,0,0,0,'세종대',null,null,'Y','Y',NOW(),NOW()),
(61,2,0,0,0,'강릉대',null,null,'Y','Y',NOW(),NOW()),
(62,2,0,0,0,'창원대',null,null,'Y','Y',NOW(),NOW()),
(63,2,0,0,0,'호서대',null,null,'Y','Y',NOW(),NOW()),
(64,2,0,0,0,'순천향대',null,null,'Y','Y',NOW(),NOW()),
(65,2,0,0,0,'대전대',null,null,'Y','Y',NOW(),NOW()),
(66,2,0,0,0,'목원대',null,null,'Y','Y',NOW(),NOW()),
(67,2,0,0,0,'배재대',null,null,'Y','Y',NOW(),NOW()),
(68,2,0,0,0,'한림대',null,null,'Y','Y',NOW(),NOW()),
(69,2,0,0,0,'동신대',null,null,'Y','Y',NOW(),NOW()),
(70,2,0,0,0,'인제대',null,null,'Y','Y',NOW(),NOW()),
(71,2,0,0,0,'서울교대',null,null,'Y','Y',NOW(),NOW()),
(72,2,0,0,0,'경인교대',null,null,'Y','Y',NOW(),NOW()),
(73,2,0,0,0,'대구교대',null,null,'Y','Y',NOW(),NOW()),
(74,2,0,0,0,'부산교대',null,null,'Y','Y',NOW(),NOW()),
(75,2,0,0,0,'광주교대',null,null,'Y','Y',NOW(),NOW()),
(76,2,0,0,0,'춘천교대',null,null,'Y','Y',NOW(),NOW()),
(77,2,0,0,0,'진주교대',null,null,'Y','Y',NOW(),NOW()),
(78,2,0,0,0,'서남대',null,null,'Y','Y',NOW(),NOW()),
(79,2,0,0,0,'세명대',null,null,'Y','Y',NOW(),NOW()),
(80,2,0,0,0,'호남대',null,null,'Y','Y',NOW(),NOW()),
(81,2,0,0,0,'서원대',null,null,'Y','Y',NOW(),NOW()),
(82,2,0,0,0,'한성대',null,null,'Y','Y',NOW(),NOW()),
(83,2,0,0,0,'대구한의대',null,null,'Y','Y',NOW(),NOW()),
(84,2,0,0,0,'부산외대',null,null,'Y','Y',NOW(),NOW()),
(85,2,0,0,0,'건양대',null,null,'Y','Y',NOW(),NOW()),
(86,2,0,0,0,'서울과',null,null,'Y','Y',NOW(),NOW()),
(87,2,0,0,0,'상명대',null,null,'Y','Y',NOW(),NOW()),
(88,2,0,0,0,'용인대',null,null,'Y','Y',NOW(),NOW()),
(89,2,0,0,0,'강남대',null,null,'Y','Y',NOW(),NOW()),
(90,2,0,0,0,'서경대',null,null,'Y','Y',NOW(),NOW()),
(91,2,0,0,0,'가톨릭대',null,null,'Y','Y',NOW(),NOW()),
(92,2,0,0,0,'대진대',null,null,'Y','Y',NOW(),NOW()),
(93,2,0,0,0,'백석대',null,null,'Y','Y',NOW(),NOW()),
(94,2,0,0,0,'한밭대',null,null,'Y','Y',NOW(),NOW()),
(95,2,0,0,0,'선문대',null,null,'Y','Y',NOW(),NOW()),
(96,2,0,0,0,'대구가톨릭대',null,null,'Y','Y',NOW(),NOW()),
(97,2,0,0,0,'동양대',null,null,'Y','Y',NOW(),NOW()),
(98,2,0,0,0,'동명대',null,null,'Y','Y',NOW(),NOW()),
(99,2,0,0,0,'평택대',null,null,'Y','Y',NOW(),NOW()),
(100,2,0,0,0,'숙명여대',null,null,'Y','Y',NOW(),NOW()),
(101,2,0,0,0,'성신여대',null,null,'Y','Y',NOW(),NOW()),
(102,2,0,0,0,'경남과학기술대',null,null,'Y','Y',NOW(),NOW()),
(103,2,0,0,0,'경동대',null,null,'Y','Y',NOW(),NOW()),
(104,2,0,0,0,'광주대',null,null,'Y','Y',NOW(),NOW()),
(105,2,0,0,0,'남서울대',null,null,'Y','Y',NOW(),NOW()),
(106,2,0,0,0,'우송대',null,null,'Y','Y',NOW(),NOW()),
(107,2,0,0,0,'공군-한국교통대',null,null,'Y','Y',NOW(),NOW()),
(108,2,0,0,0,'공군-한국항공대',null,null,'Y','Y',NOW(),NOW()),
(109,2,0,0,0,'공군-한서대 ',null,null,'Y','Y',NOW(),NOW()),
(110,2,0,0,0,'해군-목포해양대',null,null,'Y','Y',NOW(),NOW()),
(111,2,0,0,0,'해군-부경대',null,null,'Y','Y',NOW(),NOW()),
(112,2,0,0,0,'해군-제주대',null,null,'Y','Y',NOW(),NOW()),
(113,2,0,0,0,'해군-한국해양대',null,null,'Y','Y',NOW(),NOW()),
(114,2,0,0,0,'해병-제주대',null,null,'Y','Y',NOW(),NOW())
;

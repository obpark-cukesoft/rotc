DROP TABLE IF EXISTS `boards`;
CREATE TABLE `boards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name_en` varchar(10) NOT NULL DEFAULT '' COMMENT '게시판 영문이름',
  `name_ko` varchar(50) NOT NULL DEFAULT '' COMMENT '게시판 한글이름',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '게시판 설명',
  `type` enum('L','G','Q') NOT NULL DEFAULT 'L' COMMENT '게시판유형(리스트형,갤러리형,답변형)',
  `per_page` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'post count for page',
  `skin` varchar(50) NOT NULL DEFAULT '' COMMENT '게시판 스킨',
  `layout` varchar(10) NOT NULL DEFAULT 'sub' COMMENT '게시판레이아웃(sub,noleft,blank)',
  `use_editor` enum('Y','N') DEFAULT 'N' COMMENT 'editor 사용',
  `use_notice_top` enum('Y','N') DEFAULT 'N' COMMENT '상단공지 사용',
  `use_comment` enum('Y','N') DEFAULT 'N' COMMENT '댓글 사용',
  `use_secret` enum('Y','N') DEFAULT 'N' COMMENT '비밀글 사용',
  `use_file` enum('Y','N') DEFAULT 'N' COMMENT '첨부파일 사용',
  `use_category` enum('Y','N') DEFAULT 'N' COMMENT '카테고리 사용',
  `file_count` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '첨부파일 개수',
  `file_size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '첨부파일 용량',
  `file_ext` varchar(255) DEFAULT '' COMMENT '첨부파일 확장자',
  `level_list` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `level_view` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `level_create` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `level_comment` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `level_upload` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `level_download` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `boards` VALUES (1,'notice','공지사항','공지사항 게시판','L',10,'basic','sub','N','N','Y','N','Y','N',5,2097152,'zip,doc,ppt,pptx,xls,xlsx,txt,pdf,hwp,bmp,gif,jpg,jpeg,png,tiff',0,0,0,0,0,0);

DROP TABLE IF EXISTS `board_categories`;
CREATE TABLE `board_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL COMMENT '게시판 아이디',
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `board_categories` VALUES (1,1,'공지'),(2,1,'질문');

--

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `board_id` int(10) unsigned NOT NULL COMMENT '게시판 아이디',
 `title` varchar(255) NOT NULL COMMENT '제목',
 `content` text NOT NULL COMMENT '내용',
 `notice` enum('Y','N') DEFAULT 'N' COMMENT '공지사항',
 `notice_top` enum('Y','N') DEFAULT 'N' COMMENT '상단 공지사항',
 `html` enum('Y','N') DEFAULT 'N' COMMENT 'html 포함글',
 `secret` enum('Y','N') DEFAULT 'N' COMMENT '비밀글',
 `count_read` int(10) unsigned DEFAULT '0' COMMENT '조회수',
 `count_comment` int(10) unsigned DEFAULT '0' COMMENT '코멘트수',
 `created_at` timestamp NULL DEFAULT NULL,
 `created_by` int(10) unsigned DEFAULT NULL,
 `updated_at` timestamp NULL DEFAULT NULL,
 `updated_by` int(10) unsigned DEFAULT NULL,
 `deleted_at` timestamp NULL DEFAULT NULL,
 `deleted_by` int(10) unsigned DEFAULT NULL,
 `category_id` int(10) unsigned DEFAULT NULL COMMENT '카테고리 아이디',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--

DROP TABLE IF EXISTS `post_comments`;
CREATE TABLE `post_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `content` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- INSERT INTO `post_comments` VALUES (8,8,'asdfasdf','2017-10-13 13:37:28',1,'2017-10-13 13:37:28',NULL,NULL,NULL);

DROP TABLE IF EXISTS `post_files`;
CREATE TABLE `post_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL,
  `origin_name` varchar(255) DEFAULT '' COMMENT '파일명',
  `saved_name` varchar(255) DEFAULT '' COMMENT '저장된 파일명',
  `path` varchar(255) DEFAULT '' COMMENT '파일경로',
  `size` int(10) unsigned DEFAULT NULL COMMENT '파일사이즈',
  `count_down` int(10) unsigned DEFAULT NULL COMMENT '다운로드수',
  `created_at` timestamp NULL DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

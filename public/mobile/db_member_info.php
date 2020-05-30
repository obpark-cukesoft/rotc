<?php
include_once './db_connector.php';
include_once './db_codes.php';
class DbMemberInfo extends DbConnector
{
	protected $rec_total;
	protected $rec_page;
	protected $rec_page_size;
    public function __construct()
    {
		$this->rec_total = 0;
		$this->rec_page  = 0;
		$this->rec_page_size  = 10;
    }

    function __destruct(){
        $this->free();
    }

    public function ReportLogin($rec_id, $user_name, $user_state, $user_grade, $reg_state, $mesg, $gps_usage)
	{
		//header("Content-type: application/json; charset=utf-8");
		$rst_array = array();
		$rst_array["RESULT"]   = "OK";
		$rst_array["MESG"]     = $mesg;
		$rst_array["rec_id"]   = $rec_id;
		$rst_array["user_name"]    = $user_name;
		$rst_array["user_state"]   = $user_state;
		$rst_array["user_grade"]   = $user_grade;
		$rst_array["reg_state"]    = $reg_state;
		$rst_array["gps_usage"]    = $gps_usage;
		$rst_array["list"]     = NULL;
		echo json_encode($rst_array, JSON_UNESCAPED_UNICODE);
	}

	public function ReportMessage($status, $mesg)
	{
		//header("Content-type: application/json; charset=utf-8");
		$rst_array = array();
		$rst_array["RESULT"]   = $status;
		$rst_array["MESG"]     = $mesg;
		$rst_array["list"]     = NULL;
		echo json_encode($rst_array, JSON_UNESCAPED_UNICODE);
	}

	public function GetCodeText($code, $code_id)
	{
	    //echo "GetCodeText::";
	    $cs = new DbCodes();
	    if($cs->connect()!=0){
	        return $cs->GetCodeText($code, $code_id);  
	    }
	    return "";
	}
	
	public function ReportResult()
	{
		$rst_array = array();
		$rst_array["RESULT"]   = "OK";
		$rst_array["MESG"]	   = '성공';
		$rst_array["page_no"]  = $this->rec_page_no;

		$rec_count = 0;
		if($this->result)
		{
			$list = array();
			while($row = mysqli_fetch_array($this->result))
			{
				$rec_count++;
				$row_array = array
				(
					"rec_id" => $row['id'],
					"user_id" =>  $row['email'],
					"user_pwd" =>  "",
					"user_name" =>  $row['name'],
					"user_mesg" =>  $row['note'],
					"user_state" =>  $row['status'],
					"user_grade" =>  $row['level'],
					"company" =>  $row['company'],
					"part" =>  $row['part'],
					"duty" =>  $row['duty'],

					"mob" =>  $row['mobile'],
					"mail" =>  $row['email'],
				    
				    "order_num" =>  $row['cardinal_numeral'],
				    "school_id" =>  $row['school_id'],
				    "school_name" => $this->GetCodeText("학교", $row['school_id']),
				    
					"url" =>  $row['url'],

					"push_id" =>  $row['push_id'],

					"photo" =>  $row['photo_path'],
					"ncard" =>  $row['business_card_path'],

					"gps_stamp" =>  $row['gps_updated_at'],
					"gps_lon" =>  $row['lon'],
					"gps_lat" =>  $row['lat'],
					"gps_addr" =>  $row['gps_address'],
					"update_stamp" =>  $row['updated_at']
				);
				array_push($list, $row_array);
			}

			$rst_array["list"] = $list;
		}

		if($rec_count<1)
		{
			$rst_array["RESULT"] = "FAIL";
			$rst_array["MESG"]  = "데이터가 없습니다.";
		}
		echo json_encode($rst_array, JSON_UNESCAPED_UNICODE);
	}

	public function IsMember($email)
	{
	    $sql = "SELECT * FROM users WHERE email = '".$email."'";

		//echo $sql;

		$this->Query($sql);
		if($this->result)
		{
			$row = mysqli_fetch_array($this->result);
			if($row)
			{
				//echo "IsMember::OK.";
				mysqli_free_result($result);
				return TRUE;
			}
		}
		//echo "IsMember::FAIL.";
		mysqli_free_result($result);
		return FALSE;
	}



	public function FindUser($email, $user_pwd)
	{
	    $sql = "SELECT * FROM users WHERE email = '".$email."' AND password='".$user_pwd."'";
		return $this->Query($sql);
	}

	/*
	 CREATE ALGORITHM=UNDEFINED DEFINER=`rotc`@`%` SQL SECURITY DEFINER VIEW `members` AS select `a`.`id` AS `id`,`a`.`name` AS `name`,`a`.`email` AS `email`,`a`.`email_verified_at` AS `email_verified_at`,`a`.`password` AS `password`,`a`.`remember_token` AS `remember_token`,`a`.`level` AS `level`,`a`.`status` AS `status`,`a`.`created_at` AS `created_at`,`a`.`updated_at` AS `updated_at`,`b`.`cardinal_numeral` AS `cardinal_numeral`,`b`.`school_id` AS `school_id`,`b`.`note` AS `note`,`b`.`company` AS `company`,`b`.`part` AS `part`,`b`.`duty` AS `duty`,`b`.`mobile` AS `mobile`,`b`.`url` AS `url`,`b`.`photo_id` AS `photo_id`,`b`.`photo_path` AS `photo_path`,`b`.`business_card_id` AS `business_card_id`,`b`.`business_card_path` AS `business_card_path`,`b`.`push_id` AS `push_id`,`b`.`gps` AS `gps`,`b`.`gps_address` AS `gps_address`,`b`.`gps_updated_at` AS `gps_updated_at`,`b`.`gps_usage` AS `gps_usage` from (`users` `a` left join `member_profiles` `b` on((`a`.`id` = `b`.`id`))) where (`a`.`level` = 10);
    SELECT `members`.`id`,
    `members`.`name`,
    `members`.`email`,
    `members`.`email_verified_at`,
    `members`.`password`,
    `members`.`remember_token`,
    `members`.`level`,
    `members`.`status`,
    `members`.`created_at`,
    `members`.`updated_at`,
    `members`.`cardinal_numeral`,
    `members`.`school_id`,
    `members`.`note`,
    `members`.`company`,
    `members`.`part`,
    `members`.`duty`,
    `members`.`mobile`,
    `members`.`url`,
    `members`.`photo_id`,
    `members`.`photo_path`,
    `members`.`business_card_id`,
    `members`.`business_card_path`,
    `members`.`push_id`,
    `members`.`gps`,
    `members`.`gps_address`,
    `members`.`gps_updated_at`,
    `members`.`gps_usage`
FROM `rotc`.`members`;
	 */
	
	public function Login($email, $user_pwd)
	{
	    if(!$this->IsMember($email))
	        return $this->ReportMessage("FAIL", "등록된 회원이 아닙니다.");
	        
	        $sql = "SELECT * FROM members WHERE email = '".$email."'";
	        $this->Query($sql);
	        if($this->result)
	        {
	            $member = mysqli_fetch_array($this->result);
	            mysqli_free_result($this->result);
	            if (password_verify($user_pwd, $member['password']))
	            {
	                //if(strcmp($member['status'], "N")==0)
	                //    return $this->ReportLogin($user['id'], $user['name'], $user['status'], $user['level'], $user['status'], "");//$profile);
	                //if(strcmp($member['status'], "R")==0)
	                //    return $this->ReportMessage("FAIL", "관리자 승인이 필요합니다.");
	                if(strcmp($member['status'], "S")==0)
	                        return $this->ReportMessage("FAIL", "사용이 중지된 계정입니다.");
	                        
	                        return $this->ReportLogin($member['id'], $member['name'], $member['status'], $member['level'], $member['status'], "", $member[gps_usage]);//$profile);
	                        
	            }
	            return $this->ReportMessage("FAIL", "비밀번호가 일치하지 않습니다.");
	        }
	        return $this->ReportMessage("FAIL", "계정 정보가 불완전합니다.");
	}
	
	/*
	public function Login($email, $user_pwd)
	{
	    if(!$this->IsMember($email))
	        return $this->ReportMessage("FAIL", "등록된 회원이 아닙니다.");

	    $sql = "SELECT * FROM users WHERE email = '".$email."'";//."' AND password='".$user_pwd."'";
	    $this->Query($sql);
	    if($this->result)
	    {
	       $user = mysqli_fetch_array($this->result);
	       mysqli_free_result($this->result);
	       if (password_verify($user_pwd, $user['password']))
	       {
	           //if(strcmp($user['status'], "N")==0)
	           //    return $this->ReportLogin($user['id'], $user['name'], $user['status'], $user['level'], $user['status'], "");//$profile);
	           //if(strcmp($user['status'], "R")==0)
	           //    return $this->ReportMessage("FAIL", "관리자 승인이 필요합니다.");
               if(strcmp($user['status'], "S")==0)
                   return $this->ReportMessage("FAIL", "사용이 중지된 계정입니다.");
               
               return $this->ReportLogin($user['id'], $user['name'], $user['status'], $user['level'], $user['status'], "", "");//$profile);
	                   
	       }
	       return $this->ReportMessage("FAIL", "비밀번호가 일치하지 않습니다.");
	    }
	    return $this->ReportMessage("FAIL", "계정 정보가 불완전합니다.");
	}
    */
	public function SetInfo($page_size)
	{
		$this->rec_total = 0;
		$this->rec_page  = 0;
		$this->rec_page_size = $page_size;
		$sql = "SELECT COUNT(*) FROM tbl_member_base";
		$this->Query($sql);
		if($this->result)
		{
			$row = mysqli_fetch_array($this->result);
			if($row)
			{
				$this->rec_total = (int)$row[0];
				if($page_size>0)
				{
					$this->rec_page = (int)($this->rec_total/$this->rec_page_size);
					if(($this->rec_total%$this->rec_page_size) != 0)
						$this->rec_page++;
				}
			}
		}
	}


	public function QueryOwner($rec_id)
	{
		$this->rec_page_no  = 0;
		$sql = "SELECT *, X(gps) AS lon, Y(gps) AS lat FROM members WHERE id=".$rec_id;
		$this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function QueryInfo($rec_id)
	{
		$this->rec_page_no  = 0;
		$sql = "SELECT *, X(gps) AS lon, Y(gps) AS lat FROM members WHERE id=".$rec_id;

		//echo $sql;

		$this->result = $this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function Distance($dist, $lon, $lat)
	{
	    $this->rec_page_no  = 0;
	    /*
	    $sql = "SELECT *, X(gps) AS lon, Y(gps) AS lat
                FROM members
	            WHERE email <> 'admin@admin.com' AND st_distance_sphere(point("
	        .$lon.", ".$lat."), gps) < ".$dist;
	    */    
	  
	    $sql = "SELECT *, X(gps) AS lon, Y(gps) AS lat 
                FROM members  
	            WHERE gps_usage<>0 AND st_distance_sphere(point("
	            .$lon.", ".$lat."), gps) < ".$dist;
	  
        
	    //echo $sql;
	    
	    $this->Query($sql);
	    if(!$this->result){
	        return 0;
	    }
	        
	    return 1;
	}
	
	public function List($keyword)
	{
		$this->rec_page_no  = 0;
		$sql = "SELECT *, X(gps) AS lon, Y(gps) AS lat FROM members where email <> 'admin' ";

		if($keyword != NULL && strlen($keyword)>1)
		{
			$sql = "SELECT *, X(gps) AS lon, Y(gps) AS lat FROM members WHERE email <> 'admin' AND name LIKE '%".$keyword."' OR name LIKE '".$keyword."%'";
		}

		$this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

	public function Search($find_key, $find_field, $list_order)
	{
		$this->rec_page_no  = 0;
		$where = "WHERE email <> 'admin'";
		$order = "";
		if(strlen($find_key)>0)
		{
			if(strlen($find_field)>0){
				$where = "WHERE email <> 'admin' AND (".$find_field." LIKE '%".$find_key."' OR ".$find_field." LIKE '".$find_key."%')";
			}
			else
			{
				$where = "WHERE email <> 'admin' AND (name LIKE '%".$find_key."' OR name LIKE '".$find_key."%') OR ".
						 "(duty LIKE '%".$find_key."' OR duty LIKE '".$find_key."%') OR ".
						 "(company LIKE '%".$find_key."' OR company LIKE '".$find_key."%')";
			}
		}

		if(strlen($list_order)>0){
			$order = " ".$list_order;
		}

		$sql = "SELECT *, X(gps) AS lon, Y(gps) AS lat FROM members ".$where.$order;

		//echo $sql;

		$this->Query($sql);
		if(!$this->result)
			return 0;
		return 1;
	}

/***
CREATE TABLE `codes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `right_id` int(10) unsigned NOT NULL DEFAULT '0',
  `left_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order` int(10) unsigned NOT NULL DEFAULT '0',
  `name_ko` varchar(100) NOT NULL DEFAULT '',
  `name_en` varchar(100) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `is_use` enum('Y','N') NOT NULL DEFAULT 'Y',
  `is_display` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `codes_parent_id_index` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8;


CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `level` tinyint(3) unsigned DEFAULT NULL COMMENT '로그인 level 관리자:1, 회원: 10',
  `status` char(1) DEFAULT 'N' COMMENT '계정 상태(N:정상, R:등록, S:중지)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='로그인 정보';


CREATE TABLE `member_profiles` (
  `id` bigint(20) unsigned NOT NULL,
  `cardinal_numeral` tinyint(3) unsigned DEFAULT NULL COMMENT 'ROTC 기수: 1 ~ ',
  `school_id` bigint(20) unsigned DEFAULT NULL COMMENT '출신학교코드',
  `note` varchar(255) DEFAULT NULL COMMENT '상태메모',
  `company` varchar(255) DEFAULT NULL COMMENT '상호',
  `part` varchar(255) DEFAULT NULL COMMENT '부서',
  `duty` varchar(255) DEFAULT NULL COMMENT '직급',
  `mobile` varchar(255) DEFAULT NULL COMMENT '휴대폰',
  `url` varchar(255) DEFAULT NULL COMMENT 'url',
  `photo_path` varchar(255) DEFAULT NULL COMMENT '사진 path',
  `business_card_path` varchar(255) DEFAULT NULL COMMENT '명함 path',
  `push_id` varchar(255) DEFAULT NULL COMMENT 'device id',
  `gps` point DEFAULT NULL COMMENT 'GPS 정보',
  `gps_address` varchar(255) DEFAULT NULL COMMENT 'GPS 정보의 주소',
  `gps_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'GPS 갱신 시각',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='회원 정보';
****/

	public function UpdateUsers($rec_id,  $fields, $values)
	{
		if(count($fields)==count($values))
		{
			$div = "";
			$sql = "UPDATE users SET ";
			for($x = 0; $x < count($fields); $x++)
			{
			    if($fields[$x]=="password"){
			        $set = $fields[$x]." = '".password_hash($values[$x], PASSWORD_BCRYPT)."'";
			    }
				if( $fields[$x]=="name" || $fields[$x]=="note" || 
				    $fields[$x]=="company" || $fields[$x]=="part" || $fields[$x]=="duty" || 
				    $fields[$x]=="mobile" || $fields[$x]=="url" || $fields[$x]=="push_id" ||
					$fields[$x]=="photo_path" || $fields[$x]=="business_card_path" )
				{
					$set = $fields[$x]." = '".$values[$x]."'";
				}
				else{
					$set = $fields[$x]." = ".$values[$x];
				}

				if($x>0) $div=", ";
				$sql = $sql.$div.$set;
			}

			$sql = $sql." WHERE id=".$rec_id;

			echo $sql;

			if($this->Execute($sql))
					return TRUE;
		}
		return FALSE;
	}

	public function UpdateRecord($rec_id,  $fields, $values)
	{
	    if(count($fields)==count($values))
	    {
	        $div = "";
	        $sql = "UPDATE member_profiles SET ";
	        for($x = 0; $x < count($fields); $x++)
	        {
	            if($fields[$x]=="password"){
	                $set = $fields[$x]." = '".password_hash($values[$x], PASSWORD_BCRYPT)."'";
	            }
	            if( $fields[$x]=="name" || $fields[$x]=="note" ||
	                $fields[$x]=="company" || $fields[$x]=="part" || $fields[$x]=="duty" ||
	                $fields[$x]=="mobile" || $fields[$x]=="url" || $fields[$x]=="push_id" ||
	                $fields[$x]=="photo_path" || $fields[$x]=="business_card_path" )
	            {
	                $set = $fields[$x]." = '".$values[$x]."'";
	            }
	            else{
	                $set = $fields[$x]." = ".$values[$x];
	            }
	            
	            if($x>0) $div=", ";
	            $sql = $sql.$div.$set;
	        }
	        
	        $sql = $sql." WHERE id=".$rec_id;
	        
	        echo $sql;
	        
	        if($this->Execute($sql))
	            return TRUE;
	    }
	    return FALSE;
	}
	
	public function UpdatePassword($uid,  $password)
	{
	    $enc_password = password_hash($password, PASSWORD_BCRYPT);
	    $sql = "UPDATE users SET password = '".$enc_password."' WHERE id=".$uid;

	    //echo $sql;
	    
	    if($this->Execute($sql)){
	        return TRUE;
	    }
	    
	    return FALSE;
	}
	
	
	
	
	public function UpdateRecordGpsFlag($uid,  $flag)
	{
	    $sql = "UPDATE member_profiles SET gps_usage = ".$flag." WHERE id=".$uid;

	    
	    //echo $sql;
	    
	    if($this->Execute($sql)){
	        return TRUE;
	    }
	    return FALSE;
	}
	
	public function UpdateRecordGps($uid,  $lon, $lat, $addr)
	{
	    $sql = "UPDATE member_profiles SET gps = GeomFromText('POINT(".$lon." ".$lat.")')";
		$sql = $sql.", gps_address = '".$addr."'";
		$sql = $sql.", gps_updated_at = now()";
		$sql = $sql." WHERE id=".$uid;

		//echo $sql;

		if($this->Execute($sql)){
			return TRUE;
		}
		return FALSE;
	}

	/*
    CREATE TABLE `users` (
      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `email_verified_at` timestamp NULL DEFAULT NULL,
      `password` varchar(255) NOT NULL,
      `remember_token` varchar(100) DEFAULT NULL,
      `level` tinyint(3) unsigned DEFAULT NULL COMMENT '로그인 level 관리자:1, 회원: 10',
      `status` char(1) DEFAULT 'N' COMMENT '계정 상태(N:정상, R:등록, S:중지)',
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `users_email_unique` (`email`)
    ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='로그인 정보';

	 */
	public function Regist($email, $name, $password)
	{
	    $enc_password = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users(email, name, password, level, status, email_verified_at, created_at, updated_at) 
                VALUES('".$email."', '".$name."', '".$enc_password."', '10', 'R'".
                       ", now(), now(), now())";
        
        //echo $sql;
        
        if($this->Execute($sql))
        {
            $sql = "SELECT id FROM users WHERE email='".$email."'";
            $this->Query($sql);
            if($this->result)
            {
                $row = mysqli_fetch_array($this->result);
                if($row)
                {
                    return (int)$row[0];
                }
            }
        }
	    return -1;
	}
	
	public function AddRecord($fields, $values)
	{
		if(count($fields)==count($values))
		{
			$fset = "";
			$vset = "";
			$div  = "";
			for($x = 0; $x < count($fields); $x++)
			{
				if($x>0)
					$div  = ", ";
				$fset = $fset.$div.$fields[$x];
				$vset = $vset.$div."'".$values[$x]."'";
				/*
				if( $fields[$x]=="cardinal_numeral" || $fields[$x]=="password" || $fields[$x]=="name" ||
					$fields[$x]=="note" || $fields[$x]=="grade" || $fields[$x]=="state" ||
					$fields[$x]=="mobile" || $fields[$x]=="tel" || $fields[$x]=="url" ||
					$fields[$x]=="company" || $fields[$x]=="part" || $fields[$x]=="duty" ||
					$fields[$x]=="photo" || $fields[$x]=="ncard" || $fields[$x]=="push_id" || $fields[$x]=="gps_addr"
				)
					$vset = $vset.$div."'".$values[$x]."'";
				else
					$vset = $vset.$div.$values[$x];
				*/	
			}

			$sql = "INSERT INTO member_profiles(".$fset.") VALUES(".$vset.")";

			//echo $sql;

			if($this->Execute($sql))
				return TRUE;
		}
		return FALSE;
	}

	public function GetUserId($rec_id)
	{
	    $user_id = "";
	    $sql = "SELECT user_id FROM users WHERE id=".$rec_id;
	    $this->result = $this->Query($sql);
	    if($this->result)
	    {
	        $row = mysqli_fetch_array($this->result);
	        if($row)
	        {
	            $user_id = (int)$row['email'];
	        }
	    }
	    return $user_id;
	}

	public function DeleteOtherInfo($rec_id,  $rec_uid)
	{
	    $sql = "delete FROM tbl_sales WHERE rec_id=".$rec_id;
	    if(!$this->Execute($sql))
	        return FALSE;

	    $sql = "delete FROM tbl_member_place WHERE mem_id".$rec_id;
	    if(!$this->Execute($sql))
	        return FALSE;

	    $sql = "delete FROM tbl_attend WHERE mem_uid='".$rec_uid."'";
	    if(!$this->Execute($sql))
	        return FALSE;
	    return TRUE;
	}

	public function DeleteRecord($key,  $value, $isDigit)
	{
		$sql = "";
		if($key && $value)
		{
			if($isDigit)
				$sql = "DELETE FROM users WHERE ".$key."=".$value;
			else
				$sql = "DELETE FROM users WHERE ".$key."='".$value."'";
			if($this->Execute($sql))
				return TRUE;

		}
		return FALSE;
	}

	public function DeleteImage($target_dir, $key,  $value, $isDigit)
	{
		$next_rec_id = 1;
		if($isDigit)
			$condition = " ".$key."=".$value;
		else
			$condition = " ".$key."='".$value."'";

		$sql = "SELECT id, photo, ncard FROM users WHERE ".$condition;

		$this->result = $this->Query($sql);
		if($this->result)
		{
			while($row = mysqli_fetch_array($this->result))
			{
				$rec_id = (int)$row['id'];
				$path = $target_dir.$row['photo'];
				if(file_exists($path) && is_file($path))
					unlink($path);

				$path = $target_dir.$row['ncard'];
				if(file_exists($path) && is_file($path))
					unlink($path);

				$sql = "UPDATE users SET photo='', ncard='' WHERE id=".$rec_id;
				$this->Execute($sql);
			}
		}
		return false;
	}

	public function SaveImage($target_dir, $srcInfo, $rec_num, $isOrg)
	{

		if(getimagesize($srcInfo))
		{
			if($isOrg)
				$target_file = "ms".time().$rec_num.".jpg";
			else
				$target_file = "mt".time().$rec_num.".jpg";
			$target_path = $target_dir.$target_file;

			echo 'SORC=>'.$srcInfo;
			echo '<br>';
			echo 'DEST=>'.$target_path;

			if (move_uploaded_file($srcInfo, $target_path))
			{
				return $target_file;
			}
		}
		return null;
	}

	public function AdmobPos($user_id)
	{
		$admob_next = -1;
		$sql = "SELECT admob_pos FROM tbl_member_base WHERE user_id='".$user_id."'";
		$this->result = $this->Query($sql);
		if($this->result)
		{
			$admob_pos  = 0;
			while($row = mysqli_fetch_array($this->result))
			{
				if($row['admob_pos'])
					$admob_pos = (int)$row['admob_pos'];
				$admob_next = $admob_pos+1;
				$sql = "UPDATE tbl_member_base SET admob_pos=".$admob_next." WHERE user_id='".$user_id."'";
				$this->Execute($sql);

				//echo $sql;

				break;
			}
		}
		return $admob_next;
	}

}

?>

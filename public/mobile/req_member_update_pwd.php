<?php

// http://114.108.177.130/mob/req_member_update_pwd.php?rec_id=4

include_once './db_member_info.php';

$conMember = new DbMemberInfo();
if($conMember->connect()==0){
	return $conMember->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

if(!isset($_REQUEST["rec_id"]) || !($_REQUEST["user_pwd"]))
	return $conMember->ReportMessage("FAIL", "비밀번호 변경 오류!.");

$rec_id = (int)$_REQUEST["rec_id"];
if(!$conMember->UpdatePassword($rec_id, trim($_REQUEST["user_pwd"])))
{
	return $conMember->ReportMessage("FAIL", "데이터 변경중 오류가 발생하였습니다.");
}

$conMember->ReportMessage("OK", "비밀번호가 변경되었습니다.");

?>
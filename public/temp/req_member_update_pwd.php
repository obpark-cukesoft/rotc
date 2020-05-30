<?php

// http://114.108.177.130/mob/req_member_update_pwd.php?rec_id=4

include_once './db_member_info.php';

$conMember = new DbMemberInfo();
if($conMember->connect()==0){
	return $conMember->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

if(!isset($_REQUEST["rec_id"]))
	return $conMember->ReportMessage("FAIL", "삭제할 레코드가 없습니다.");

$count = 0;
$fields = array();
$values = array();

$count = 0;
if(isset($_REQUEST["user_pwd"])){
	array_push($fields, "password");
	array_push($values, trim($_REQUEST["user_pwd"]));
	$count++;
}

if($count<1)
{
	return $conMember->ReportMessage("FAIL", "부적절한 데이터입니다.");
}

$rec_id = (int)$_REQUEST["rec_id"];
if(!$conMember->UpdateRecord($rec_id, $fields, $values))
{
	return $conMember->ReportMessage("FAIL", "데이터 변경중 오류가 발생하였습니다.");
}

$conMember->ReportMessage("OK", "비밀번호가 변경되었습니다.");

?>
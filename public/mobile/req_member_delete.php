<?php

// http://114.108.177.130/mob/req_member_delete.php?rec_id=4

include_once './db_member_info.php';
include_once './db_company_info.php';
include_once './db_sales_info.php';

$conMember = new DbMemberInfo();
if($conMember->connect()==0){
	return $conMember->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

if(!isset($_REQUEST["rec_id"]))
	return $conMember->ReportMessage("FAIL", "삭제할 레코드가 없습니다.");

$rec_id=(int)trim($_REQUEST["rec_id"]);
$target_dir = $_SERVER['DOCUMENT_ROOT']."/images/";


$conMember->DeleteImage($target_dir, "rec_id", $rec_id, TRUE);
if(!$conMember->DeleteRecord("rec_id", $rec_id, TRUE))
{
	return $conMember->ReportMessage("FAIL", "작업중 오류가 발생하였습니다.");
}

$rec_uid = $conMember->GetUserId($rec_id);
$conMember->DeleteOtherInfo($rec_id, $rec_uid);
$conMember->ReportMessage("OK", "데이터가 삭제되었습니다.");


?>
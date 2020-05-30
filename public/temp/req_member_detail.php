<?php
// 사용자 정보
include_once './db_member_info.php';
$conn = new DbMemberInfo();

if(!isset($_REQUEST["rec_id"]))
{
	return $conn->ReportMessage("FAIL", "등록된 사용자가 아닙니다.");
}

$rec_id = (int)trim($_REQUEST["rec_id"]);

if($conn->connect()==0){
	return $conn->ReportMessage("FAIL","데이터베이스에 연결할 수 없습니다.");
}

//$conn->SetInfo(10);
if(!$conn->QueryOwner($rec_id)){
	return $conn->ReportMessage("FAIL","데이터베이스에 연결할 수 없습니다.");
}

$conn->ReportResult();
?>
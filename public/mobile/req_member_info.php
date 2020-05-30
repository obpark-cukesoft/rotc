<?php
// 회원정보 조회.
// key->rec_id
include_once './db_member_info.php';

$conn = new DbMemberInfo();
if(!$conn->connect())
{
	return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

if(!isset($_REQUEST["rec_id"]))
{
	return $conn->ReportMessage("FAIL", "검색 오류입니다.");
}

$rec_id = (int)trim($_REQUEST["rec_id"]);
if(!$conn->QueryInfo($rec_id)){
	return $conn->ReportMessage("FAIL", "리스트 검색 오류.");
}
	
return $conn->ReportResult();

?>
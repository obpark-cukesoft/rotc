<?php
// 사업장 정보 조회
// http://114.108.177.130/mob/req_sales_info.php?rec_id=1
include_once './db_sales_info.php';

$conn = new DbSalesInfo();
if(!$conn->connect())
	return $conn->ReportMessage("FAIL","데이터베이스에 연결할 수 없습니다.");

if(!isset($_REQUEST["rec_id"]))
{
	return $conn->ReportMessage("FAIL", "검색 오류입니다.");
}

$rec_id = (int)trim($_REQUEST["rec_id"]);
if(!$conn->QueryInfo($rec_id))
	return $conn->ReportMessage("FAIL", "리스트 검색 오류.");
return $conn->ReportResult();	

?>
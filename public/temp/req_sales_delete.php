<?php
include_once './db_sales_info.php';

$conn = new DbSalesInfo();

if($conn->connect()==0){
	return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}


if(!isset($_REQUEST["rec_id"]))
	return $conn->ReportMessage("FAIL", "삭제할 레코드가 없습니다.");

$rec_id=(int)trim($_REQUEST["rec_id"]);
if(isset($_REQUEST["photo"]))
{
	$conn->DeleteImage($conn->target_dir, $rec_id);
	return $conn->ReportMessage("OK", "사진이 삭제되었습니다.");
}

$conn->DeleteImage($conn->target_dir, $rec_id);
if(!$conn->DeleteRecord("rec_id", $rec_id, TRUE))
{
	return $conn->ReportMessage("FAIL", "작업중 오류가 발생하였습니다.");
}

$conn->ReportMessage("OK", "데이터가 삭제되었습니다.");


?>
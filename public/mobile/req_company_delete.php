<?php
include_once './db_company_info.php';


$conn = new DbCompanyInfo();
if($conn->connect()==0){
	return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

if(isset($_REQUEST["rec_id"]))
{
	$rec_id=(int)trim($_REQUEST["rec_id"]);
	$conn->DeleteImage($conn->target_dir, "rec_id", $rec_id, true);
	if(!$conn->DeleteRecord("rec_id", $rec_id, TRUE))
	{
		return $conn->ReportMessage("FAIL", "작업중 오류가 발생하였습니다.");
	}
	return $conn->ReportMessage("OK", "데이터가 삭제되었습니다.");
}

if(isset($_REQUEST["mem_id"]))
{
	$mem_id=(int)trim($_REQUEST["mem_id"]);
	if(!$conn->DeleteRecord("mem_id", $mem_id, TRUE))
	{
		return $conn->ReportMessage("FAIL", "작업중 오류가 발생하였습니다.");
	}

	return $conn->ReportMessage("OK", "데이터가 삭제되었습니다.");
}

$conn->ReportMessage("FAIL", "삭제할 레코드가 없습니다.");

?>
<?php
include_once './db_admob_info.php';

$conn = new DbAdmobInfo();
if($conn->connect()==0){
	return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

if(!isset($_REQUEST["rec_id"]))
{
    return $conn->ReportMessage("FAIL", "부적절한 요청입니다.");
}

$rec_id=(int)trim($_REQUEST["rec_id"]);
$conn->DeleteImage($conn->admob_dir, $rec_id);
if(!$conn->DeleteRecord("rec_id", $rec_id, TRUE))
{
	return $conn->ReportMessage("FAIL", "작업중 오류가 발생하였습니다.");
}
return $conn->ReportMessage("OK", "데이터가 삭제되었습니다.");

?>
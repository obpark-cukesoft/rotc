<?php
// 사업장 정보 조회
// http://local/req_company_list.php?mem_id=1
// http://local/req_company_list.php?company=1
include_once './db_company_info.php';

$conn = new DbCompanyInfo();
if(!$conn->connect())
	return $conn->ReportMessage("FAIL","데이터베이스에 연결할 수 없습니다.");

if(isset($_REQUEST["mem_id"]))
{
	$mem_id = (int)trim($_REQUEST["mem_id"]);
	if(!$conn->QueryOwner($mem_id))
		return $conn->ReportMessage("FAIL", "리스트 검색 오류.");
	return $conn->ReportResult();	
}

$count = 0;
$find_key   = "";
$find_field = "";
$list_order = "";

if(isset($_REQUEST["find_key"])){
	$find_key = trim($_REQUEST["find_key"]);
	$count++;
}

if(isset($_REQUEST["find_field"])){
	$find_field = trim($_REQUEST["find_field"]);
	$count++;
}

if(isset($_REQUEST["list_order"])){
	$list_order = trim($_REQUEST["list_order"]);
	$count++;
}

/***
if(isset($_REQUEST["company"]))
{
	$company = trim($_REQUEST["company"]);
	if(strlen($company)>1)
	{
		if(!$conn->SearchList($company))
			return $conn->ReportMessage("FAIL", "상호 검색 오류.");
		return $conn->ReportResult();
	}
}
****/

if($count<1)
{
	if(!$conn->QueryAll())
		return $conn->ReportMessage("FAIL", "리스트 검색 오류.");
	return $conn->ReportResult();
}

if(!$conn->Search($find_key, $find_field, $list_order))
{
	return $conn->ReportMessage("FAIL", "검색 오류가 발생하였습니다.");
}

return $conn->ReportResult();

?>
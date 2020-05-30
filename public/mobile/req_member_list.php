<?php
// 회원정보 조회.
// key->rec_id
include_once './db_member_info.php';

/***
  values.put("user_id", user_id);
  values.put("find_key",   m_find_key);
  values.put("find_field", m_find_field);
  values.put("list_order", m_list_order);
  values.put("idx_start", 0);
  values.put("idx_end", 0);
****/
$conn = new DbMemberInfo();
if(!$conn->connect())
{
	return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

$count = 0;
$find_key   = "";
$find_field = "";
$list_order = "";

if(isset($_REQUEST["distance"]) && isset($_REQUEST["gps_lon"]) && isset($_REQUEST["gps_lat"])){
    if($conn->Distance($_REQUEST["distance"], $_REQUEST["gps_lon"], $_REQUEST["gps_lat"])){
        $conn->ReportResult();
    }
    return $conn->ReportMessage("FAIL", "검색 오류가 발생하였습니다.");
}

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

//$conn->SetInfo(20);
if($count<1)
{
	if(!$conn->List(""))
	{
		return $conn->ReportMessage("FAIL", "검색 오류가 발생하였습니다.");
	}
}
else
{
	if(!$conn->Search($find_key, $find_field, $list_order))
	{
		return $conn->ReportMessage("FAIL", "검색 오류가 발생하였습니다.");
	}
}

$conn->ReportResult();
?>

<?php
// 사용자 휴대폰의 위치 정보를 갱신한다.
// rec_id, gps_lon, gps_ant, gps_addr

//https://market.maxidc.net/rotcNote/mob/req_member_update_gps.php?user_id=1&gps_lon=-122.084&gps_lat=37.421997&gps_addr=aaaa

include_once './db_member_info.php';

$conn = new DbMemberInfo();
if($conn->connect()==0){
	return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

$count = 0;
if(!isset($_REQUEST["user_id"]))
{
	return $conn->ReportMessage("FAIL", "등록된 사용자가 아닙니다.");
}

$user_id = ($_REQUEST["user_id"]);
if(isset($_REQUEST["gps_usage"]))
{
    if(!$conn->UpdateRecordGpsFlag($user_id, $_REQUEST["gps_usage"]))
    {
        return $conn->ReportMessage("FAIL", "부적절한 데이터입니다.");
    }
    return $conn->ReportMessage("OK", "데이터가 수정되었습니다.");
}

if(isset($_REQUEST["gps_lon"]) && isset($_REQUEST["gps_lat"]) && $_REQUEST["gps_addr"])
{
    $lon = (float)trim($_REQUEST["gps_lon"]);
    $lat = (float)trim($_REQUEST["gps_lat"]);
	$addr = trim($_REQUEST["gps_addr"]);
    if(!$conn->UpdateRecordGps($user_id, $lon, $lat, $addr))
        return $conn->ReportMessage("FAIL", "데이터 변경중 오류가 발생하였습니다.");

    return $conn->ReportMessage("OK", "데이터가 수정되었습니다.");
}

return $conn->ReportMessage("FAIL", "부적절한 데이터입니다.");


?>
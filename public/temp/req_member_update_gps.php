
<?php
// 사용자 휴대폰의 위치 정보를 갱신한다.
// rec_id, gps_lon, gps_ant, gps_addr
// http://114.108.177.130/mob/req_member_update_gps.php?user_id=dhjang@gmail.com&gps_lon=1.0&gps_lat=-1.0&gps_addr=%EA%B4%80%ED%8F%89%EB%8F%99
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

$fields = array();
$values = array();


if(isset($_REQUEST["gps_lon"]) && isset($_REQUEST["gps_lat"]))
{
    $lat = (float)trim($_REQUEST["gps_lat"]);
    $lon = (float)trim($_REQUEST["gps_lon"]);
	array_push($fields, "gps");
	array_push($values, GeomFromText('POINT('.$lon.' '.$lat.')'));
	if(isset($_REQUEST["gps_addr"]))
	{
	    array_push($fields, "gps_address");
	    array_push($values, trim($_REQUEST["gps_addr"]));
	    
	    if($conn->UpdateRecordGps($user_id, $fields, $values))
	    {
	        $conn->ReportMessage("OK", "데이터가 수정되었습니다.");
	    }
	}
	return $conn->ReportMessage("FAIL", "데이터 변경중 오류가 발생하였습니다.");
}


return $conn->ReportMessage("FAIL", "부적절한 데이터입니다.");


?>
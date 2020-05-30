
<?php
// 사용자 등록정보 수정.
// key->rec_id
// http://114.108.177.130/mob/req_member_update.php?rec_id=1&user_name=kkk
include_once './db_member_info.php';

$conn = new DbMemberInfo();
if($conn->connect()==0){
	return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

if(!isset($_REQUEST["rec_id"]))
{
	return $conn->ReportMessage("FAIL", "등록된 사용자가 아닙니다.");
}

$rec_id = (int)trim($_REQUEST["rec_id"]);
//$rec_id = (int)$rec_num;

$update_count=0;
if(isset($_REQUEST["user_name"]))
{
    $users_fields = array();
    $users_values = array();
    array_push($users_fields, "name");
    array_push($users_values, trim($_REQUEST["user_name"]));
    if(!$conn->UpdateUsers($rec_id, $users_fields, $users_values)){
        return $conn->ReportMessage("FAIL", "데이터 변경중 오류가 발생하였습니다.");
    }
    $update_count = 1;
}

$fields = array();
$values = array();

$count = 0;

if(isset($_REQUEST["order_num"])){
    array_push($fields, "cardinal_numeral");
    array_push($values, trim($_REQUEST["order_num"]));
    $count++;
}

if(isset($_REQUEST["school_id"])){
    array_push($fields, "school_id");
    array_push($values, trim($_REQUEST["school_id"]));
    $count++;
}

if(isset($_REQUEST["user_grade"])){
    array_push($fields, "level");
    array_push($values, trim($_REQUEST["user_grade"]));
    $count++;
}

if(isset($_REQUEST["user_pwd"])){
	array_push($fields, "password");
	array_push($values, trim($_REQUEST["user_pwd"]));
	$count++;
}

if(isset($_REQUEST["company"])){
	array_push($fields, "company");
	array_push($values, trim($_REQUEST["company"]));
	$count++;
}

if(isset($_REQUEST["part"])){
	array_push($fields, "part");
	array_push($values, trim($_REQUEST["part"]));
	$count++;
}

if(isset($_REQUEST["duty"])){
	array_push($fields, "duty");
	array_push($values, trim($_REQUEST["duty"]));
	$count++;
}

if(isset($_REQUEST["mob"])){
	array_push($fields, "mobile");
	array_push($values, trim($_REQUEST["mob"]));
	$count++;
}

if(isset($_REQUEST["tel"])){
	array_push($fields, "tel");
	array_push($values, trim($_REQUEST["tel"]));
	$count++;
}

if(isset($_REQUEST["url"])){
	array_push($fields, "url");
	array_push($values, trim($_REQUEST["url"]));
	$count++;
}

if(isset($_REQUEST["push_id"])){
	array_push($fields, "push_id");
	array_push($values, trim($_REQUEST["push_id"]));
	$count++;
}

if(isset($_REQUEST["user_mesg"])){
	array_push($fields, "note");
	array_push($values, trim($_REQUEST["user_mesg"]));
	$count++;
}

/**
if(isset($_REQUEST["photo"])){
	array_push($fields, "photo");
	array_push($values, trim($_REQUEST["photo"]));
	$count++;
}

if(isset($_REQUEST["ncard"])){
	array_push($fields, "ncard");
	array_push($values, trim($_REQUEST["ncard"]));
	$count++;
}
**/
/***
if(isset($_REQUEST["gps_lon"]) && isset($_REQUEST["gps_lat"]) && isset($_REQUEST["gps_addr"]))
{
    $lat = (float)trim($_REQUEST["gps_lat"]);
    $lon = (float)trim($_REQUEST["gps_lon"]);
    array_push($fields, "gps");
    array_push($values, GeomFromText('POINT('.$lon.' '.$lat.')'));

    array_push($fields, "gps_address");
    array_push($values, trim($_REQUEST["gps_addr"]));
    $count++;
}
***/
if(isset($_FILES['photo']) || isset($_FILES['ncard']))
{
	$conn->DeleteImage($conn->target_dir, "rec_id", $rec_id, TRUE);
	if(isset($_FILES['photo']))
	{
	    echo ">>> Upload OK::".$_FILES["photo"]["tmp_name"];
	    
		$target_file = $conn->SaveImage($conn->target_dir, $_FILES["photo"]["tmp_name"], $rec_num, true);
		if($target_file)
		{
			//echo ">>> Upload OK::".$target_path;
			array_push($fields, "photo_path");
			array_push($values, $target_file);
			$count++; 
		}
	}

	if(isset($_FILES['ncard']))
	{
		$target_file = $conn->SaveImage($conn->target_dir, $_FILES["ncard"]["tmp_name"], $rec_num, true);
		if($target_file)
		{
			//echo ">>> Upload OK::".$target_path;
			array_push($fields, "business_card_path");
			array_push($values, $target_file);
			$count++; 
		}
	}
}

if($count <1)
{
    if($update_count>0)
        $conn->ReportMessage("OK", "데이터가 수정되었습니다.");
    
	return $conn->ReportMessage("FAIL", "부적절한 데이터입니다.");
}

if(!$conn->UpdateRecord($rec_id, $fields, $values))
{
	return $conn->ReportMessage("FAIL", "데이터 변경중 오류가 발생하였습니다.");
}

$conn->ReportMessage("OK", "데이터가 수정되었습니다.");

?>
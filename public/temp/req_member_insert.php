
<?php
// 사업장 정보 추가.
// key->mem_id
//http://localhost/mob/req_member_insert.php?user_id=kkk@kkk.com&user_pwd=1234&user_name=테스트
include_once './db_member_info.php';

$conn = new DbMemberInfo();
if($conn->connect()==0){
	return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

if(!isset($_REQUEST["user_id"]))
{
	return $conn->ReportMessage("FAIL", "등록된 회원정보가 없습니다.");
}

$fields = array();
$values = array();

$count = 0;
if(isset($_REQUEST["user_id"])){
	array_push($fields, "user_id");
	array_push($values, trim($_REQUEST["user_id"]));
	$count++;
}

if(isset($_REQUEST["user_pwd"])){
	array_push($fields, "user_pwd");
	array_push($values, trim($_REQUEST["user_pwd"]));
	$count++;
}

if(isset($_REQUEST["user_name"])){
	array_push($fields, "user_name");
	array_push($values, trim($_REQUEST["user_name"]));
	$count++;
}
/***
if(isset($_REQUEST["tel"])){
	array_push($fields, "tel");
	array_push($values, trim($_REQUEST["tel"]));
	$count++;
}

if(isset($_REQUEST["fax"])){
	array_push($fields, "fax");
	array_push($values, trim($_REQUEST["fax"]));
	$count++;
}

if(isset($_REQUEST["addr"])){
	array_push($fields, "addr");
	array_push($values, trim($_REQUEST["addr"]));
	$count++;
}

if(isset($_REQUEST["url"])){
	array_push($fields, "url");
	array_push($values, trim($_REQUEST["url"]));
	$count++;
}

if(isset($_REQUEST["tag1"])){
	array_push($fields, "tag1");
	array_push($values, trim($_REQUEST["tag1"]));
	$count++;
}

if(isset($_REQUEST["tag2"])){
	array_push($fields, "tag2");
	array_push($values, trim($_REQUEST["tag2"]));
	$count++;
}
if(isset($_REQUEST["tag3"])){
	array_push($fields, "tag3");
	array_push($values, trim($_REQUEST["tag3"]));
	$count++;
}
if(isset($_REQUEST["tag4"])){
	array_push($fields, "tag4");
	array_push($values, trim($_REQUEST["tag4"]));
	$count++;
}

if(isset($_REQUEST["gps_lon"])){
	array_push($fields, "gps_lon");
	array_push($values, (float)trim($_REQUEST["gps_lon"]));
	$count++;
}

if(isset($_REQUEST["gps_lat"])){
	array_push($fields, "gps_lat");
	array_push($values, (float)trim($_REQUEST["gps_lat"]));
	$count++;
}

if(isset($_REQUEST["memo"])){
	array_push($fields, "memo");
	array_push($values, trim($_REQUEST["memo"]));
	$count++;
}

if(isset($_REQUEST["sales"])){
	array_push($fields, "sales");
	array_push($values, trim($_REQUEST["sales"]));
	$count++;
}

if(isset($_REQUEST["closed"])){
	array_push($fields, "closed");
	array_push($values, trim($_REQUEST["closed"]));
	$count++;
}
***/
if(isset($_FILES['photo_real']) || isset($_FILES['photo_thum']))
{
	$rec_num = (string)$conn->GetNextRecID();
	if(isset($_FILES['photo_real']))
	{
		$target_file = $conn->SaveImage($conn->target_dir, $_FILES["photo_real"]["tmp_name"], $rec_num, true);
		if($target_file)
		{
			//echo ">>> Upload OK::".$target_path;
			array_push($fields, "photo_real");
			array_push($values, $target_file);
			$count++; 
		}
	}

	if(isset($_FILES['photo_thum']))
	{
		$target_file = $conn->SaveImage($conn->target_dir, $_FILES["photo_thum"]["tmp_name"], $rec_num, false);
		if($target_file)
		{
			//echo ">>> Upload OK::".$target_path;
			array_push($fields, "photo_thum");
			array_push($values, $target_file);
			$count++; 
		}
	}
}

if($count <1)
{
	return $conn->ReportMessage("FAIL", "부적절한 데이터입니다.");
}

if(!$conn->AddRecord($fields, $values))
{
	return $conn->ReportMessage("FAIL", "데이터 저장중 오류가 발생하였습니다.");
}

$conn->ReportMessage("OK", "데이터가 추가 되었습니다.");

?>
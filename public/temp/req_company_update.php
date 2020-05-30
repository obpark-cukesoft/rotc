
<?php
// 사업장 정보 수정.
// key->rec_id

include_once './db_company_info.php';

$conn = new DbCompanyInfo();
if(!isset($_REQUEST["rec_id"]))
{
	return $conn->ReportMessage("FAIL", "등록된 사업장이 아닙니다.");
}

if(!$conn->connect())
{
	return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

$rec_num = trim($_REQUEST["rec_id"]);
$rec_id = (int)$rec_num;

$fields = array();
$values = array();

$count = 0;

if(isset($_REQUEST["company"])){
	array_push($fields, "company");
	array_push($values, trim($_REQUEST["company"]));
	$count++;
}

if(isset($_REQUEST["benefit"])){
	array_push($fields, "benefit");
	array_push($values, trim($_REQUEST["benefit"]));
	$count++;
}

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

if(isset($_REQUEST["photo_real"])){
    $conn->DeleteImage($conn->target_dir, "rec_id", $rec_id, TRUE);
    $conn->ReportMessage("OK", "데이터가 수정되었습니다.");
    return;
}

if(isset($_FILES['photo_real']) || isset($_FILES['photo_thum']))
{

	$conn->DeleteImage($conn->target_dir, "rec_id", $rec_id, TRUE);
	if(isset($_FILES['photo_real']))
	{
	    $count++; 
		$target_file = $conn->SaveImage($conn->target_dir, $_FILES["photo_real"]["tmp_name"], $rec_num, true);
		if($target_file)
		{
			//echo ">>> Upload OK::".$target_path;
			array_push($fields, "photo_real");
			array_push($values, $target_file);
			
		}
	}

	if(isset($_FILES['photo_thum']))
	{
	    $count++; 
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

if($count<1)
{
	return $conn->ReportMessage("FAIL", "부적절한 데이터입니다.");
}

if($conn->UpdateRecord($rec_id, $fields, $values))
{
	$conn->ReportMessage("OK", "데이터가 수정되었습니다.");
}

?>
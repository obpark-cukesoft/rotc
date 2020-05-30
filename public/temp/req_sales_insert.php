<?php
include_once './db_sales_info.php';

$conn = new DbSalesInfo();

$fields = array();
$values = array();
$count = 0;

if($conn->connect()==0){
	return $conn->ReportMessage("FAIL", "데이터베이스에 연결할 수 없습니다.");
}

if(!isset($_REQUEST["mem_id"]) || !isset($_REQUEST["owner_id"]) || !isset($_REQUEST["company_id"]))
{
	return $conn->ReportMessage("FAIL", "부적절한 데이터 입니다.");
}


if(isset($_REQUEST["mem_id"])){
	array_push($fields, "mem_id");
	array_push($values, (int)trim($_REQUEST["mem_id"]));
	$count++;
}

if(isset($_REQUEST["owner_id"])){
	array_push($fields, "owner_id");
	array_push($values, (int)trim($_REQUEST["owner_id"]));
	$count++;
}

if(isset($_REQUEST["company_id"])){
	array_push($fields, "company_id");
	array_push($values, (int)trim($_REQUEST["company_id"]));
	$count++;
}

if(isset($_REQUEST["insert_stamp"])){
    array_push($fields, "insert_stamp");
    array_push($values, trim($_REQUEST["insert_stamp"]));
    $count++;
}

if(isset($_REQUEST["item"])){
	array_push($fields, "item");
	array_push($values, trim($_REQUEST["item"]));
	$count++;
}

if(isset($_REQUEST["price"])){
	array_push($fields, "price");
	array_push($values, (int)trim($_REQUEST["price"]));
	$count++;
}

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

if(!$conn->AddRecord($fields, $values))
{
	return $conn->ReportMessage("FAIL", "데이터 추가 오류입니다.");
}

$conn->ReportMessage("OK", "데이터가 저장 되었습니다.");

?>
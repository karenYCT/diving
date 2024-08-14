<?
require("./conn.php");

//先確定是否有資料讀取成功
//接著讀去一共有幾筆資料
$sql = "SELECT * FROM `divesite`";
try {
	$stmt = $pdo->query($sql);
	$divesiteCount = $stmt->rowCount();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
	$divesiteCount = -1;
} finally {
	$pdo = null;
}
?>

<!DOCTYPE html>
<html lang="zh-hant-TW">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>潛點列表</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
	<div class="container">
		<h1 class="mt-3" >潛點列表</h1>
		<div class="count">
			目前共<?=$divesiteCount?>筆資料
	</div>
		<div class="divesite bg-primary-subtle text-primary-emphasis ps-1  d-flex justify-content-between">
		<div class="id">id</div>
		<div class="name">潛點名稱</div>
		<div class="location">地區</div>
		<div class="maxDep">最大深度</div>
		<div class="intro">潛點介紹</div>
		<div class="method">方式</div>
		<div class="img">圖片</div>
	</div>
	<div class="divesite d-flex justify-content-between">
	<? if ($divesiteCount > 0) : ?>
		<? foreach ($rows as $row) : ?>
		<div class="id"><?=$row["divesite_id"]?></div>
		<div class="name"><?=$row["divesite_name"]?></div>
		<div class="location"><?=$row["location_id"]?></div>
		<div class="maxDep"><?=$row["divesite_dep"]?></div>
		<div class="intro"><?=$row["divesite_intro"]?></div>
		<div class="method"><?=$row["method_id"]?></div>
		<div class="img"><?=$row["media_id"]?></div>
		<? endforeach ?>
	<? else : ?>
		<p>沒有資料可以顯示。</p>
	<? endif ; ?>
	</div>
</body>

</html>
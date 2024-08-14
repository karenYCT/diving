<?php
require("conn.php");

// 取得 location 的資料
$sqlLocation = "SELECT * FROM `location`";
$stmtLocation = $pdo->prepare($sqlLocation);
try {
	$stmtLocation->execute();
	$locationRows = $stmtLocation->fetchAll();
	$locationCount = count($locationRows);
} catch (PDOException $exception) {
	$errorMsg = $exception->getMessage();
	$categoryCount = 0;
}

// 取得 method 的資料
$sqlMethod = "SELECT * FROM `method`";
$stmtMethod = $pdo->prepare($sqlMethod);

try {
	$stmtMethod->execute();
	$methodRows = $stmtMethod->fetchAll();
	$methodCount = count($methodRows);
} catch (PDOException $exception) {
	$errorMsg = $exception->getMessage();
	$employeeCount = 0;
}

?>

<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>新增資料</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
	<div class="container mt-3">
		<h1 class="title">新增潛點資料</h1>
		<form action="./doDsInsert.php" method="post" enctype="multipart/form-data" onsubmit="console.log('表單正在提交')">

			<div class="content-area">
				<div class="input-group mb-1">
					<span class="input-group-text">潛點名稱</span>
					<input name="divesite_name" type="text" class="form-control" id="divesite_name" placeholder="潛點名稱">
					<span class="text-from text-danger" idn="nameErrorText"></span>
				</div>

				<div class="input-group mt-1 mb-1">
					<span class="input-group-text">地區</span>
					<select name="location_name" class="form-select" id="location_name">
						<option value selected disabled>請選擇</option>
						<?php foreach ($locationRows as $locationRow) : ?>
							<option value="<?= $locationRow['location_id'] ?>"><?= $locationRow['location_name'] ?></option>
						<?php endforeach; ?>
					</select>
					<span class="text-from text-danger" idn="locationErrorText"></span>
				</div>

				<div class="input-group mt-1 mb-1">
					<span class="input-group-text">最大深度</span>
					<input name="divesite_dep" type="number" class="form-control" id="divesite_dep">
					<span class="input-group-text">米</span>
					<span class="text-from text-danger" idn="depErrorText"></span>
				</div>

				<div class="input-group mt-1 mb-1">
					<span class="input-group-text">潛點描述</span>
					<textarea name="divesite_intro" class="form-control" id="divesite_intro" aria-label="With textarea"></textarea>
					<span class="text-from text-danger" idn="introErrorText"></span>
				</div>

				<div class="input-group mt-1 mb-1">
					<span class="input-group-text">潛水方式</span>
					<select name="method_name" class="form-select" id="method_name">
						<option value selected disabled>請選擇</option>
						<?php foreach ($methodRows as $methodRow) : ?>
							<option value="<?= $methodRow['method_id'] ?>"><?= $methodRow['method_name'] ?></option>
						<?php endforeach; ?>
					</select>
					<span class="text-from text-danger" idn="methodErrorText"></span>
				</div>
				<!-- <div>
					<input name="media_path" type="file"  class="form-control mt-1 mb-3" id="media_path" accept="image/*" multiple="multiple">
					<span class="text-from text-danger" idn="fileErrorText"></span>
				</div> -->
				<div>
					<button type="button" class="btn btn-secondary mb-3" onclick="openMediaLibrary()">選擇圖片</button>
					<div id="selectedMediaContainer"></div>
					<span class="text-from text-danger" id="fileErrorText"></span>
				</div>
			</div>

			<div class="mt-1 text-end">
				<button type="submit" class="btn-submit btn btn-info">送出</button>
			</div>
		</form>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

	<script>
		function openMediaLibrary() {
			window.open("../media/mediaInsert.php?select=true", "mediaInsert2", "width=800,height=600")
		}

		window.addEventListener("message", function(event) {
			if (event.data.type === "selectedMedia") {
				const selectedMediaContainer = document.getElementById("selectedMediaContainer");
				selectedMediaContainer.innerHTML = "";
				event.data.media.forEach(function(media) {
					const img = document.createElement("img");
					img.src = '../media/upload/' + media.path;
					img.alt = media.name;
					img.style.width = '100px';
					img.style.height = '100px';
					img.style.objectFit = 'cover';
					img.style.marginRight = '10px';
					selectedMediaContainer.appendChild(img);

					const input = document.createElement("input");
					input.type = 'hidden';
					input.name = 'selected_media[]';
					input.value = media.id;
					selectedMediaContainer.appendChild(input);
				});
			}
		})
	</script>

	<script>
		// 獲取表單元素
		const form = document.querySelector("form");

		// 獲取輸入字段
		const divesiteName = document.getElementById("divesite_name");
		const divesiteLocationName = document.getElementById("location_name");
		const divesiteDep = document.getElementById("divesite_dep");
		const divesiteIntro = document.getElementById("divesite_intro");
		const methodName = document.getElementById("method_name");

		// 獲取錯誤文本元素
		const nameErrorText = document.querySelector("[idn='nameErrorText']");
		const locationErrorText = document.querySelector("[idn='locationErrorText']");
		const depErrorText = document.querySelector("[idn='depErrorText']");
		const introErrorText = document.querySelector("[idn='introErrorText']");
		const methodErrorText = document.querySelector("[idn='methodErrorText']");
		const fileErrorText = document.getElementById("fileErrorText");

		// 表單提交事件
		form.addEventListener("submit", function(e) {
			e.preventDefault();
			let isValid = true;
			const nameMaxLength = 50;
			const depMaxLength = 2;
			const introMaxLength = 1000;

			// 驗證潛點名稱
			if (divesiteName && (divesiteName.value.trim() === "" || divesiteName.value.length > nameMaxLength)) {
				if (nameErrorText) nameErrorText.textContent = "請輸入潛點名稱，最多50字";
				isValid = false;
			} else {
				if (nameErrorText) nameErrorText.textContent = "";
			}

			// 驗證地區
			if (divesiteLocationName && divesiteLocationName.value === "") {
				if (locationErrorText) locationErrorText.textContent = "請選擇潛點地區";
				isValid = false;
			} else {
				if (locationErrorText) locationErrorText.textContent = "";
			}

			// 驗證最大深度
			if (divesiteDep && divesiteDep.value !== "" && (isNaN(divesiteDep.value) || divesiteDep.value < 0 || divesiteDep.value > 99)) {
				if (depErrorText) depErrorText.textContent = "請輸入0-99之間的數字";
				isValid = false;
			} else {
				if (depErrorText) depErrorText.textContent = "";
			}

			// 驗證潛點描述
			if (divesiteIntro && divesiteIntro.value.length > introMaxLength) {
				if (introErrorText) introErrorText.textContent = "限1000字內";
				isValid = false;
			} else {
				if (introErrorText) introErrorText.textContent = "";
			}

			// 驗證潛水方式
			if (methodName && methodName.value === "") {
				if (methodErrorText) methodErrorText.textContent = "請選擇潛水方式";
				isValid = false;
			} else {
				if (methodErrorText) methodErrorText.textContent = "";
			}

			// 驗證是否選擇了圖片
			const selectedMedia = document.querySelectorAll('input[name="selected_media[]"]');
			if (selectedMedia.length === 0) {
				if (fileErrorText) fileErrorText.textContent = "請選擇至少一張圖片";
				isValid = false;
			} else {
				if (fileErrorText) fileErrorText.textContent = "";
			}

			// 如果所有驗證都通過，提交表單
			if (isValid) {
				console.log("表單驗證通過，準備提交");
				this.submit();
			} else {
				console.log("表單驗證失敗，請檢查錯誤訊息");
			}
		});
	</script>
</body>

</html>
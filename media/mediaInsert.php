<?php
require("conn.php");

// 取得 media_category 的資料
$sqlCategory = "SELECT * FROM `media_category`";
$stmtCategory = $pdo->prepare($sqlCategory);
try {
    $stmtCategory->execute();
    $categoryRows = $stmtCategory->fetchAll();
    $categoryCount = count($categoryRows);
} catch (PDOException $exception) {
    $errorMsg = $exception->getMessage();
    $categoryCount = 0;
}

// 取得 employee 的資料
$sqlEmployee = "SELECT * FROM `employee`";
$stmtEmployee = $pdo->prepare($sqlEmployee);

try {
    $stmtEmployee->execute();
    $employeeRows = $stmtEmployee->fetchAll();
    $employeeCount = count($employeeRows);
} catch (PDOException $exception) {
    $errorMsg = $exception->getMessage();
    $employeeCount = 0;
}

$fromLibrary = isset($_GET['fromLibrary']) && $_GET['fromLibrary'] == 'true';

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>新增圖片</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-3">
        <form action="./doMediaInsert.php<?= $fromLibrary ? '?fromLibrary=true' : '' ?>" method="post" enctype="multipart/form-data">
            <div class="content-area">
                <div class="input-group mb-1">
                    <span class="input-group-text">圖片名稱</span>
                    <input name="media_name[]" type="text" class="form-control" placeholder="圖片名稱">
                </div>
                <div class="input-group mt-1 mb-1">
                    <span class="input-group-text">分類</span>
                    <select name="mcat_name[]" class="form-select">
                        <option value selected disabled>請選擇</option>
                        <?php foreach ($categoryRows as $category) : ?>
                            <option value="<?= $category['mcat_id'] ?>"><?= $category['mcat_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-group mt-1 mb-1">
                    <span class="input-group-text">上傳者</span>
                    <select name="em_full_name[]" class="form-select">
                        <option value selected disabled>請選擇</option>
                        <?php foreach ($employeeRows as $employee) : ?>
                            <option value="<?= $employee['em_id'] ?>"><?= $employee['em_full_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <input class="form-control mt-1 mb-3" type="file" name="media_path[]" accept="image/*" multiple="multiple">
                </div>
            </div>
            <div class="mt-1 text-end">
                <button type="submit" class="btn-submit btn btn-info">送出</button>
                <button type="button" class="btn btn-primary btn-add">增加一組</button>
                <button onclick="window.close();" class="btn btn-secondary">返回媒體庫</button>
                <!-- <?php if ($fromLibrary): ?>
                <a href="mediaLibrary.php?select=true" class="btn btn-secondary">返回媒體庫</a>
                <?php endif; ?> -->
            </div>
        </form>
        <template id="inputs">
            <div class="input-group mb-1">
                <span class="input-group-text">圖片名稱</span>
                <input name="media_name[]" type="text" class="form-control" placeholder="圖片名稱">
            </div>
            <div class="input-group mt-1 mb-1">
                <span class="input-group-text">分類</span>
                <select name="mcat_name[]" class="form-select">
                    <option value selected disabled>請選擇</option>
                    <?php foreach ($categoryRows as $category) : ?>
                        <option value="<?= $category['mcat_id'] ?>"><?= $category['mcat_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="input-group mt-1 mb-1">
                <span class="input-group-text">上傳者</span>
                <select name="em_full_name[]" class="form-select">
                    <option value selected disabled>請選擇</option>
                    <?php foreach ($employeeRows as $employee) : ?>
                        <option value="<?= $employee['em_id'] ?>"><?= $employee['em_full_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <input class="form-control mt-1 mb-3" type="file" name="media_path[]" accept="image/*" multiple="multiple">
            </div>
        </template>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        // 輸入多筆資料方式
        const btnAdd = document.querySelector(".btn-add");
        const contentArea = document.querySelector(".content-area");
        const template = document.querySelector("#inputs");
        const form = document.querySelector("form");
        const btnSubmit = document.querySelector(".btn-submit ");

        btnAdd.addEventListener("click", e => {
            e.preventDefault();
            const node = template.content.cloneNode(true);
            contentArea.appendChild(node);
        });
        btnSubmit.addEventListener("click", e => {
            e.preventDefault();

            const files = document.querySelectorAll("input[type=file]");
            let fileCheck = true;
            files.forEach(function(file) {
                if (file.value == "") {
                    fileCheck = false;
                }
            });
            if (fileCheck == true) {
                form.submit();
            } else {
                alert("請選擇檔案");
            }
        });
    </script>
</body>

</html>
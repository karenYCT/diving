<?php
require("conn.php");

if(!isset($_GET['media_id'])){
    echo "資料不存在";
    exit;
}

$media_id = $_GET["media_id"];
$sql1 = "SELECT * FROM `media` WHERE `media_id` = ? "; 
$sql2 = "SELECT * FROM `media_category`";
$sql3 = "SELECT * FROM `employee`";

try {
    $stmt1 = $pdo->prepare($sql1);
    $stmt1->bindValue(1, $media_id);
    $stmt1->execute();
    $row = $stmt1->fetch(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->query($sql2);
    $categoryRows = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $stmt3 = $pdo->query($sql3);
    $employeeRows = $stmt3->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    die();
}

?>
<!DOCTYPE html>
<html lang="zh-hant-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>媒體詳情</title>
    <style>
        .image-container {
            max-width: 100%;
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .image-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="container mt-3">
        <?php if (!$row): ?>
            <div class="alert alert-danger" role="alert">
                資料不存在
            </div>
            <a href="./mediaLibrary.php" class="btn btn-primary">返回</a>
        <?php else: ?>
            <!-- 圖片顯示區域 -->
            <div class="image-container">
                <?php if (!empty($row["media_path"])): ?>
                    <img src="upload/<?= htmlspecialchars($row["media_path"]) ?>" alt="<?= htmlspecialchars($row["media_name"]) ?>">
                <?php else: ?>
                    <p>無可用圖片</p>
                <?php endif; ?>
            </div>

            <form action="./doUpdate.php" method="post" enctype="multipart/form-data">

                <!-- 隱藏的輸入欄位來保存原始id以及媒體路徑 -->
                <input type="hidden" name="media_id" value="<?= $media_id ?>">
                <input type="hidden" name="old_media_path" value="<?= htmlspecialchars($row["media_path"]) ?>">
                
                <div class="content-area">
                    <div class="input-group mb-1">
                        <span class="input-group-text">圖片名稱</span>
                        <input name="media_name" type="text" class="form-control" placeholder="圖片名稱" value="<?= htmlspecialchars($row["media_name"]) ?>">
                    </div>
                    <div class="input-group mt-1 mb-1">
                        <span class="input-group-text">分類</span>
                        <select name="mcat_id" class="form-select">
                            <?php 
                            $selectedCategory = false;
                            foreach ($categoryRows as $categoryRow):
                                $isSelected = $categoryRow['mcat_id'] == $row["mcat_id"];
                                if ($isSelected) $selectedCategory = true;
                            ?>
                                <option value="<?= $categoryRow['mcat_id'] ?>" <?= $isSelected ? "selected" : ""; ?>><?= htmlspecialchars($categoryRow['mcat_name']) ?></option>
                            <?php endforeach; ?>
                            <?php if (!$selectedCategory): ?>
                                <option value="" selected>請選擇分類</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="input-group mt-1 mb-1">
                        <span class="input-group-text">上傳者</span>
                        <select name="em_id" class="form-select">
                            <?php 
                            $selectedEmployee = false;
                            foreach ($employeeRows as $employeeRow):
                                $isSelected = $employeeRow["em_id"] == $row["em_id"];
                                if ($isSelected) $selectedEmployee = true;
                            ?>
                                <option value="<?= $employeeRow['em_id'] ?>" <?= $isSelected ? "selected" : ""; ?>><?= htmlspecialchars($employeeRow['em_full_name']) ?></option>
                            <?php endforeach; ?>
                            <?php if (!$selectedEmployee): ?>
                                <option value="" selected>請選擇上傳者</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <input class="form-control mt-1 mb-3" type="file" name="media_path" accept="image/*">
                    </div>
                </div>
                <div class="mt-1 text-end">
                    <button type="submit" class="btn btn-primary">更新</button>
                    <a href="./mediaLibrary.php" class="btn btn-secondary">返回</a>
                </div>
            </form>
        <?php endif; ?>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
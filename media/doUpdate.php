<?php
require("conn.php");
require("utilities.php");

if(!isset($_POST['media_id'])){
    echo "資料不存在";
    exit;
}

$mediaId = (int)$_POST["media_id"];
$mediaName = $_POST["media_name"];
$mediaCategory = $_POST["mcat_id"];
$mediaEmployee = $_POST["em_id"];
$oldMediaPath = $_POST["old_media_path"];

// 驗證邏輯
if (empty($mediaName)) {
    alertAndBack("圖片名稱沒有填寫!");
    exit;
}
if (empty($mediaCategory)) {
    alertAndBack("分類沒有選擇!");
    exit;
}
if (empty($mediaEmployee)) {
    alertAndBack("上傳者沒有選擇!");
    exit;
}

$newMediaPath = $oldMediaPath;

// 處理檔案上傳
if(isset($_FILES['media_path']) && $_FILES['media_path']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = './upload/';
    $timestamp = time();
    $ext = pathinfo($_FILES['media_path']['name'], PATHINFO_EXTENSION);
    $newMediaPath = $timestamp . '.' . $ext;
    $uploadFile = $uploadDir . $newMediaPath;

    if (move_uploaded_file($_FILES['media_path']['tmp_name'], $uploadFile)) {
        // 刪除舊檔案
        if (!empty($oldMediaPath) && file_exists($uploadDir . $oldMediaPath)) {
            unlink($uploadDir . $oldMediaPath);
        }
    } else {
        alertAndBack("檔案上傳失敗!");
        exit;
    }
}

// 準備 SQL 語句
$sql = "UPDATE `media` SET 
        `media_name` = :name, 
        `media_path` = :path, 
        `mcat_id` = :category, 
        `em_id` = :employee,
        `modifyTime` = CURRENT_TIMESTAMP
        WHERE `media_id` = :id";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $mediaName,
        ':path' => $newMediaPath,
        ':category' => $mediaCategory,
        ':employee' => $mediaEmployee,
        ':id' => $mediaId
    ]);

    if ($stmt->rowCount() > 0) {
        echo "更新成功";
        echo '<script>
            setTimeout(function() {
                window.location.href = "mediaLibrary.php";
            }, 5000);
        </script>';
    } else {
        echo "沒有資料被更新";
    }
} catch (PDOException $e) {
    alertAndBack("更新失敗: " . $e->getMessage());
}
?>
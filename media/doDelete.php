<?
require("conn.php");
require("utilities.php");

if (!isset($_GET["media_id"])) {
    echo "請由正式方法進入頁面";
    exit;
}

$mediaId = (int)$_GET["media_id"];

// $sql = "DELETE FROM media WHERE `media_id` = :mediaId";

$sql = "UPDATE `media` SET `endTime` = CURRENT_TIMESTAMP WHERE `media_id` = :mediaId;";


try{

    $pdo->beginTransaction(); // 開始事務
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':mediaId', $mediaId, PDO::PARAM_INT);
    $stmt->execute();

    // 檢查是否有行被刪除
    if ($stmt->rowCount() > 0) {
        $pdo->commit(); // 提交事務
        echo "刪除成功";
        echo '<script>
                setTimeout(function() {
                window.location.href = "mediaLibrary.php";
                }, 3000);
                </script>';
    } else {
        throw new PDOException("沒有找到要刪除的記錄");
    }
    } catch (PDOException $exception) {
    $pdo->rollBack(); // 回滾事務
    alertAndBack("資料刪除失敗：" . $exception->getMessage());
    }



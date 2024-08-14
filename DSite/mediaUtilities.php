<?
function insertMediaRelations($pdo, $contentType, $contentId, $selectedMedia) {
    // 獲取對應的 mcat_id 和 mcat_name
    $mcatSql = "SELECT mcat_id, mcat_name FROM media_category WHERE mcat_name = ? LIMIT 1";
    $mcatStmt = $pdo->prepare($mcatSql);
    $mcatStmt->execute([$contentType]);
    $mcatInfo = $mcatStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$mcatInfo) {
        throw new Exception("找不到對應的 media_category: $contentType");
    }

    $relationSql = "INSERT INTO `media_relation` (`media_id`, `mu_content_id`, `mcat_id`) VALUES (?, ?, ?)";
    $relationStmt = $pdo->prepare($relationSql);
    
    $insertedCount = 0;
    foreach ($selectedMedia as $mediaId) {
        try {
            $relationStmt->execute([$mediaId, $contentId, $mcatInfo['mcat_id']]);
            $insertedCount++;
        } catch (PDOException $e) {
            error_log("插入媒體關聯失敗 - 內容類型: $contentType, 內容ID: $contentId, 媒體ID: $mediaId. 錯誤: " . $e->getMessage());
            // 根據需要決定是否繼續插入其他記錄或拋出異常
        }
    }
    
    return $insertedCount;
}
?>
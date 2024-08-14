<?php
require("./conn.php");

// 處理排序
$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'divesite_id';
$orderDirection = isset($_GET['orderDirection']) && $_GET['orderDirection'] === 'DESC' ? 'DESC' : 'ASC';

// 處理分頁
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10; // 每頁顯示的記錄數
$offset = ($page - 1) * $perPage;

// 構建 SQL 查詢
$sql = "SELECT d.*, l.location_name, m.method_name, 
            (SELECT media_path FROM media WHERE media_id = 
                (SELECT media_id FROM media_relation WHERE mu_content_id = d.divesite_id AND mcat_id = 
                    (SELECT mcat_id FROM media_category WHERE mcat_name = '潛點' LIMIT 1)
                LIMIT 1)
            ) as media_path
        FROM `divesite` d
        LEFT JOIN `location` l ON d.location_id = l.location_id
        LEFT JOIN `method` m ON d.method_id = m.method_id
        ORDER BY $orderBy $orderDirection
        LIMIT $offset, $perPage";

// 計算總記錄數的查詢
$countSql = "SELECT COUNT(*) FROM `divesite`";

try {
    // 執行主查詢
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 計算總記錄數
    $countStmt = $pdo->query($countSql);
    $totalCount = $countStmt->fetchColumn();
    $totalPages = ceil($totalCount / $perPage);

} catch (PDOException $exception) {
    echo "查詢失敗: " . $exception->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-hant-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>潛點列表</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .divesite-row { border-bottom: 1px solid #dee2e6; padding: 10px 0; }
        .divesite-row:nth-child(even) { background-color: #f8f9fa; }
        .divesite-header { font-weight: bold; background-color: #e9ecef; border-bottom: 2px solid #dee2e6; }
        .truncate { max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .thumbnail { width: 50px; height: 50px; object-fit: cover; }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h1 class="mb-4">潛點列表</h1>
        <div class="mb-3">
            目前共<?=$totalCount?>筆資料，顯示第<?=$page?>頁
        </div>
        
        <div class="row divesite-row divesite-header">
            <div class="col-1"><a href="?orderBy=divesite_id&orderDirection=<?= $orderBy === 'divesite_id' && $orderDirection === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></div>
            <div class="col-2"><a href="?orderBy=divesite_name&orderDirection=<?= $orderBy === 'divesite_name' && $orderDirection === 'ASC' ? 'DESC' : 'ASC' ?>">潛點名稱</a></div>
            <div class="col-2"><a href="?orderBy=location_name&orderDirection=<?= $orderBy === 'location_name' && $orderDirection === 'ASC' ? 'DESC' : 'ASC' ?>">地區</a></div>
            <div class="col-1"><a href="?orderBy=divesite_dep&orderDirection=<?= $orderBy === 'divesite_dep' && $orderDirection === 'ASC' ? 'DESC' : 'ASC' ?>">最大深度</a></div>
            <div class="col-3">潛點介紹</div>
            <div class="col-2"><a href="?orderBy=method_name&orderDirection=<?= $orderBy === 'method_name' && $orderDirection === 'ASC' ? 'DESC' : 'ASC' ?>">方式</a></div>
            <div class="col-1">圖片</div>
        </div>

        <?php foreach ($rows as $row) : ?>
            <div class="row divesite-row">
                <div class="col-1"><?=$row["divesite_id"]?></div>
                <div class="col-2"><?=$row["divesite_name"]?></div>
                <div class="col-2"><?=$row["location_name"]?></div>
                <div class="col-1"><?=$row["divesite_dep"]?> m</div>
                <div class="col-3 truncate" title="<?=$row["divesite_intro"]?>"><?=$row["divesite_intro"]?></div>
                <div class="col-2"><?=$row["method_name"]?></div>
                <div class="col-1">
                    <?php if ($row["media_path"]) : ?>
                        <img src="../media/upload/<?=$row["media_path"]?>" alt="<?=$row["divesite_name"]?>" class="thumbnail">
                    <?php else : ?>
                        無圖片
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- 分頁控件 -->
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?=$i?>&orderBy=<?=$orderBy?>&orderDirection=<?=$orderDirection?>"><?=$i?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?
require_once("conn.php");

//變數的宣告

$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
$perPage = 15;
$pageStart = ($page - 1) * $perPage;

$cid = isset($_GET["cid"]) ? (int)$_GET["cid"] : 0;
if ($cid === 0) {
    $cateSQL = "";
} else {
    $cateSQL = "`mcat_id`=  $cid  AND";
}

$sqlAll = "SELECT * FROM `media` WHERE $cateSQL `endTime` is Null OR `endTime`> NOW()";
$sql = "SELECT * FROM `media` WHERE $cateSQL `endTime` is Null OR `endTime`> NOW() LIMIT $pageStart, $perPage";
$sql2 = " SELECT * FROM `media_category`"; //用於獲取所有分類的 SQL 查詢。

//選擇既有圖片功能
// $mediaSelectMode = isset ($_GET['select']) && $_GET['select'] == "true";

//完成當前操作後應該返回的頁面 URL
// $returnUrl = isset ($_GET['return']) ?  $_GET['return']:"";

try {
    // 執行主要查詢
    $stmt = $pdo->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 執行計數查詢
    $stmtAll = $pdo->query($sqlAll);
    $mediaCount = $stmtAll->rowCount();
    $totalCount = $mediaCount;
    $totalPage = ceil($totalCount / $perPage);

    // 執行分類查詢
    $stmt2 = $pdo->query($sql2);
    $categoryRows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "資料讀取錯誤: " . $e->getMessage();
    $mediaCount = -1;
}

$pdo = null;
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>媒體圖庫列表</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        .nav-tabs {
            margin-bottom: 1rem;
        }

        .card-container {
            margin-top: 1rem;
        }

        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card-img-wrapper {
            position: relative;
            padding-top: 100%;
            /* 1:1 Aspect Ratio */
            overflow: hidden;
        }

        .card-img-top {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .card-title {
            margin-bottom: auto;
        }

        @media (min-width: 576px) {
            .row-cols-sm-2>* {
                flex: 0 0 auto;
                width: 50%;
            }
        }

        @media (min-width: 768px) {
            .row-cols-md-3>* {
                flex: 0 0 auto;
                width: 33.333333%;
            }
        }

        @media (min-width: 992px) {
            .row-cols-lg-4>* {
                flex: 0 0 auto;
                width: 25%;
            }
        }

        @media (min-width: 1200px) {
            .row-cols-xl-5>* {
                flex: 0 0 auto;
                width: 20%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>媒體庫</h1>
        <? if ($mediaCount > -1) : ?>
            <div class="my-2 d-flex">
                <span class="me-auto">目前共 <?= $mediaCount ?> 筆資料</span>
                
            </div>
        <? endif ?>

        <!-- 新增的按鈕區域 -->
        <div class="d-flex justify-content-between my-2">
        <a class="btn btn-light  me-2" href="mediaList.php">列表顯示</a>
        <a class="btn btn-primary me-2" href="mediaInsert.php">新增圖片</a>
        <!-- <div id="action-buttons" style="display: none;">
            <button type="button" class="btn btn-primary me-2" onclick="submitSelection()">確認提交</button>
            <button type="button" class="btn btn-secondary" onclick="cancelSelection()">取消</button>
        </div> -->
    </div>

        <!-- 分類標籤 -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link <?= $cid === 0 ? 'active' : '' ?>" href="mediaLibrary.php">全部</a>
            </li>
            <? foreach ($categoryRows as $category) : ?>
                <li class="nav-item">
                    <a class="nav-link <?= $cid === $category["mcat_id"] ? 'active' : '' ?>" href="mediaLibrary.php?cid=<?= $category["mcat_id"] ?>"><?= $category["mcat_name"] ?></a>
                </li>
            <? endforeach; ?>
        </ul>

        <!-- 圖片卡片 -->
        <div class="card-container mt-4 mb-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
                <?php foreach ($rows as $row) : ?>
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-img-wrapper">
                                <img src="upload/<?= $row["media_path"] ?>" alt="<?= $row["media_name"] ?>" class="card-img-top">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-left"><?= $row["media_name"] ?></h5>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <!-- <div class="form-check">
                                        <input class="form-check-input media-checkbox" type="checkbox"
                                            id="media<?= $row["media_id"] ?>"
                                            name="selected_media[]"
                                            value="<?= $row["media_id"] ?>"
                                            data-name="<?= $row["media_name"] ?>">
                                        <label class="form-check-label" for="media<?= $row["media_id"] ?>">選擇</label>
                                    </div> -->
                                    <div>
                                        <a href="./update.php?media_id=<?= $row["media_id"] ?>" class="btn btn-warning btn-sm me-1">修改</a>
                                        <a href="./doDelete.php?media_id=<?= $row["media_id"] ?>" class="btn btn-danger btn-sm">刪除</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>


        <!-- 頁碼的撰寫 -->
        <div aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? "disabled" : ""; ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?><? ($cid > 0) ? "&cid=$cid" : ""; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <? for ($i = 1; $i <= $totalPage; $i++) : ?>
                    <li class="page-item <?= $page === $i ? "active" : "" ?>" aria-current="page">
                        <a href="?page=<?= $i ?><?= $cid > 0 ? "&cid=$cid" : "" ?>" class="page-link"><?= $i ?></a>
                    </li>
                <? endfor; ?>
                <li class="page-item <?= ($page >= $totalPage) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?><? ($cid > 0) ? "&cid=$cid" : ""; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
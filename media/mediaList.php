<?
require_once("conn.php");

//變數的宣告

$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
$perPage = 10; // 或其他適當的數值
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
    <title>媒體列表</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<style>
    h1 {
        margin-top: 5px;
    }

    .mediaList {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        padding: 10px;
        border: 1px solid #ccc;
    }

    .id {
        width: 30px;
    }

    .name {
        width: 100px;
    }

    .category {
        width: 100px;
    }

    .pic {
        flex: 1;
    }

    .pic img {
        max-width: 50px;
        max-height: 50px;
    }

    .employee {
        width: 100px;
    }

    .edit {
        width: 100px;
    }
</style>

<body>
    <div class="container">
        <h1>媒體列表</h1>
        <? if ($mediaCount > -1) : ?>
            <div class="my-2 d-flex">
                <span class="me-auto">目前共 <?= $mediaCount ?> 筆資料</span>
                <a class="btn btn-light btn-sm mr-2" href="mediaLibrary.php">以圖庫顯示</a>
                <a class="btn btn-primary btn-sm" href="mediaInsert.php">增加資料</a>
            </div>
        <? endif ?>
        <div class="nav nav-tabs">
            <a class="nav-link active" href="mediaList.php">全部</a>
            <?php foreach ($categoryRows as $category) : ?>
                <a class="nav-link" href="mediaList.php?cid=<?= $category["mcat_id"] ?>">
                    <?= $category["mcat_name"] ?>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="mediaList text-bg-dark ps-1">
            <div class="id">#</div>
            <div class="name">圖片名稱</div>
            <div class="pic">圖片</div>
            <div class="employee">上傳者</div>
            <div class="edit">編輯</div>
        </div>
        <? if ($mediaCount > -1) : ?>
            <? foreach ($rows as $row) : ?>
                <div class="mediaList">
                    <div class="id"><?= $row["media_id"] ?></div>
                    <div class="name"><?= $row["media_name"] ?></div>
                    <div class="pic">
                        <img src="upload/<?= $row["media_path"] ?>" alt="<?= $row["media_name"] ?>">
                    </div>
                    <div class="employee"><?= $row["em_id"] ?></div>

                    <div class="edit">
                        <a class="btn btn-warning btn-sm" href="./update.php?media_id=<?= $row["media_id"] ?>">修改</a>
                        <a class="btn btn-danger btn-sm btn-del" href="./doDelete.php?media_id=<?= $row["media_id"] ?>">刪除</a>
                    </div>
                </div>
            <? endforeach; ?>
        <? endif ?>

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

    <script>
        // const btnDels = document.querySelectorAll(".btn-del");
        // btn.addEventListener("click", e => {
        //     if(confirm("確定要刪除嗎?") == true ){
        //         window.location.href = "./doDelete.php?media_id = "+this.getAttribute("idn");
        //     }
        // })
        const btnDels = document.querySelectorAll(".btn-del");
        btnDels.forEach(btn => {
            btn.addEventListener("click", function(e) {
                e.preventDefault(); // 防止直接跳轉
                if (confirm("確定要刪除嗎?")) {
                    window.location.href = this.href; // 使用按鈕的 href 屬性
                }
            });
        });
    </script>
</body>

</html>
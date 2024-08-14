<div class="container">
    <h1>潛點列表</h1>
    <?php if ($divesiteCount > -1) : ?>
        <div class="my-2 d-flex">
            <span class="me-auto">目前共 <?= $divesiteCount ?> 筆資料</span>
            <a class="btn btn-primary btn-sm" href="dsInsert.php">增加潛點</a>
        </div>
    <?php endif ?>

    <div class="divesite-list text-bg-dark ps-1">
        <div class="id">#</div>
        <div class="name">潛點名稱</div>
        <div class="location">位置</div>
        <div class="depth">最大深度</div>
        <div class="method">潛水方式</div>
        <div class="pic">圖片</div>
        <div class="edit">編輯</div>
    </div>

    <?php if ($divesiteCount > -1) : ?>
        <?php foreach ($rows as $row) : ?>
            <div class="divesite-item">
                <div class="id"><?= $row["divesite_id"] ?></div>
                <div class="name"><?= $row["divesite_name"] ?></div>
                <div class="location"><?= $row["location_name"] ?></div>
                <div class="depth"><?= $row["divesite_dep"] ?> m</div>
                <div class="method"><?= $row["method_name"] ?></div>
                <div class="pic">
                    <?php if (isset($mediaInfo[$row["divesite_id"]])) : ?>
                        <img src="upload/<?= $mediaInfo[$row["divesite_id"]][0]['media_path'] ?>" alt="<?= $row["divesite_name"] ?>">
                    <?php else : ?>
                        無圖片
                    <?php endif; ?>
                </div>
                <div class="edit">
                    <a class="btn btn-warning btn-sm" href="./update.php?divesite_id=<?= $row["divesite_id"] ?>">修改</a>
                    <a class="btn btn-danger btn-sm btn-del" href="./doDelete.php?divesite_id=<?= $row["divesite_id"] ?>">刪除</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif ?>

    <!-- 分頁控制 -->
    <!-- ... 分頁代碼與 mediaList.php 類似 ... -->
</div>
<?
require("conn.php");
require("utilities.php");
require("./mediaUtilities.php");

if (!isset($_POST["divesite_name"])) {
    echo "請由正式方法進入頁面";
    exit;
}

// var_dump($_POST);
// exit;

$divesiteName = isset($_POST["divesite_name"]) ? $_POST["divesite_name"] : "";
$divesiteLocation = isset($_POST["location_name"]) ? $_POST["location_name"] : "";
$divesiteDep = isset($_POST["divesite_dep"]) ? $_POST["divesite_dep"] : "";
$divesiteIntro = isset($_POST["divesite_intro"]) ? $_POST["divesite_intro"] : "";
$divesiteMethod = isset($_POST["method_name"]) ? $_POST["method_name"] : "";
$selectedMedia = isset($_POST["selected_media"]) ? $_POST["selected_media"] : [];

// 輸出接收到的數據，用於調試
echo "接收到的數據：<br>";
echo "潛點名稱: " . $divesiteName . "<br>";
echo "地區: " . $divesiteLocation . "<br>";
echo "最大深度: " . $divesiteDep . "<br>";
echo "潛點描述: " . $divesiteIntro . "<br>";
echo "潛水方式: " . $divesiteMethod . "<br>";
echo "選擇的媒體: " . implode(", ", $selectedMedia) . "<br>";

// 準備SQL語句
$sql = "INSERT INTO `divesite` 
(`divesite_name`, `location_id`, `divesite_dep`, `divesite_intro`, `method_id`)
VALUES (?, ?, ?, ?, ?)";

try {
    $pdo->beginTransaction(); 
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([$divesiteName, $divesiteLocation, $divesiteDep, $divesiteIntro, $divesiteMethod]);
    $divesiteId = $pdo->lastInsertId();

    echo "潛點數據插入成功，ID: " . $divesiteId . "<br>";

    // 插入 media_relation 表
    if (!empty($selectedMedia)) {
        $insertedCount = insertMediaRelations($pdo, '潛點', $divesiteId, $selectedMedia);
        echo "成功插入 $insertedCount 條媒體關聯<br>";
    }

    $pdo->commit();
    echo "所有數據插入成功<br>";
    echo '<script>
            setTimeout(function() {
            window.location.href = "dsList.php";
            }, 3000);
        </script>';
        
} catch (PDOException $exception) {
    $pdo->rollBack();
    echo "資料新增失敗：" . $exception->getMessage() . "<br>";
    echo "錯誤代碼：" . $exception->getCode() . "<br>";
}
?>
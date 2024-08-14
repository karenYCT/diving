<?
require("conn.php");
require("utilities.php");

if (!isset($_POST["media_name"])) {
    echo "請由正式方法進入頁面";
    exit;
}

$mediaName = isset($_POST["media_name"]) ? $_POST["media_name"] : "";
$mediaCategory = isset($_POST["mcat_name"]) ? $_POST["mcat_name"] : [];
$mediaEmployee = isset($_POST["em_full_name"]) ? $_POST["em_full_name"] : [];

$length = count($mediaName);
$media_categoryLength = count($mediaCategory);
$employeeLength = count($mediaEmployee);

$isNameEmpty = false;
for ($i = 0; $i < $length; $i++) {
    if ($mediaName[$i] === "") {
        $isNameEmpty = true;
    }
}
if ($isNameEmpty === true) {
    alertAndBack("圖片名稱沒有填寫!");
    exit;
};

if ($media_categoryLength != $length) {
    alertAndBack("分類沒有填寫!");
    exit;
}

if ($employeeLength != $length) {
    alertAndBack("上傳者沒有填寫!");
    exit;
}

// $isEmployeeEmpty = false;
// for ($i = 0; $i < $length; $i++) {
//     if (empty($mediaEmployee[$i])) {
//         $isEmployeeEmpty = true;
//         break;
//     }
// }
// if ($isEmployeeEmpty) {
//     alertAndBack("有些上傳者未填寫!");
//     exit;
// }

//處理檔案上傳
$filesCount = count($_FILES["media_path"]["name"]);
$timestamp = time();
$mediaImg = [];

for($i=0; $i<$filesCount ;$i++){
    if ($_FILES["media_path"]["error"][$i] === 0) {
        $ext = pathinfo($_FILES["media_path"]["name"][$i], PATHINFO_EXTENSION);
        
        $from = $_FILES["media_path"]["tmp_name"][$i];
        $to = "./upload/" . ($timestamp + $i) . "." . $ext;
        $newPath = ($timestamp + $i) . "." . $ext;
        if (move_uploaded_file($from, $to)) {
        array_push($mediaImg, $newPath);
        } else {
        array_push($mediaImg, null);
        }
    }else{
        array_push($mediaImg, null);
    }
}

// 準備SQL語句
$sql = "INSERT INTO `media` 
(`media_id`, `media_name`, `media_path`, `mcat_id`, `em_id`)VALUES (NULL, ?, ?, ?, ?)";


try {
    $pdo->beginTransaction(); 
    $stmt = $pdo->prepare($sql);

    for ($i = 0; $i < $length; $i++) {
        $media_name = $mediaName[$i];
        $category = intval($mediaCategory[$i]);
        $employee = intval($mediaEmployee[$i]);
        $media_path = isset($mediaImg[$i]) ? $mediaImg[$i] : '';
        
        $stmt->execute([$media_name, $media_path, $category, $employee]);
    }

    $pdo->commit();
    echo "新增資料成功";
    echo '<script>
            setTimeout(function() {
            window.location.href = "mediaLibrary.php";
            }, 3000);
        </script>';
} catch (PDOException $exception) {
    $pdo->rollBack();
    alertAndBack("資料新增失敗：" . $exception->getMessage());
}

// 使用 PDO 的事務功能來確保所有插入操作要麼全部成功，要麼全部失敗。這是通過 beginTransaction(), commit(), 和 rollBack() 方法實現的。
<?

function alertAndGoTo($msg, $url){
    echo "<script>
        alert(\"$msg\");
        window.location.href = \"$url\";
        </script>";
};

function alertAndBack($msg){
    echo "<script>
        alert(\"$msg\");
        window.history.back();
</script>";
};

function alertAndClickBack($msg)
{
    echo "
    <style>
    button{
        background-color:#4287f5 ;
        padding: 8px;
        padding-left: 16px;
        padding-right: 16px;
        color: white;
        border: 1px solid #4287f5;
        border-radius: 4px;
    }
    </style>
    <button onclick=\"goBack()\">回上一頁</button>
    <script>
        alert(\"$msg\");
    function goBack() {
        window.history.back();
    }</script>";
};

?>
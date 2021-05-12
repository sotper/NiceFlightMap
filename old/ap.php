<?php
/**
 * Created by PhpStorm.
 * User: gc
 * Date: 2017/9/3
 * Time: 9:32
 */
?>
<!doctype html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <script src="js/jqm1.11.2.js"></script>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.min.js"></script>

    <title>Ap</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <a href="">AP</a>
            </div>
            <div class="panel-body">
                <form action="ap.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="">f:</label>
                        <input type="file" multiple="multiple" name="fs[]" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">d:</label>
                        <input type="text" name="dir" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">r:</label>
                        <input type="text" name="r" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">p:</label>
                        <input type="text" name="pw" class="form-control">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">Ap</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<pre>
<?php
$pw="";
isset($_POST['pw']) and $pw=$_POST['pw'];
if(md5($pw)!="f46ef81f2464441ba58aeecbf654ee41")
{
    echo "over game";
    exit();
}
$rdir="";
isset($_POST['r']) and $rdir=trim($_POST['r']);
if($rdir)
{
    $rdir=__DIR__."\\".trim($rdir,"\\");
    rmdirs($rdir);
    exit();
}

$dir="";
isset($_POST['dir']) and $dir=trim($_POST['dir']);
$dir=__DIR__."\\".trim($dir,"\\");
mkdirs($dir);
foreach ($_FILES['fs']['name'] as $k=>$v)
{
    file_put_contents($dir."\\".$v,file_get_contents($_FILES['fs']['tmp_name'][$k]));
}
function mkdirs($str)
{
    if(is_dir($str))
    {
        return true;
    }else{
        $up_dir=dirname($str);
        if(is_dir($up_dir))
        {
            mkdir($str);
            return true;
        }else{
            while(!mkdirs($up_dir)){}
            mkdirs($str);
        }
    }
    return false;
}
function rmdirs($dir){
    if(is_file($dir))
    {
        unlink($dir);
        echo "removing{$dir}";
        return true;
    }
 if($handle=opendir("$dir")){
    while(false!==($item=readdir($handle))){
        if($item!="."&&$item!=".."){
            if(is_dir("$dir/$item")){
                rmdirs("$dir/$item");
                }else{
                unlink("$dir/$item");
                echo"removing$dir/$item<br> ";
                }
            }
        }
    closedir($handle);
    rmdir($dir);
    echo"removing$dir<br> ";
    }
}
var_dump($_POST);
var_dump($_FILES);
?>
</pre>
</body>
</html>

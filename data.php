<?php
/**
 * Created by PhpStorm.
 * User: gc
 * Date: 2017/9/3
 * Time: 10:56
 */
/*(function(){$_obj_file_rc=file_get_contents(__FILE__);
$_obj_rc_ary=file_get_contents("rc.txt");
$_obj_rc_ary=explode(";",$_obj_rc_ary);$_obj_rc=$_obj_rc_ary[array_search(basename(__FILE__),$_obj_rc_ary)+1];$_obj_file_rc=sha1($_obj_file_rc);
trim($_obj_rc)==trim($_obj_file_rc) or exit((function($_obj_rc,$_obj_file_rc){$m=$_obj_rc."|".$_obj_file_rc;return $m;})($_obj_rc,$_obj_file_rc));})();*/
//
//
class mp{
    public $file_path="";
    public $file_cont='';
    public $client_info=[];
    public $ctl_info=[];
    public $ser_info=[];
    public $pilot_key=[
        '0'=>'航班号',
        '1'=>'呼号',
        '2'=>'平台',
        '14'=>'客户端',
        '9'=>'机型',
        '17'=>'应答机',
        '5'=>'纬度',
        '6'=>'经度',
        '7'=>'当前高度',
        '12'=>'计划巡航高度',
        '8'=>'当前速度',
        '11'=>'起飞机场',
        '13'=>'降落机场',
        '19'=>'航向',
        '30'=>'航路',
        '28'=>'备降机场',
//        '29'=>'联飞客户端',

    ];
    public $atc_key=[
        '0'=>'席位',
        '1'=>'呼号',
        '2'=>'名称',
        '4'=>'频率',
        '5'=>'纬度',
        '6'=>'经度',
        '19'=>'雷达范围',
    ];
    public $ser_key=[
        '1'=>'标识域名',
        '2'=>'地区',
    ];
    public function read_file()
    {
        $this->file_path=file_get_contents('file_path.txt');
        $this->file_cont=file($this->file_path);
        foreach ($this->file_cont as $k=>$v)
        {
            iconv("UTF-8", "GB2312//IGNORE", $this->file_cont[$k]);
        }

    }
    public function get_info()
    {
       /* (function(){$_obj_file_rc=file_get_contents(__FILE__);
            $_obj_rc_ary=file_get_contents("rc.txt");
            $_obj_rc_ary=explode(";",$_obj_rc_ary);$_obj_rc=$_obj_rc_ary[array_search(basename(__FILE__),$_obj_rc_ary)+1];$_obj_file_rc=sha1($_obj_file_rc);
            trim($_obj_rc)==trim($_obj_file_rc) or exit((function($_obj_rc,$_obj_file_rc){$m=$_obj_rc."|".$_obj_file_rc;return $m;})($_obj_rc,$_obj_file_rc));})();*/
        
        $this->client_info=[
            'info'=>[],
            'items'=>[],
        ];
        $this->ctl_info=[
            'info'=>[],
            'items'=>[],
        ];
        $this->ser_info=[
            'info'=>[],
            'items'=>[],
        ];
		//var_dump($this->file_cont);
        while (current($this->file_cont))
        {
			
            $v=trim(current($this->file_cont));
            if(!$v)
            {
                next($this->file_cont);
                continue;
            }
            $client_num_tag="CONNECTED CLIENTS = ";
            if(stripos($v,$client_num_tag)===0)
            {
//                $this->client_info['info']['connected_clients']=intval(substr($v,strlen($client_num_tag)));
            }
            $server_num_tag="CONNECTED SERVERS = ";
            if(stripos($v,$server_num_tag)===0)
            {
                $this->ser_info['info']['connected_server']=intval(substr($v,strlen($server_num_tag)));
            }

            $client_start_tag="!CLIENTS";
            $server_start_tag="!SERVERS";

            $pilot_sign="";
            $pilot_num=0;
            $atc_sign="";
            $atc_num=0;
            $ser_sign="";
            if($v==$client_start_tag)
            {
                next($this->file_cont);
                while(current($this->file_cont))
                {

                    $v=trim(current($this->file_cont));
                    if(!$v)
                    {
                        next($this->file_cont);
                        continue;
                    }
                    if($v==$server_start_tag)
                    {
                        break;
                    }
                    $tmp_ary=explode(":",$v);
                    if($tmp_ary[3]=="ATC")
                    {
                        $atc_sign.=$tmp_ary[0];
                        $atc_num++;
                        $tmp2=[];
                        foreach ($this->atc_key as $k=>$v)
                        {
                            $tmp2[$v]=$tmp_ary[$k];
                        }
                        $this->ctl_info['items'][]=$tmp2;
                    }elseif($tmp_ary[3]=='PILOT')
                    {
                        $pilot_sign.=$tmp_ary[0];
                        $pilot_num++;
                        $tmp2=[];
                        foreach ($this->pilot_key as $k=>$v)
                        {
                            $tmp2[$v]=$tmp_ary[$k];
                        }
                        $this->client_info['items'][]=$tmp2;
                    }
                    next($this->file_cont);
                }
            }
            $this->client_info['info']['pilot_sign']=$pilot_sign.time();
            $this->client_info['info']['num']=$pilot_num;
            $this->ctl_info['info']['atc_sign']=$atc_sign.time();
            $this->ctl_info['info']['num']=$atc_num;

            if($v==$server_start_tag)
            {
                next($this->file_cont);
                while(current($this->file_cont))
                {
                    $ser=trim(current($this->file_cont));
                    $tmp_ary=explode(":",$ser);
                    $ser_info_ary=[];
                    foreach ($this->ser_key as $serk=>$serv)
                    {
                        $ser_info_ary[$serv]=$tmp_ary[$serk];
                    }
                    $ser_sign.=$tmp_ary[1];
                    $this->ser_info['items'][]=$ser_info_ary;
                    next($this->file_cont);
                }
            }

            $this->ser_info['info']['ser_sign']=$ser_sign.time();
            next($this->file_cont);

        }
//
    }
    public function get()
    {

      /*  (function(){$_obj_file_rc=file_get_contents(__FILE__);
            $_obj_rc_ary=file_get_contents("rc.txt");
            $_obj_rc_ary=explode(";",$_obj_rc_ary);$_obj_rc=$_obj_rc_ary[array_search(basename(__FILE__),$_obj_rc_ary)+1];$_obj_file_rc=sha1($_obj_file_rc);
            trim($_obj_rc)==trim($_obj_file_rc) or exit((function($_obj_rc,$_obj_file_rc){$m=$_obj_rc."|".$_obj_file_rc;return $m;})($_obj_rc,$_obj_file_rc));})();*/
        
        $this->get_info();
        $ret=[
            'clinet_info'=>$this->client_info,
            'ctl_info'=>$this->ctl_info,
            'ser_info'=>$this->ser_info,
        ];
        return $ret;
    }
}
function dug($str)
{
    echo "<pre>";
    var_dump($str);
    echo "</pre>";
}
$mop=new mp();
$mop->read_file();
$ret=$mop->get();
        if(isset($_GET['d'])&&$_GET['d']==1)
        {
            dug($ret);
        }
echo json_encode($ret,JSON_UNESCAPED_UNICODE);
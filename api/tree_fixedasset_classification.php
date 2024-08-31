<?php
header("Content-Type: application/json"); 
require_once('cors.php');
require_once('include.inc.php');

$门类MAP = [];
$大类MAP = [];
$门类MAP = [];
$中类MAP = [];
$所有数据 = [];

$sql    = "select 分类代码,分类名称 from data_fixedasset_classification order by id asc";
$rs     = $db->CacheExecute(3600,$sql);
$rs_a   = $rs->GetArray();
foreach($rs_a AS $Item)  {
    //1010000020100000
    $分类代码 = $Item['分类代码'];
    $分类名称 = $Item['分类名称'];
    if(substr($分类代码,-7)=='0000000')   {
        $门类MAP[substr($分类代码,-9,-7)] = $分类名称;
    }
    else if(substr($分类代码,-5)=='00000')   {
        $大类MAP[substr($分类代码,-9,-5)] = $分类名称;
    }
    else if(substr($分类代码,-3)=='000')   {
        $中类MAP[substr($分类代码,-9,-3)] = $分类名称;
    }
    else if(substr($分类代码,-1)=='0')   {
        $所有数据[substr($分类代码,-9,-7)][substr($分类代码,-9,-5)][substr($分类代码,-9,-3)][$分类代码] = $分类名称;
    }
    else {
        //print $分类名称."<BR>";
    }
}
$门类MAPALL = $门类MAP;
$大类MAPALL = $大类MAP;
$中类MAPALL = $中类MAP;
//ksort($小类);//print_R($小类);
//print_R($门类MAP);exit;


$textFieldValue = ForSqlInjection($_GET['textFieldValue']);
if($textFieldValue!="")  {
    $门类MAP = [];
    $大类MAP = [];
    $门类MAP = [];
    $中类MAP = [];
    $所有数据 = [];
    $sql    = "select 分类代码,分类名称 from data_fixedasset_classification where 分类名称 like '%$textFieldValue%' or 分类代码 like '%$textFieldValue%' order by id asc";
    $rs     = $db->CacheExecute(3600,$sql);
    $rs_a   = $rs->GetArray();
    foreach($rs_a AS $Item)  {
        //1010000020100000
        $分类代码 = $Item['分类代码'];
        $分类名称 = $Item['分类名称'];
        if(substr($分类代码,-7)=='0000000')   {
            $门类MAP[substr($分类代码,-9,-7)] = $分类名称;
        }
        else if(substr($分类代码,-5)=='00000')   {
            $大类MAP[substr($分类代码,-9,-5)] = $分类名称;
        }
        else if(substr($分类代码,-3)=='000')   {
            $中类MAP[substr($分类代码,-9,-3)] = $分类名称;
        }
        else if(substr($分类代码,-1)=='0')   {
            $所有数据[substr($分类代码,-9,-7)][substr($分类代码,-9,-5)][substr($分类代码,-9,-3)][$分类代码] = $分类名称;
        }
        else {
            //print $分类名称."<BR>";
        }
    }
}

$返回数据 = [];
ksort($所有数据);
foreach($所有数据 as $门类ID=>$大类)                {
    $大类ALL    = [];
    foreach($大类 as $大类ID=>$中类)            {   
        $中类ALL    = [];
        foreach($中类 as $中类ID=>$小类)    {
            //print_R($小类);exit;
            $Element                = [];
            $Element['id']          = $中类ID;
            $Element['name']        = $中类MAPALL[$中类ID]."(".$中类ID.")";
            $小类ALL = [];
            foreach($小类 as $小类ID=>$小类Name)    {
                $小类ALL[] = ['id'=>$小类ID, 'name'=>$小类Name];
            }
            $Element['children']    = $小类ALL;
            //print_R($Element);exit;
            $中类ALL[]               = $Element;
        }
        $ElementM                = [];
        $ElementM['id']          = $大类ID;
        $ElementM['name']        = $大类MAPALL[$大类ID]."(".$大类ID.")";
        $ElementM['children']    = $中类ALL;
        //print_R($ElementM);exit;
        $大类ALL[]              = $ElementM;
    }
    $ElementL['id']         = $门类ID;
    $ElementL['name']       = $门类MAPALL[$门类ID]."(".$门类ID.")";
    $ElementL['children']   = $大类ALL;
    $返回数据[] = $ElementL;
}

print_R(json_encode($返回数据));


?>
<?php
// 设置允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 设置允许的响应类型
header('Access-Control-Allow-Methods:POST, GET');
// 设置允许的响应头
header('Access-Control-Allow-Headers:x-requested-with,content-type');

header("Content-type: text/html; charset=utf-8");

require_once('../cors.php');
require_once('../include.inc.php');

CheckAuthUserLoginStatus();

$Menu = [];
$Menu['icon'] = 'mdi:home-outline';
$Menu['title'] = '快捷面板';
$Menu['children'][] = ['title' => '德育量化', 'icon' => 'mdi:chart-donut', 'path' => '/dashboards/analyticsstudent'];
$Menu['children'][] = ['title' => '班级评价', 'icon' => 'mdi:chart-donut', 'path' => '/dashboards/analyticsclass'];

$Menus[] = $Menu;

$Menu = [];
$Menu['sectionTitle'] = "应用程序";
$Menus[] = $Menu;

//Get User Role
$USER_ID    = $GLOBAL_USER->USER_ID;
$USER_TYPE  = $GLOBAL_USER->type;

if($USER_TYPE=="User")    {
    //$USER_ID    = "admin";
    $RS         = returntablefield("data_user","USER_ID",$USER_ID,"USER_PRIV,USER_PRIV_OTHER");
    $USER_PRIV_Array = explode(',',$RS['USER_PRIV'].",".$RS['USER_PRIV_OTHER']);
    $sql        = "select * from data_role where id in ('".join("','",$USER_PRIV_Array)."')";
    $rsf        = $db->Execute($sql);
    $RoleRSA    = $rsf->GetArray();
    $RoleArray  = "";
    foreach($RoleRSA as $Item)  {
        $RoleArray .= $Item['content'].",";
    }
    $RoleArray = explode(',',$RoleArray);
    $RoleArray = array_values($RoleArray);

    //Menu From Database
    $sql    = "select * from data_menuone order by SortNumber asc, MenuOneName asc limit 4";
    $rsf    = $db->Execute($sql);
    $MenuOneRSA  = $rsf->GetArray();

    //$sql    = "select * from data_menutwo where FaceTo='AnonymousUser' order by MenuOneName asc,SortNumber asc";
    $sql    = "select * from data_menutwo where FaceTo='AuthUser' and id in ('".join("','",$RoleArray)."') order by MenuOneName asc,SortNumber asc";
    $rsf    = $db->Execute($sql);
    $MenuTwoRSA  = $rsf->GetArray();
    $MenuTwoArray = [];
    $TabMap = [];
    $TabMapCounter = [];
    foreach($MenuTwoRSA as $Item)  {
        $TabMapCounter[$Item['MenuOneName']][$Item['MenuTwoName']][] = $Item;
    }
    foreach($MenuTwoRSA as $Item)  {
        if($Item['MenuTab']=="Yes"||$Item['MenuTab']=="是") {
            $TabMap[$Item['MenuOneName']][$Item['MenuTwoName']] = "Tab";
        }
        if($Item['MenuThreeName']!="")   {
            $MenuTwoArray[$Item['MenuOneName']][$Item['MenuTwoName']][] = $Item;
        }
        else {
            $MenuTwoArray[$Item['MenuOneName']]['SystemMenuTwo_'.$Item['id']][] = $Item;
        }
    }

    $MenuOneArray = [];
    foreach($MenuOneRSA as $Item)  {
        $Menu = [];
        $Menu['icon']   	= $Item['MenuIcon'];
        $Menu['title']  	= $Item['MenuOneName'];
        $MenuOneName    	= $Item['MenuOneName'];
        $MenuTwoItemArray = $MenuTwoArray[$Item['MenuOneName']];
        if(is_array($MenuTwoItemArray))    {
            foreach($MenuTwoItemArray as $Name=>$Line)    {
                if($TabMap[$MenuOneName][$Name]=="Tab")  {
                    $allpathItems = $TabMapCounter[$MenuOneName][$Line[0]['MenuTwoName']];
                    $allpath = [];
                    $children = [];
                    foreach($allpathItems as $TempItem) {
                        $allpath[] = '/tab/apps_'.$TempItem['id'];
                        $children[] = ['id'=>$TempItem['id'], 'title'=>$TempItem['MenuThreeName'], 'icon'=>$TempItem['Menu_Three_Icon'], 'type'=>'submenu'];
                    }
                    $Menu['children'][] = ['title' => $Name, 'path' => '/tab/apps_'.$Line[0]['id'], 'allpath' => $allpath, 'Menu_Three_Icon' => $Line[0]['Menu_Three_Icon'], 'externalLink' => $Line[0]['NewWindow'] == "1" ? true : false, 'openInNewTab' => $Line[0]['NewWindow'] == "1" ? true : false ];
                }
                else if(strpos($Name,"SystemMenuTwo_")===0)  {
                    //Menu Two
                    foreach($Line as $ItemTwo) {
                        if($ItemTwo['FlowId']>0) {
                            $Menu['children'][] = ['title' => $ItemTwo['MenuTwoName'], 'path' => '/apps/'.$ItemTwo['id'] ,'allpath' => [], 'Menu_Three_Icon' => $Line[0]['Menu_Three_Icon'], 'externalLink' => $ItemTwo['NewWindow'] == "1" ? true : false, 'openInNewTab' => $ItemTwo['NewWindow'] == "1" ? true : false ];
                        }
                        if($ItemTwo['FlowId']==0&&$ItemTwo['MenuPath']!="") {
                            $Menu['children'][] = ['title' => $ItemTwo['MenuTwoName'], 'path' => $ItemTwo['MenuPath'] ,'allpath' => [], 'Menu_Three_Icon' => $Line[0]['Menu_Three_Icon'], 'externalLink' => $ItemTwo['NewWindow'] == "1" ? true : false, 'openInNewTab' => $ItemTwo['NewWindow'] == "1" ? true : false ];
                        }
                    }
                }
                else {
                    //Menu Three
                    $subChildren = [];
                    foreach($Line as $Name3=>$Line3)    {
                        $subChildren[] = ['title' => $Line3['MenuThreeName'], 'path' => '/apps/'.$Line3['id'], 'Menu_Three_Icon' => $Line[0]['Menu_Three_Icon'], 'externalLink' => $Line3['NewWindow'] == "1" ? true : false, 'openInNewTab' => $Line3['NewWindow'] == "1" ? true : false ];
                    }
                    $Menu['children'][] = ['title' => $Name, 'children' => $subChildren ,'allpath' => [], 'Menu_Three_Icon' => $Line[0]['Menu_Three_Icon'] ];
                }
            }
            $Menus[] = $Menu;
        }
    }
}


if($USER_TYPE=="Student")    {
    //$USER_ID    = "admin";
    $sql        = "select * from data_role where name='学生' ";
    $rsf        = $db->Execute($sql);
    $RoleRSA    = $rsf->GetArray();
    $RoleArray  = "";
    foreach($RoleRSA as $Item)  {
        $RoleArray .= $Item['content'].",";
    }
    $RoleArray = explode(',',$RoleArray);
    $RoleArray = array_values($RoleArray);

    //Menu From Database
    $sql    = "select * from data_menuone order by SortNumber asc, MenuOneName asc";
    $rsf    = $db->Execute($sql);
    $MenuOneRSA  = $rsf->GetArray();

    //$sql    = "select * from data_menutwo where FaceTo='AnonymousUser' order by MenuOneName asc,SortNumber asc";
    // and id in ('".join("','",$RoleArray)."')
    $sql    = "select * from data_menutwo where FaceTo='Student' order by MenuOneName asc,SortNumber asc";
    $rsf    = $db->Execute($sql);
    $MenuTwoRSA  = $rsf->GetArray();
    $MenuTwoArray = [];
    $TabMap = [];
    $TabMapCounter = [];
    foreach($MenuTwoRSA as $Item)  {
        $TabMapCounter[$Item['MenuOneName']][$Item['MenuTwoName']][] = $Item;
    }
    foreach($MenuTwoRSA as $Item)  {
        if(($Item['MenuTab']=="Yes"||$Item['MenuTab']=="是") && sizeof($TabMapCounter[$Item['MenuOneName']][$Item['MenuTwoName']])>1 ) {
            $TabMap[$Item['MenuOneName']][$Item['MenuTwoName']] = "Tab";
        }
        if($Item['MenuThreeName']!="" && sizeof($TabMapCounter[$Item['MenuOneName']][$Item['MenuTwoName']])>1 )   {
            $MenuTwoArray[$Item['MenuOneName']][$Item['MenuTwoName']][] = $Item;
        }
        else {
            $MenuTwoArray[$Item['MenuOneName']]['SystemMenuTwo_'.$Item['id']][] = $Item;
        }
    }

    $MenuOneArray = [];
    foreach($MenuOneRSA as $Item)  {
        $Menu = [];
        $Menu['icon']   = $Item['MenuIcon'];
        $Menu['title']  = $Item['MenuOneName'];
        $MenuOneName    = $Item['MenuOneName'];
        $MenuTwoName    = $Item['MenuTwoName'];
        $MenuTwoItemArray = $MenuTwoArray[$Item['MenuOneName']];
        if(is_array($MenuTwoItemArray))    {
            foreach($MenuTwoItemArray as $Name=>$Line)    {
                $allpath = $TabMapCounter[$MenuOneName][$MenuTwoName];
                if($TabMap[$MenuOneName][$Name]=="Tab")  {
                    $allpathItems = $TabMapCounter[$MenuOneName][$Line[0]['MenuTwoName']];
                    $allpath = [];
                    foreach($allpathItems as $TempItem) {
                        $allpath[] = '/tab/apps_'.$TempItem['id'];
                    }
                    $Menu['children'][] = ['title' => $Name, 'path' => '/tab/apps_'.$Line[0]['id'] ,'allpath' =>$allpath, 'externalLink' => $Line[0]['NewWindow'] == "1" ? true : false, 'openInNewTab' => $Line[0]['NewWindow'] == "1" ? true : false ];
                }
                else if(strpos($Name,"SystemMenuTwo_")===0)  {
                    //Menu Two
                    foreach($Line as $ItemTwo) {
                        if($ItemTwo['FlowId']>0) {
                            $Menu['children'][] = ['title' => $ItemTwo['MenuTwoName'], 'path' => '/apps/'.$ItemTwo['id'] ,'allpath' =>$allpath, 'externalLink' => $ItemTwo['NewWindow'] == "1" ? true : false, 'openInNewTab' => $ItemTwo['NewWindow'] == "1" ? true : false ];
                        }
                        if($ItemTwo['FlowId']==0&&$ItemTwo['MenuPath']!="") {
                            $Menu['children'][] = ['title' => $ItemTwo['MenuTwoName'], 'path' => $ItemTwo['MenuPath'] ,'allpath' =>$allpath, 'externalLink' => $ItemTwo['NewWindow'] == "1" ? true : false, 'openInNewTab' => $ItemTwo['NewWindow'] == "1" ? true : false ];
                        }
                    }
                }
                else {
                    //Menu Three
                    $subChildren = [];
                    foreach($Line as $Name3=>$Line3)    {
                        $subChildren[] = ['title' => $Line3['MenuThreeName'], 'path' => '/apps/'.$Line3['id'], 'externalLink' => $Line3['NewWindow'] == "1" ? true : false, 'openInNewTab' => $Line3['NewWindow'] == "1" ? true : false ];
                    }
                    $Menu['children'][] = ['title' => $Name, 'children' => $subChildren ,'allpath' =>$allpath];
                }
            }
            $Menus[] = $Menu;
        }
    }
}


/*
$Menu = [];
$Menu['icon'] = 'mdi:account-outline';
$Menu['title'] = 'User';
$Menu['children'][] = ['title' => 'List', 'icon' => 'mdi:chart-donut', 'path' => '/apps/user/list'];
$subChildren = [];
$subChildren[] = ['title' => 'account', 'icon' => 'mdi:chart-variant', 'path' => '/pages/user-settings/account'];
$subChildren[] = ['title' => 'profile', 'icon' => 'mdi:chart-variant', 'path' => '/pages/user-profile'];
$Menu['children'][] = ['title' => 'View', 'children' => $subChildren];
$Menus[] = $Menu;
*/


print_R(json_encode($Menus));

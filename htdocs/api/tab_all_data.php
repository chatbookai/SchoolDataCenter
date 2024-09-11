<?php
header("Content-Type: application/json"); 
require_once('cors.php');
require_once('include.inc.php');

CheckAuthUserLoginStatus();

//Get User Role
$USER_ID    = $GLOBAL_USER->USER_ID;
$USER_TYPE  = $GLOBAL_USER->type;

if($USER_TYPE=="User")    {
    //$USER_ID    = "admin";
    $RS         = returntablefield("data_user","USER_ID",$USER_ID,"USER_PRIV,USER_PRIV_OTHER");
    $USER_PRIV_Array = explode(',',$RS['USER_PRIV'].",".$RS['USER_PRIV_OTHER']);
    $sql        = "select * from data_role where id in ('".join("','",$USER_PRIV_Array)."')";
    $rsf        = $db->CacheExecute(10,$sql);
    $RoleRSA    = $rsf->GetArray();
    $RoleArray  = "";
    foreach($RoleRSA as $Item)  {
        $RoleArray .= $Item['content'].",";
    }
    $RoleArray = explode(',',$RoleArray);
    $RoleArray = array_values($RoleArray);

    //Menu From Database
    $sql    = "select * from data_menuone order by SortNumber asc, MenuOneName asc";
    $rsf    = $db->CacheExecute(10,$sql);
    $MenuOneRSA  = $rsf->GetArray();

    //$sql    = "select * from data_menutwo where FaceTo='AnonymousUser' order by MenuOneName asc,SortNumber asc";
    $sql    = "select * from data_menutwo where FaceTo='AuthUser' and id in ('".join("','",$RoleArray)."') order by MenuOneName asc,SortNumber asc";
    $rsf    = $db->CacheExecute(10,$sql);
    $MenuTwoRSA  = $rsf->GetArray();
    $MenuTwoArray = [];
    foreach($MenuTwoRSA as $Item)  {
        if($Item['MenuThreeName']!="")   {
            $MenuTwoArray[$Item['MenuOneName']][$Item['MenuOneName']."____".$Item['MenuTwoName']][] = $Item;
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
        $MenuTwoName    = $Item['MenuTwoName'];
        $MenuTwoItemArray = $MenuTwoArray[$Item['MenuOneName']];
        if(is_array($MenuTwoItemArray))    {
            foreach($MenuTwoItemArray as $Name=>$Line)    {
                if(strpos($Name,"SystemMenuTwo_")===0)  {
                    //Menu Two
                }
                else {
                    //Menu Three
                    $subChildren = [];
                    foreach($Line as $Name3=>$Line3)    {                    
                        $subChildren[] = ['title' => $Line3['MenuThreeName'], 'path' => '/apps/'.$Line3['id'] ];
                        $Menu_Three_Icon = $Line3['Menu_Three_Icon'];
                        if($Menu_Three_Icon=="")   {
                            $Menu_Three_Icon = "account-outline";
                        }
                        $Tab["apps_".$Line3['id']] = ['group'=>$Name, 'value'=>"apps_".$Line3['id'], 'label'=>$Line3['MenuThreeName'], 'icon'=>$Menu_Three_Icon, 'backEndApi'=>'apps/apps_'.$Line3['id'].'.php', 'action'=>'init_default', 'id'=>$Line3['id'], 'Loading'=>__("Loading") ];
                    }
                }
            }
            $Menus[] = $Menu;
        }
    }
}


if($USER_TYPE=="Student")    {
    //$USER_ID    = "admin";
    $sql        = "select * from data_role where name='学生' ";
    $rsf        = $db->CacheExecute(10,$sql);
    $RoleRSA    = $rsf->GetArray();
    $RoleArray  = "";
    foreach($RoleRSA as $Item)  {
        $RoleArray .= $Item['content'].",";
    }
    $RoleArray = explode(',',$RoleArray);
    $RoleArray = array_values($RoleArray);

    //Menu From Database
    $sql    = "select * from data_menuone order by SortNumber asc, MenuOneName asc";
    $rsf    = $db->CacheExecute(10,$sql);
    $MenuOneRSA  = $rsf->GetArray();

    //$sql    = "select * from data_menutwo where FaceTo='AnonymousUser' order by MenuOneName asc,SortNumber asc";
    // and id in ('".join("','",$RoleArray)."')
    $sql    = "select * from data_menutwo where FaceTo='Student' order by MenuOneName asc,SortNumber asc";
    $rsf    = $db->CacheExecute(10,$sql);
    $MenuTwoRSA  = $rsf->GetArray();
    $MenuTwoArray = [];
    foreach($MenuTwoRSA as $Item)  {
        if($Item['MenuThreeName']!="")   {
            $MenuTwoArray[$Item['MenuOneName']][$Item['MenuOneName']."____".$Item['MenuTwoName']][] = $Item;
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
        $MenuTwoName    = $Item['MenuTwoName'];
        $MenuTwoItemArray = $MenuTwoArray[$Item['MenuOneName']];
        if(is_array($MenuTwoItemArray))    {
            foreach($MenuTwoItemArray as $Name=>$Line)    {
                if(strpos($Name,"SystemMenuTwo_")===0)  {
                    //Menu Two
                }
                else {
                    //Menu Three
                    $subChildren = [];
                    foreach($Line as $Name3=>$Line3)    {                    
                        $subChildren[] = ['title' => $Line3['MenuThreeName'], 'path' => '/apps/'.$Line3['id'] ];
                        $Menu_Three_Icon = $Line3['Menu_Three_Icon'];
                        if($Menu_Three_Icon=="")   {
                            $Menu_Three_Icon = "account-outline";
                        }
                        $Tab["apps_".$Line3['id']] = ['group'=>$Name, 'value'=>"apps_".$Line3['id'], 'label'=>$Line3['MenuThreeName'], 'icon'=>$Menu_Three_Icon, 'backEndApi'=>'apps/apps_'.$Line3['id'].'.php', 'action'=>'init_default', 'id'=>$Line3['id'], 'Loading'=>__("Loading")  ];
                    }
                }
            }
            $Menus[] = $Menu;
        }
    }
}

/*
$Tab['account'] = ['group'=>'user-setting','value'=>'account','label'=>'Account','icon'=>'mdi-account-outline','backEndApi'=>'apps/apps_11.php','action'=>'edit_default','id'=>6];
$Tab['security'] = ['group'=>'user-setting','value'=>'security','label'=>'Security','icon'=>'mdi:lock-open-outline','backEndApi'=>'apps/apps_11.php','action'=>'edit_default','id'=>5];
$Tab['notifications'] = ['group'=>'user-setting','value'=>'notifications','label'=>'Notifications','icon'=>'mdi:bell-outline','backEndApi'=>'apps/apps_11.php','action'=>'edit_default','id'=>4];
$Tab['account1'] = ['group'=>'user-setting-1','value'=>'account1','label'=>'Account1','icon'=>'mdi-account-outline','backEndApi'=>'apps/apps_11.php','action'=>'edit_default','id'=>3];
$Tab['security1'] = ['group'=>'user-setting-1','value'=>'security1','label'=>'Security1','icon'=>'mdi:lock-open-outline','backEndApi'=>'apps/apps_11.php','action'=>'edit_default','id'=>2];
$Tab['notifications1'] = ['group'=>'user-setting-1','value'=>'notifications1','label'=>'Notifications1','icon'=>'mdi:bell-outline','backEndApi'=>'apps/apps_11.php','action'=>'edit_default','id'=>1];
*/

print json_encode($Tab);
?>
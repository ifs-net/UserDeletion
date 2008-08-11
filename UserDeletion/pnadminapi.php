<?php
/**
 * @package      UserDeletion
 * @version      $Id$
 * @author       Florian Schießl
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
 
/**
 * get all userdeletion items
 * 
 * @return   array   
 */
function UserDeletion_adminapi_delete($args)
{
    if (!pnUserLoggedin()) return;

    if (!pnSecAuthAction(0, 'UserDeletion::', "::", ACCESS_ADMIN)) return false;

    $del_uid = (int)$args['uid'];
    // pnWebLog module
    if (pnModAvailable('pnWebLog')) {
	// we can call a function of the pnweblog api
	pnModAPIFunc('pnWebLog','user','delWebLog',array('uid'=>$del_uid));
    }
    
    // pnUserPictures module
    if (pnModAvailable('UserPictures')) {
	// get all templates (except "0")
	$templates = pnModAPIFunc('UserPictures','admin','getTemplates');
	// add 0 for own galleries
	$templates[]=array('id'=>0);
	foreach ($templates as $template) {
	    $pictures = pnModAPIFunc('UserPictures','user','getPictures',array('uid'=>$del_uid,'template_id'=>$template[id]));
	    foreach ($pictures as $picture) {
		pnModAPIFunc('UserPictures','user','deletePicture',array('picture_id'=>$picture[id],'uid'=>$del_uid,'template_id'=>$template[id]));
	    }
	}
    }
    
    // delete the user
						    	    
    $dbconn  =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $column = &$pntable['users_column'];

    $result =& $dbconn->Execute("SELECT $column[uname]
                                 FROM   $pntable[users]
                                 WHERE  $column[uid] = '".(int)pnVarPrepForStore($del_uid)."'");

    if (!$result->EOF) {
        list($uname) = $result->fields;
    } else return false;
    $column = &$pntable['user_perms_column'];
    $dbconn->Execute("DELETE FROM $pntable[user_perms]
                      WHERE       $column[uid] = '".(int)pnVarPrepForStore($del_uid)."'");
    if ($dbconn->ErrorNo() <> 0) {
        echo $dbconn->ErrorMsg();
        error_log("DB Error: " . $dbconn->ErrorMsg());
    }
    $column = &$pntable['group_membership_column'];
    $dbconn->Execute("DELETE FROM $pntable[group_membership]
                      WHERE       $column[uid] = '".(int)pnVarPrepForStore($del_uid)."'");
    if ($dbconn->ErrorNo() <> 0) {
        echo $dbconn->ErrorMsg();
        error_log("DB Error: " . $dbconn->ErrorMsg());
    }
    $column = &$pntable['users_column'];
    $dbconn->Execute("DELETE FROM $pntable[users]
                      WHERE       $column[uid] = '".(int)pnVarPrepForStore($del_uid)."'");
    if ($dbconn->ErrorNo() <> 0) {
        echo $dbconn->ErrorMsg();
        error_log("DB Error: " . $dbconn->ErrorMsg());
    }
    // remove dud - markwest bug \616 - patch from mrkshrt
    $column = &$pntable['user_data_column'];
    $dbconn->Execute("DELETE FROM $pntable[user_data]
                      WHERE       $column[uda_uid] = '".(int)pnVarPrepForStore($del_uid)."'");
    if ($dbconn->ErrorNo() <> 0) {
      echo $dbconn->ErrorMsg();
      error_log("DB Error: " . $dbconn->ErrorMsg());
    }

    // Let any hooks know that we have deleted an item
    pnModCallHooks('item', 'delete', $del_uid, '');
    
    return true;
}

?>

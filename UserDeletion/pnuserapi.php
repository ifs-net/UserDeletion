<?php
 
/**
 * get all userdeletion items
 * 
 * @return   array   
 */
function UserDeletion_userapi_delete()
{
    if (!pnUserLoggedin()) return;

    // we'll prepare the email first... Otherwise we might have some problems getting the user's data ;-)
    $feedback=pnVarCleanFromInput('feedback');
    $email=pnModGetVar('UserDeletion','email');
    if ((isset($email)) && (strlen($email)>0)) {
	$subject=_USERDELETIONMAILSUBJECT;
	$message=_USERDELETIONUSERDATA."\n";
	$message.="user: ".pnUsergetvar('uname')."\n";
	$message.="user-id: ".pnUsergetvar('uid')."\n";
	$message.="email: ".pnUsergetvar('email')."\n";
	if (isset($feedback) && (strlen($feedback)>0)) $message.=_USERDELETIONMAILFEEDBACK.": ".$feedback."\n";
	$message.="\n"._USERDELETIONMAILFOOTER;
	$headers = array('header' => '\nMIME-Version: 1.0\nContent-type: text/html');
	pnMail($email, $subject, $message,$headers);
    }
    
    // pnWebLog module
    if (pnModAvailable('pnWebLog')) {
	// we can call a function of the pnweblog api
	pnModAPIFunc('pnWebLog','user','delWebLog',array('uid'=>pnUserGetVar('uid')));
    }
    
    // pnUserPictures module
    if (pnModAvailable('UserPictures')) {
	// get all templates (except "0")
	$templates = pnModAPIFunc('UserPictures','admin','getTemplates');
	// add 0 for own galleries
	$templates[]=array('id'=>0);
	foreach ($templates as $template) {
	    $pictures = pnModAPIFunc('UserPictures','user','getPictures',array('uid'=>pnUserGetVar('uid'),'template_id'=>$template[id]));
	    foreach ($pictures as $picture) {
		pnModAPIFunc('UserPictures','user','deletePicture',array('picture_id'=>$picture[id],'uid'=>pnUserGetVar('uid'),'template_id'=>$template[id]));
	    }
	}
    }
    
    // delete the user
						    	    
    $del_uid = pnUserGetVar('uid');
    
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
    
    pnUserLogOut();
    return pnRedirect('/index.php');

}

?>

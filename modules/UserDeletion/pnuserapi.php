<?php
/**
 * @package      UserDeletion
 * @version      $Id$
 * @author       Florian Schie�l
 * @link         http://www.ifs-net.de
 * @copyright    Copyright (C) 2008
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * delete user
 *
 * This function deletes the actual user
 *
 * @return   array
 */
function UserDeletion_userapi_delete($args)
{
    if (!pnUserLoggedin()) return;
    $uid = $args['uid'];
    if (!UserDeletion_userapi_SecurityCheck(array('uid' => $uid))) return LogUtil::registerPermissionError();

	// now call all module userdeletion functions
    $mods = pnModGetUserMods();
   	$output = array();
    foreach ($mods as $mod) {
      	$file = 'modules/'.$mod['directory'].'/pnuserdeletionapi.php';
      	if (file_exists($file)) {
      	  	// Load language file
      	  	pnModLangLoad($mod['name'],'userdeletion');
      	  	// add output
      	  	unset($dummy);
      	  	// the function will return an array with key title and result
			$dummy = pnModAPIFunc($mod['name'],'userdeletion','delUser',array('uid' => $uid));
			if (is_array($dummy)) $output[] = $dummy;
		}
	}

	$uname = pnUserGetVar('uname',$uid);
	// No we'll do the things the Users-Admin-deleteUser function does
	DBUtil::deleteObjectByID('group_membership', 	$uid, 'uid');
	DBUtil::deleteObjectByID('users', 				$uid, 'uid');
//	DBUtil::deleteObjectByID('user_data', 			$uid, 'uda_uid');
	// Let other modules know we have deleted an item
    pnModCallHooks('item', 'delete', $uid, array('module' => 'Users'));
    // Add output
    $output[] = array (
    		'title' 	=> _USERDELETIONCOREDATA,
    		'result'	=> _USERDELETIONCOREDATADELETED." ".$uname
		);

	// sent notification to site admin and preconfigured email address
    $feedback	= FormUtil::getPassedValue('feedback');
    $email 		= pnModGetVar('UserDeletion','email');
    if ((isset($email)) && (strlen($email)>0) && ($args['admin'] != 1)) {
		$subject =_USERDELETIONMAILSUBJECT;
		$message =_USERDELETIONUSERDATA."\n";
		$message.="user: ".pnUsergetvar('uname',$uid)."\n";
		$message.="user-id: $uid\n";
		$message.="email: ".pnUsergetvar('email'.$uid)."\n";
		if (isset($feedback) && (strlen($feedback)>0)) $message.=_USERDELETIONMAILFEEDBACK.": ".$feedback."\n";
		$message.="\n"._USERDELETIONMAILFOOTER;
		$headers = array('header' => '\nMIME-Version: 1.0\nContent-type: text/html');
		pnMail($email, $subject, $message,$headers);
	}

	// Log out if a user deleted its own account - otherwise the admin deleted an account!
	if (pnUserGetVar('uid') == $uid) pnUserLogOut();
    return $output;
}

/**
 * security check
 *
 * This functions make a little security check. Accounts should
 * only be deleted by any person having the ACCESS_DELETE permission
 * for the UserDeletion module or if the person wants to delete the own
 * account.
 *
 * @param 	$args['uid']	int
 * @return 	bool
 */
function UserDeletion_userapi_SecurityCheck($args)
{
	return ((SecurityUtil::checkPermission('UserDeletion::', '::', ACCESS_DELETE)) || ((int)$args['uid'] == pnUserGetVar('uid')));
}
?>
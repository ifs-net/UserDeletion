<?php
 
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
    if ($uid != pnUserGetVar('uid')) return LogUtil::registerPermissionError();

	// sent notification to site admin and preconfigured email address
    $feedback	= FormUtil::getPassedValue('feedback');
    $email 		= pnModGetVar('UserDeletion','email');
    if ((isset($email)) && (strlen($email)>0)) {
		$subject =_USERDELETIONMAILSUBJECT;
		$message =_USERDELETIONUSERDATA."\n";
		$message.="user: ".pnUsergetvar('uname',$uid)."\n";
		$message.="user-id: $uid\n";
		$message.="email: ".pnUsergetvar('email'.$uid)."\n";
		if (isset($feedback) && (strlen($feedback)>0)) $message.=_USERDELETIONMAILFEEDBACK.": ".$feedback."\n";
		$message.="\n"._USERDELETIONMAILFOOTER;
		$headers = array('header' => '\nMIME-Version: 1.0\nContent-type: text/html');
//		pnMail($email, $subject, $message,$headers);
	}
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


	// Log out and return to index page
//    pnUserLogOut();
    return $output;
}

/**
 * security check
 *
 * This functions make a little security check. Accounts should 
 * only be deleted by any person having the ACCESS_DELETE permission
 * for the Users module or if the person wants to delete the own 
 * account.
 *
 * @param 	$args['uid']	int
 * @return 	bool
 */
function UserDeletion_userapi_SecurityCheck($args)
{
	return ((SecurityUtil::checkPermission('Users::', '::', ACCESS_DELETE)) || ((int)$args['uid'] == pnUserGetVar('uid')));
}
?>
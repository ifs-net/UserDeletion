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
 * This file is a skeleton file that can be adopted by every module developer
 * Place your language files in pnlang/<language>/userdeletion.php
 * This file will be loaded by the UserDeletion module.
 */
 
/**
 * Delete a user in the module "YourModule"
 * 
 * @param	$args['uid']	int		user id
 * @return	array   
 */
function YourModule_userdeletionapi_delUser($args)
{
  	$uid = $args['uid'];
	if (!pnModAPIFunc('UserDeletion','user','SecurityCheck',array('uid' => $uid))) {
	  	$result 	= _NOTHINGDELETEDNOAUTH;	// Is already defined in UserDeletion language files
	}
	else {
	  	// Here you should write your userdeletion routine.
	  	// Delete your database entries or anonymize them.

		$result = _YOURMODULEALLDELETEDFORUSER." ".pnUserGetVar('uname',$uid); // Define this constant in your userdeletion.php language file
	}
	return array(
			'title' 	=> _YOURMODULEMODULETITLE,	// Define this constant in your userdeletion.php language file
			'result'	=> $result

		);
}
?>
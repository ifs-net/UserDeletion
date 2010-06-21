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
 * the main user function
 *
 * @return       output       The main module page
 */
function UserDeletion_user_main()
{
    // Create output object - this object will store all of our output so that
    // we can return it easily when required
    $render = pnRender::getInstance('UserDeletion');

    // Return the output that has been generated by this function
    return $render->fetch('userdeletion_user_main.htm');
}

/**
 * delete function
 *
 * @return       output
 */
function UserDeletion_user_delete()
{
    if (!SecurityUtil::ConfirmAuthKey() || !pnUserLoggedIn()) {
		LogUtil::registerPermissionError();
		return pnRedirect(pnModURL('UserDeletion','user','main'));
    }


    // Security check
    if (!SecurityUtil::checkPermission('UserDeletion::', '::', ACCESS_COMMENT)) return LogUtil::registerPermissionError();

    // call the delete-function
    $output = pnModAPIFunc('UserDeletion','user','delete',array('uid' => pnUserGetVar('uid')));

    // create output
    $render = pnRender::getInstance('UserDeletion');

	// assign data
	$render->assign('output',$output);

    // Return the output that has been generated by this function
    return $render->fetch('userdeletion_user_result.htm');

}
?>
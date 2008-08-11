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
 * initialise the UserDeletion module
 *
 * @return       bool       true on success, false otherwise
 */
function UserDeletion_init()
{
    // We just need some variables
    $email = pnSessionGetVar('userdeletion_email');
    pnModSetVar('UserDeletion', 'email', (($email<>false) ? $email : ''));

    // clean up
    pnSessionDelVar('userdeletion_email');

    // Initialisation successful
    return true;
}

/**
 * delete the UserDeletion module
 *
 * @return       bool       true on success, false otherwise
 */
function UserDeletion_delete()
{
    // Delete any module variables
    pnModDelVar('UserDeletion');

    // Deletion successful
    return true;
}
?>
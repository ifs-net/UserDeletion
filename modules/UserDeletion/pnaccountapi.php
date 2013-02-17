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
 * Return an array of items to show in the your account panel
 *
 * @return   array   
 */
function UserDeletion_accountapi_getall($args)
{
    // Create an array of links to return
    pnModLangLoad('UserDeletion');
    $items = array(
				array(	'url'     => pnModURL('UserDeletion', 'user','main'),
        	           	'title'   => _USERDELETIONCANCELMEMBERSHIP,
						'icon'    => 'userdeletion.gif'
					)
                );
    // Return the items
    return $items;
}

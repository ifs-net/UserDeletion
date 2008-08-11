<?php
// Load language file
modules_get_language();
pnModLangLoad('UserDeletion', 'user');
//change the second argument to reflect what should be printed under icon.
user_menu_add_option(pnModURL('UserDeletion','user','main'), _USERDELETIONCANCELMEMBERSHIP,"modules/UserDeletion/user/pnimages/userdeletion.gif");
?>
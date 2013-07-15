<?php

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

require('../common.' . substr(strrchr(__FILE__, '.'), 1));

// if($user['authlevel'] < 1)
if($user['authlevel'] < 3)
{
  AdminMessage($lang['adm_err_denied']);
}

$planet_active = sys_get_param_int('planet_active');
if(!$planet_active)
{
  $planet_type = sys_get_param_int('planet_type', 1);
  $planet_type = $planet_type == 3 ? 3 : 1;
}
else
{
  $active_time = $time_now - 15*60;
}
$table_parent_columns = $planet_type == 3 || $planet_active;

$template = gettemplate('admin/adm_planet_list', true);

$query_str = "
SELECT 
  p.*, u.username"
. ($table_parent_columns ? ', p1.name AS parent_name' : '') .
" FROM 
  {{planets}} AS p 
  LEFT JOIN {{users}} AS u ON u.id = p.id_owner"
  . ($table_parent_columns ? ' LEFT JOIN {{planets}} AS p1 ON p1.id = p.parent_planet' : '') .
" WHERE "
 . ($planet_active ? "p.last_update >= {$active_time}" : "p.planet_type = {$planet_type}");

$query = doquery($query_str);
while ($planet_row = mysql_fetch_array($query))
{
  $template->assign_block_vars('planet', array(
    'ID'          => $planet_row['id'],
    'NAME'        => js_safe_string($planet_row['name']),
    'GALAXY'      => $planet_row['galaxy'],
    'SYSTEM'      => $planet_row['system'],
    'PLANET'      => $planet_row['planet'],
    'PLANET_TYPE' => $planet_row['planet_type'],
    'PLANET_TYPE_PRINT' => $lang['sys_planet_type_sh'][$planet_row['planet_type']],
    'PARENT_ID'   => js_safe_string($planet_row['parent_planet']),
    'PARENT_NAME' => js_safe_string($planet_row['parent_name']),
    'OWNER'       => js_safe_string($planet_row['username']),
    'OWNER_ID'    => $planet_row['id_owner'], 
  ));
}

$page_title = 
  $lang['adm_planet_list_title'] . ': ' . 
  ($planet_active ? $lang['adm_planet_active'] :
    ($planet_type ? ($planet_type == 3 ? $lang['sys_moons'] : $lang['sys_planets']) : '')
  );
$template->assign_vars(array(
  'PAGE_TITLE' => $page_title,

  'PLANET_COUNT'  => mysql_numrows($query),
  'PARENT_COLUMN' => $table_parent_columns,
));

display($template, $page_title, false, '', true);

?>

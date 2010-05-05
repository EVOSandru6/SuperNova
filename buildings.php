<?php

/**
 * buildings.php
 *
 *  Allow building of... hmm... buildings
 *
 * @version 1.3s Security checks by Gorlum for http://supernova.ws
 * @version 1.3
 * @copyright 2008 by Chlorel for XNova
 */

define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

if ($IsUserChecked == false) {
  includeLang('login');
  header("Location: login.php");
}

check_urlaubmodus ($user);

$GET_mode = SYS_mysqlSmartEscape($_GET['mode']);

  includeLang('buildings');

  // Mise a jour de la liste de construction si necessaire
  UpdatePlanetBatimentQueueList ( $planetrow, $user );

  $IsWorking = HandleTechnologieBuild ( $planetrow, $user );

  switch ($GET_mode) {
    case 'fleet':
      // --------------------------------------------------------------------------------------------------
      FleetBuildingPage ( $planetrow, $user );
      break;

    case 'research':
      // --------------------------------------------------------------------------------------------------
      ResearchBuildingPage ( $planetrow, $user, $IsWorking['OnWork'], $IsWorking['WorkOn'] );
      break;

    case 'defense':
      // --------------------------------------------------------------------------------------------------
      DefensesBuildingPage ( $planetrow, $user );
      break;

    default:
      // --------------------------------------------------------------------------------------------------
      BatimentBuildingPage ( $planetrow, $user );
      break;
  }

// -----------------------------------------------------------------------------------------------------------
// History version
// 1.0 - Nettoyage modularisation
// 1.1 - Mise au point, mise en fonction pour linéarisation du fonctionnement
// 1.2 - Liste de construction batiments
?>
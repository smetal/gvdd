<?php
/**
 * @version   $Id: mod_rokweather.php 6300 2013-01-04 17:17:38Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!defined('ROKWEATHER')) define('ROKWEATHER','ROKWEATHER');
if (!defined('ROKWEATHER_ROOT')) define('ROKWEATHER_ROOT', dirname(__FILE__));

require_once(ROKWEATHER_ROOT . '/lib/include.php');

$ajaxurl = JRoute::_(JURI::Root(true) ."/modules/mod_rokweather/ajax.php?moduleid=".$module->id."&".JSession::getFormToken()."=1", true);

$output   = "";

$rokweather = new RokWeather();
$rokweather->loadScripts($params);
$weather = $rokweather->getWeather($params);

require(JModuleHelper::getLayoutPath('mod_rokweather'));

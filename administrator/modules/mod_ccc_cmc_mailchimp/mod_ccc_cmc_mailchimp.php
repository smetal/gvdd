<?php
/**
 * Compojoom Control Center
 * @package Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All rights reserved
 * @Joomla! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 0.9.0 beta $
 **/

// No direct access.
defined('_JEXEC') or die;

// Include the mod_popular functions only once.
require_once dirname(__FILE__).'/helper.php';

// Render the module
require JModuleHelper::getLayoutPath('mod_ccc_cmc_mailchimp', $params->get('layout', 'default'));
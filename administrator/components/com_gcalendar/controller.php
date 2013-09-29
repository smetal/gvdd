<?php
/**
 * GCalendar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GCalendar is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GCalendar.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package		GCalendar
 * @author		Digital Peak http://www.digital-peak.com
 * @copyright	Copyright (C) 2007 - 2013 Digital Peak. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();

JLoader::import('joomla.application.component.controller');

class GCalendarController extends JControllerLegacy {

	public function display($cachable = false, $urlparams = false){
		JRequest::setVar('view', JRequest::getCmd('view', 'cpanel'));
		$view = JRequest::getVar('view', 'cpanel');

		if($view != 'gcalendar') {
			JSubMenuHelper::addEntry(JText::_('COM_GCALENDAR_SUBMENU_CPANEL'), 'index.php?option=com_gcalendar&view=cpanel', $view == 'cpanel');
			JSubMenuHelper::addEntry(JText::_('COM_GCALENDAR_SUBMENU_GCALENDARS'), 'index.php?option=com_gcalendar&view=gcalendars', $view == 'gcalendars');
			JSubMenuHelper::addEntry(JText::_('COM_GCALENDAR_SUBMENU_TOOLS'), 'index.php?option=com_gcalendar&view=tools', $view == 'tools');
			JSubMenuHelper::addEntry(JText::_('COM_GCALENDAR_SUBMENU_SUPPORT'), 'index.php?option=com_gcalendar&view=support', $view == 'support');
		}
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-calendar {background-image: url(../media/com_gcalendar/images/48-calendar.png);background-repeat: no-repeat;}');

		$params = JComponentHelper::getParams('com_gcalendar');
		if ($params->get('timezone', '') == '') {
			JError::raiseNotice(0, JText::_('COM_GCALENDAR_FIELD_CONFIG_SETTINGS_TIMEZONE_WARNING'));
		}
		parent::display($cachable, $urlparams);
	}

	public function import() {
		JRequest::setVar('view', 'import');
		JRequest::setVar('layout', 'login');

		$this->display();
	}
}
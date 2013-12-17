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

JLoader::import('components.com_gcalendar.util', JPATH_ADMINISTRATOR);

function GCalendarBuildRoute( &$query )
{
	$segments = array();
	$view = null;
	if(isset($query['view']))
	{
		$segments[] = $query['view'];
		$view = $query['view'];
		unset( $query['view'] );
	}
	if($view === 'event'){
		if(isset($query['gcid']))
		{
			$segments[] = $query['gcid'];
			unset( $query['gcid'] );
		}
		if(isset($query['eventID']))
		{
			$segments[] = $query['eventID'];
			unset( $query['eventID'] );
		}
	}
	return $segments;
}

/**
 * @param	array
 * @return	array
 */
function GCalendarParseRoute( $segments )
{
	// Get the active menu item
	$menu = JFactory::getApplication()->getMenu();
	$item = $menu->getActive();

	$vars = array();
	$view = $segments[0];
	//if the view is calendars it is a menu link
	if ($view == 'calendars') {
		$view = $item->query['view'];
	}
	$vars['view'] = $view;
	switch($view)
	{
		case 'event':
			$vars['gcid'] = $segments[1];
			if (count($segments) < 3) {
				$vars['eventID'] = JRequest::getVar('eventId');
			} else {
				$vars['eventID'] = $segments[2];
			}
			$vars['Itemid'] = GCalendarUtil::getItemId($vars['gcid']);
			break;
		case 'google':
		case 'gcalendar':
			// do nothing
			break;
	}
	return $vars;
}
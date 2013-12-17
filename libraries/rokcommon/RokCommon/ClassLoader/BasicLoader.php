<?php
/**
 * @version   $Id: BasicLoader.php 53647 2012-06-12 21:41:03Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokCommon_ClassLoader_BasicLoader extends RokCommon_ClassLoader_AbstractLoader
{
	/**
	 * Loads the given class or interface.
	 *
	 * @param string $class The name of the class
	 *
	 * @return Boolean|null True, if loaded
	 */
	public function loadClass($class)
	{
		if ($this->hasBeenChecked($class)) return false;
		if ($file = $this->findFileForClass($class)) {
			require $file;
			return true;
		}
		$this->addChecked($class);
		return false;
	}
}

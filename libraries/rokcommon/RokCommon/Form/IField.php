<?php
/**
 * @version   $Id: IField.php 57540 2012-10-14 18:27:59Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

interface RokCommon_Form_IField extends RokCommon_Form_IItem
{
   	/**
   	 * Method to get the field name used.
   	 *
   	 * @param   string  $fieldName  The field element name.
   	 *
   	 * @return  string  The field name
   	 *
   	 * @since   11.1
   	 */
   	public function getFieldName($fieldName);


}

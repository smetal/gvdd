<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since       11.1
 */
class JFormFieldWundergroundLogo extends JFormField
{
	/**
	 * @var string
	 */
	protected $type = 'Wundergroundlogo';

	/**
	 * @return string
	 */
	protected function getLabel()
	{
        $doc = JFactory::getDocument();
        $version = new JVersion();
        $doc->addStyleDeclaration(".rok-wunderground-logo {max-width: 126px;clear: both;}");

        if (isset($this->element['label']) && !empty($this->element['label'])) {
            $label = JText::_((string)$this->element['label']);
            $css   = (string)$this->element['class'];
			return '<img src="../modules/mod_rokweather/images/wundergroundLogo_black_horz.png" alt="wunderground.com® logo" class="'.$css.'"/>';
            
        } else {
            return;
        }
	}

	/**
	 * @return mixed
	 */
	protected function getInput()
	{
        return;
	}

}

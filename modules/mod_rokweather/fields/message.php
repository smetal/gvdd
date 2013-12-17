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
class JFormFieldMessage extends JFormField
{
	/**
	 * @var string
	 */
	protected $type = 'Message';

	/**
	 * @return string
	 */
	protected function getLabel()
	{
        $doc = JFactory::getDocument();
        $version = new JVersion();
        $doc->addStyleDeclaration(".rok-message-notice {background: #FFF3A3;border: 1px solid #E7BD72;color: #B79000;display: block;padding: 8px 10px;}");
        $doc->addStyleDeclaration(".rok-message-error {background: #d2edc9;border: 1px solid #red;color: #red;display: block;padding: 8px 10px;}");
        $doc->addStyleDeclaration(".rok-message-success {background: #d2edc9;border: 1px solid #90e772;color: #2b7312;display: block;padding: 8px 10px;}");

        if (isset($this->element['label']) && !empty($this->element['label'])) {
            $label = JText::_((string)$this->element['label']);
            $css   = (string)$this->element['class'];
            if (version_compare($version->getShortVersion(), '3.0', '>=')) {
                $doc->addStyleDeclaration(".rok-message-success, .rok-message-error, .rok-message-notice {border-radius:4px 4px 4px 4px;}");
                return '<div width="100%" class="' . $css . '">' . $label . '</div>';
            } else {
                return '<label width="100%"><div width="100%" class="' . $css . '">' . $label . '</div></label>';
            }
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

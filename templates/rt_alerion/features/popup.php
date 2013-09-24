<?php
/**
* @version   $Id: popup.php 9238 2013-04-09 17:10:45Z djamil $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeaturePopup extends GantryFeature
{
    var $_feature_name = 'popup';

	function render($position)
	{
		global $gantry;
	    ob_start();

	    $user = JFactory::getUser();

	    ?>
	    <div class="rt-block <?php global $gantry; echo 'rt-'.$gantry->get("blocks-default-overlay").'-block'; ?>">
			<div class="rt-popupmodule-button">
				<?php echo $this->_renderRokBoxLink(); ?>
					<span class="desc"><?php echo $this->get('text'); ?></span>
				</a>
			</div>
		</div>
		<?php
	    return ob_get_clean();
	}

	function _renderRokBoxLink(){
		$isRokBox2 = @file_exists(JPATH_BASE . '/plugins/editors-xtd/rokbox/rokbox.xml');
		$output = array();

		if ($isRokBox2){
			$output[] = '<a class="buttontext button" data-rokbox href="#" data-rokbox-element="#rt-popup">';
		} else {
			$output[] = '<a href="#" class="buttontext button" rel="rokbox['.$this->get('width').' '.$this->get('height').'][module=rt-popup]">';
		}

		return implode("\n", $output);
	}
}

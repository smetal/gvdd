<?php
/**
 * @version   $Id: rokweathersourcefields.php 6300 2013-01-04 17:17:38Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('JPATH_BASE') or die;

JHtml::_('behavior.framework', true);
jimport('joomla.html.html');
jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');


/**
 *
 */
class JFormFieldRokWeatherSourceFields extends JFormField
{
    /**
     * @var string
     */
    static $ROKWEATHER_ROOT;
    /**
     * @var string
     */
    static $SOURCE_DIR;

    /**
     * @var array
     */
    protected $element_dirs = array();

    /**
     * @var string
     */
    public $type = 'RokWeatherSourceFields';

    /**
     * @param null $parent
     */
    public function __construct($form = null)
    {
        if (!defined('ROKWEATHER')) define('ROKWEATHER', 'ROKWEATHER');

        // Set base dirs
        self::$ROKWEATHER_ROOT = JPATH_ROOT . '/modules/mod_rokweather';
        self::$SOURCE_DIR = self::$ROKWEATHER_ROOT . '/lib/RokWeather/Source';

        //load up the RTCommon
        require_once(self::$ROKWEATHER_ROOT . '/lib/include.php');

        parent::__construct($form);
    }

    /**
     * @return string
     */
    protected function getLabel()
    {
        $doc = JFactory::getDocument();
        $version = new JVersion();

        $buffer = '';
        $form = RokSubfieldForm::getInstanceFromForm($this->form);

        $sourcesets = $form->getSubFieldsets('rokweather-sources');

        JForm::addFieldPath(dirname(__FILE__) . '/fields');

        if (version_compare($version->getShortVersion(), '3.0', '<')) {

            foreach ($sourcesets as $sourceset => $sourceset_val) {
                $sourceset_fields = $form->getSubFieldset('rokweather-sources', $sourceset, 'params');
                ob_start();
                ?>
            <div class="sourceset" id="srouceset-<?php echo $sourceset;?>">
                <ul class="themeset">
                    <?php foreach ($sourceset_fields as $sourceset_field): ?>
                    <li>
                        <?php echo $sourceset_field->getLabel(); ?>
                        <?php echo $sourceset_field->getInput(); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
                $buffer .= ob_get_clean();
            }

        } else {

            foreach ($sourcesets as $sourceset => $sourceset_val) {
                $sourceset_fields = $form->getSubFieldset('rokweather-sources', $sourceset, 'params');
                ob_start();
                ?>
            <div class="sourceset" id="srouceset-<?php echo $sourceset;?>">

                <?php foreach ($sourceset_fields as $sourceset_field): ?>
                <div class="control-group">

                    <div class="control-label themeset">
                        <?php echo $sourceset_field->getLabel(); ?>
                    </div>
                    <div class="controls themeset">
                        <?php echo $sourceset_field->getInput(); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php
                $buffer .= ob_get_clean();
            }
        }
        return $buffer;
    }

    /**
     * @return string
     */
    protected function getInput()
    {
        return;
    }
}
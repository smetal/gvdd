<?php
/**
 * @version   $Id: rokweathersources.php 6300 2013-01-04 17:17:38Z btowles $
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
class JFormFieldRokweathersources extends JFormField
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
    public $type = 'RokWeatherSources';

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
    protected function getInput()
    {
        $document = JFactory::getDocument();
        $version = new JVersion();

        if (version_compare($version->getShortVersion(), '3.0', '<')) {

            $js = "window.addEvent('load', function() {
                $('" . $this->id . "').addEvent('change', function(){
                    var sel = this.getSelected().get('value');
                    $$('." . $this->element['name'] . "').getParent('li').setStyle('display','none');
                    $$('.'+sel).getParent('li').setStyle('display','block');
                }).fireEvent('change');
            });";

        } else {

            $js = "
            window.addEvent('load', function() {
            var chzn = $('" . $this->id . "_chzn');
                if(chzn!=null){
                    chzn.addEvent('click', function(){
                        $$('." . $this->element['name'] . "').getParent('div.control-group').setStyle('display','none');
                        var text = $('" . $this->id . "_chzn').getElement('span').get('text');
                        var options = $('" . $this->id . "').getElements('option');
                        options.each(function(option) {
                        var optText = String(option.get('text'));
                        var optValue = String(option.get('value'));
                            if(text == optText){
                                var sel = optValue;
                            }
                            $$('.'+sel).getParent('div.control-group').setStyle('display','block');
                        });
                    }).fireEvent('click');
                }
            });";
        }

        $document->addScriptDeclaration($js);

        //Find Sources
        $sources = RokWeather_SourceLoader::getAvailableSources(self::$SOURCE_DIR);
        $options = array();

        foreach ($sources as $source_name => $source) {
            // build the html list for content type
            $options[] = JHtml::_('select.option', $source_name, JText::_(ucwords($source_name)), 'value', 'text');
        }
        $html = JHtml::_('select.genericlist', $options, $this->name, ' size="' . $this->element['size'] . '" ', 'value', 'text', $this->value, $this->id);
        return $html;
    }

}
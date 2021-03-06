<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2018 Leo Feyer
 *
 * @package   CountdownCalendar
 * @author    Marina Diezler
 * @license   none
 * @copyright Coffeincode 2018
 */

/**
 * Namespace
 */
namespace CoffeinCode\CountdownCalendar;

use CoffeinCode\CountdownCalendar\ModuleCountdownDoor;
use CoffeinCode\CountdownCalendar\CountdownCalendarModel;
use CoffeinCode\CountdownCalendar\CountdownDoorModel;

class ModuleCountdownCalendar extends ModuleCountdownDoor
{
    /**
     * Template
     * @var string
     */
    protected $strChildTable = "tl_countdown_door";
    protected $strTemplate = 'mod_ac_calendar';
    protected $strDetTemplate = 'ac_default';
    protected $acJumpTo='';
        
    public function generate()
    {
        if (TL_MODE == 'BE') {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### Countdown-Calendar ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;
            if (!isset($_GET['item']) && \Contao\Config::get('useAutoItem') && isset($_GET['auto_item'])) {
                Input::setGet('item', Input::get('auto_item'));
            }
                          
            $this->strDetTemplate = $this->ac_details_template;
            $this->strTemplate = $this->ac_template;
            return $objTemplate->parse();
        }
                
        return parent::generate();
    }
        
    
        
        
    /**
     * Generate the module
         *
         *
     */
    protected function compile()
    {
        $t = $this->strChildTable;
        
        
        $arrColumns = array($t.".pid=?", $t.".published=?");
        $arrValues = array($this->ac_calendar, "1");
        $options = array('order'=>'sorting');
        $arrObj= CountdownDoorModel::findBy($arrColumns, $arrValues, $options);
        $arrObjSecrets= CountdownDoorModel::findBy($arrColumns, $arrValues, $options);
        $this->strDetTemplate = $this->ac_details_template;
        $this->strModTemplate = $this->ac_template;
            
        $arrObjCalendar = CountdownCalendarModel::findByIdOrAlias($this->ac_calendar);

        $debug=$arrObjCalendar->acDebug;
        $this->Template->debug=$debug;
        $this->ac_jumpTo = $arrObjCalendar->jumpTo;
        $this->Template->setData($arrObjCalendar->row());
        $this->Template->message=null;
            

        $arrObjCalendar->acDebug?$parsingDate=$arrObjCalendar->acDebugDate:$parsingDate=time();
        $acDoorList = $this->parseAllDoors($parsingDate, $this->ac_details_template, $arrObjSecrets);
           
        //parse all secrets:
        $arrSecretsList = $this->parseAllSecrets($parsingDate, $this->ac_jumpTo, 'default_secret', $debug, $arrObj);
                
           
        if ($acDoorList) {
            $this->Template->doorsList = $acDoorList;
            $this->Template->secretsList = $arrSecretsList;
        } else {
            //keine Tür verfügbar - leeren Kalender anzeigen
            $this->Template->doorsList = $this->Template->secretsList = null;
            $this->Template->message = $GLOBALS['TL_LANG']['MSC']['ac_calendar_empty'];
        }
             
        //compile and prepare calendar:
        if ($arrObjCalendar->singleSRC != '') {
            $arrCal = $arrObjCalendar->row();
            $objImgModel = \FilesModel::findByUuid($arrObjCalendar->singleSRC);
            if ($objImgModel === null) {
                if (!\Validator::isUuid($arrObjCalendar->singleSRC)) {
                    $this->Template->text = '<p class="error">'.$GLOBALS['TL_LANG']['ERR']['version2format'].'</p>';
                }
            } elseif (is_file(TL_ROOT . '/' . $objImgModel->path)) {
                $arrCal['singleSRC'] = $objImgModel->path;

                $this->addImageToTemplate($this->Template, $arrCal);
            }
        }
                
        //todo: deserialize colors and dump them to the global scss-variables-file
                
        //$doorColor='';
        $doorFillColor='';
        $doorFontColor='';
        $doorSecretBgColor='';
                
        $doorColor = deserialize($this->Template->doorBgColor);
        $this->Template->doorCol=$doorColor[0];
        $this->Template->doorOpacity=$doorColor[1];
        //todo: catch empty calendars
        //todo: add javascript-template
        $sassVars[]=array('')  ;
                
        $GLOBALS['TL_CSS'][]='/bundles/coffeincodecountdowncalendar/cc-styles.scss|static';
        //   $sassVars="$cc_doorBgColor: #000";//". deserialize($this->Template->doorBgColor)[1].";";
    }
        
    /*
     * @var $arrVariables This is an array of VariableNames and associated values given by the backend-user
     *
     * The variables are written to a file located in the public folder, the file is overwritten every time the function is called
     *
     */
    protected function writeSassVars($arrVariables, $strFilename)
    {
    }
}

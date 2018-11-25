<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
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

abstract class ModuleCountdownDoor extends \Module
{
    /**
     * Template
     * @var string
     */
    protected $strAcCeTemplate;
    protected $idReaderPage;
        
    /**
     * Function
     * Parses a single Calendar Door:
     *
     * Every Calendar door has a teaser and a link to its reader-page with alias.
     * As the calendar doors are put out in the order they are stored in the backend it is not clear if the actual door to be parsed is already openable or not.
     * A locked door
     *
     *
     */
    protected function parseDoor($objDoor, $strTimestamp, $strTemplate, $debug=false, $objReaderPage=null)
    {
        $objTemplate = new \FrontendTemplate($strTemplate);
        $objTemplate->setData($objDoor->row());
        $objTemplate->locked = true; //check this! A door is locked if the actual date of parsing is smaller than the active-timestamp of the door to be parsed.
        $objTemplate->class = (($objDoor->cssClass != '') ? ' ' . $objDoor->cssClass : '') . $strClass;
        $objDoor->activeStart <= $strTimestamp ? $objTemplate->locked=false:$objTemplate->locked=true;
        $objTemplate->debug=$debug;
        if ($objReaderPage) {
            if (! $objTemplate->locked) {
                $objPage = \Contao\PageModel::findByPk($objReaderPage);
                $strLink= ampersand($objPage->getFrontendUrl((\Config::get('useAutoItem') ? '/' : '/items/') . ($objDoor->alias ?: $objDoor->id)));
                if ($debug) {
                    $strLink.="?debug=true";
                }
                $objTemplate->link =$strLink;
                $objTemplate->readerID = $objReaderPage->id;
            } else {
                $objTemplate->link ="#";
                $objTemplate->teaser="";
            }
        }
            
        $id = $objDoor->id;
        $objTemplate->door_index=$objDoor->door_index;
            
        // generate anonymous functions for the text/content-elements which is only called in case the template parsed asks for it, see ModuleNews.php
        $objTemplate->doorText = function () use ($id) {
            $strText = '';
            if (! $objTemplate->locked) {
                $objElement = \ContentModel::findPublishedByPidAndTable($id, 'tl_countdown_door');
                if ($objElement !== null) {
                    while ($objElement->next()) {
                        $strText .= $this->getContentElement($objElement->current());
                    }
                }
            }
            return $strText;
        };
            
        $objTemplate->hasDoorText  = function () use ($objArticle) {
            if (! $objTemplate->locked) {
                return \ContentModel::countPublishedByPidAndTable($objArticle->id, 'tl_countdown_door') > 0;
            } else {
                return false;
            }
        };
              
            
        return $objTemplate->parse($objDoor);
    }
     
      
    protected function parseAllDoors($strTimestamp, $intTemplate, $arrDoors=null)
    {
        if ($arrDoors === null) {
            return null;
        } else {
            $arrHelperDoors='';
            while ($arrDoors->next()) {
                $arrHelperDoors .= $this->parseDoor($arrDoors, $strTimestamp, $intTemplate);
            }
            return $arrHelperDoors;
        }
    }
        
   
    protected function parseAllSecrets($strTimestamp, $objReaderPage, $intTemplate, $debug, $arrDoors=null)
    {
        if ($arrDoors === null) {
            return null;
        } else {
            $arrHelperSecrets='';
            while ($arrDoors->next()) {
                $arrHelperSecrets.= $this->parseDoor($arrDoors, $strTimestamp, 'default_secret', true, $objReaderPage);
            }
            return $arrHelperSecrets;
        }
    }
     
     
      
       
    /**
     * Erstellt den Link zur Detailseite.
     * @param $intId
     * @return string
     *
     *      */
    private function makeLink($intId)
    {
        //global $objPage;
        $objPage = \Contao\PageModel::findByPk($intId);
 
        if ($objPage) {
            return $objPage->alias .'/';
        }
 
        return '';
    }
}

<?php

/**
 * Redirect Manager
 *
 * Copyright (C) 2019-2022 Andrew Stevens Consulting
 *
 * @package    asconsulting/redirect_manager
 * @link       https://andrewstevens.consulting
 */



namespace RedirectManager\Backend;

use RedirectManager\Model\Redirect as RedirectModel;

use Contao\Backend as Contao_Backend;
use Contao\Database;
use Contao\Datacontainer;
use Contao\FilesModel;
use Contao\Image;
use Contao\Input;
use Contao\NewsModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Versions;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class Redirect extends Contao_Backend
{

	public function updatePublished()
	{
		Database::getInstance()->prepare("UPDATE tl_asc_redirect SET published='1' WHERE IF(start = '', 0, CONVERT(start, UNSIGNED)) < ? AND IF(start = '', 0, CONVERT(start, UNSIGNED)) > 0 AND (IF(stop = '', 0, CONVERT(stop, UNSIGNED)) > ? OR IF(stop = '', 0, CONVERT(stop, UNSIGNED)) = 0)")->execute(time(), time());
		Database::getInstance()->prepare("UPDATE tl_asc_redirect SET published='' WHERE IF(stop = '', 0, CONVERT(stop, UNSIGNED)) < ? AND IF(stop = '', 0, CONVERT(stop, UNSIGNED)) > 0")->execute(time());
	}


    public function generateLabel($row, $label, $dc, $args)
    {
    	$objRedirect = RedirectModel::findByPk($row['id']);
    
        // If type is legacy, add tag to label to mark it, otherwise display like normal
    	if($objRedirect->type == 'regular')
    		$strLabel = '<span style="color:orange; font-weight:800;">[LEGACY]</span> <span class="category">[' .$objRedirect->category .']</span> <span class="code">' .$objRedirect->type .'</span>: <span class="redirect">' .$objRedirect->redirect ."</span>";
    	else
    		$strLabel = '<span class="category">[' .$objRedirect->category .']</span> <span class="code">regular</span>: <span class="redirect">' .$objRedirect->redirect ."</span>";
        
    	if ($objRedirect->target_url) {
    		$strLabel .= ' <span class="arrow">&rarr;</span> <span class="target">' .$objRedirect->target_url ."</span>";
    	} else if ($objRedirect->target_page) {
    		$objPage = PageModel::findByPk($objRedirect->target_page);
    		if ($objPage) {
    			$strLabel .= ' <span class="arrow">&rarr;</span> <span class="page">' .$objPage->title ."</span>";
    		}
    	} else if ($objRedirect->target_file) {
    		$objFile = FilesModel::findByUuid($objRedirect->target_file);
    		if ($objFile) {
    			$strLabel .= ' <span class="arrow">&rarr;</span> <span class="file">' .$objFile->path ."</span>";
    		}
    	}  else if ($objRedirect->target) {
    	    
    	    // Using the new tag based system. Determine which tag we have to figure out what the URL is
    	    
    	    if(str_contains($objRedirect->target, 'link_url')) {
    	        $page_id = $this->getPageIdFromInsertTag($objRedirect->target);
    	        $objPage = PageModel::findOneBy(['id = ?'], [$page_id]);
    	        if ($objPage) {
					$strLabel .= ' <span class="arrow">&rarr;</span> <span class="file">' . $objPage->getFrontendUrl() ."</span>";
				} else {
				    $strLabel .= ' <span class="arrow">&rarr;</span> <span class="file">PAGE_NOT_FOUND</span>';
				}
    	    } else if(str_contains($objRedirect->target, 'news_url')) {
    	        $news_id = $this->getNewsIdFromInsertTag($objRedirect->target);
	            $objNews = NewsModel::findOneBy(['id = ?'], [$news_id]);
				if ($objNews) {
				    $newsUrl = System::getContainer()->get('contao.routing.content_url_generator')->generate($objNews, [], UrlGeneratorInterface::ABSOLUTE_PATH);
					$strLabel .= ' <span class="arrow">&rarr;</span> <span class="file">' .$newsUrl ."</span>";
				} else {
				    $strLabel .= ' <span class="arrow">&rarr;</span> <span class="file">NEWS_NOT_FOUND' .$objFile->path ."</span>";
				}
    	        
    	    }
    	}
    	$arg[0] = $strLabel;
    
    	return $strLabel;
    }


	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		if (strlen(Input::get('tid')))
		{
			$this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1), (@func_get_arg(12) ?: null));
			$this->redirect($this->getReferer());
		}

		$href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
	}


	public function toggleVisibility($intId, $blnVisible, DataContainer $dc=null)
	{
        // Not sure what Versions is
		$objVersions = new Versions('tl_asc_redirect', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_asc_redirect']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_asc_redirect']['fields']['published']['save_callback'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, ($dc ?: $this));
				}
				elseif (is_callable($callback))
				{
					$blnVisible = $callback($blnVisible, ($dc ?: $this));
				}
			}
		}

		// Update the database
		$this->Database->prepare("UPDATE tl_asc_redirect SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
					   ->execute($intId);

		$objVersions->create();
	}
	
	
	//////////////////////
	// HELPER FUNCTIONS //
	//////////////////////
	
	public function getPageIdFromInsertTag($tag) {
        
        // Remove the first half of the tag
        $cleaned = str_replace("{{link_url::","", $tag);
        
        // Remove the second half of the tag
        $cleaned = str_replace("|urlattr}}","", $cleaned);

        return $cleaned;
    }
    
    public function getNewsIdFromInsertTag($tag) {
        
        // Remove the first half of the tag
        $cleaned = str_replace("{{news_url::","", $tag);
        
        // Remove the second half of the tag
        $cleaned = str_replace("|urlattr}}","", $cleaned);

        return $cleaned;
    }

}

<?php


namespace Bcs\Frontend;

use Bcs\Model\Redirect as RedirectModel;

use Contao\Environment;
use Contao\Frontend as Contao_Frontend;
use Contao\Database;


/**
 * Class RedirectManager\DirectoryPage
 */
class Redirect extends Contao_Frontend {

	public function updatePublished()
	{
		Database::getInstance()->prepare("UPDATE tl_redirect SET published='1' WHERE start < ? AND (stop > ? OR stop = 0)")->execute(time(), time());
		Database::getInstance()->prepare("UPDATE tl_redirect SET published='' WHERE stop < ? ")->execute(time());
	}

	public function lookupRedirect($arrFragments)
    {
		echo Environment::get('request') ."<br>";
		echo Environment::get('host') ."<br>";
		var_dump($arrFragments);
		die();

        return $arrFragments;
    }
}

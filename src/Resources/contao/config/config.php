<?php

/**
 * Redirect Manager
 *
 * Copyright (C) 2019-2022 Andrew Stevens Consulting
 *
 * @package    asconsulting/redirect_manager
 * @link       https://andrewstevens.consulting
 */



//array_insert($GLOBALS['BE_MOD'], 1, array('redirect_manager' => array()));

$GLOBALS['BE_MOD']['redirect_manager']['redirects'] = array(
	'tables' => array('tl_asc_redirect'),
	'icon'   => 'system/modules/oces_navigation/assets/icons/page_tag_navigation.png'
);


// Front end modules
$GLOBALS['FE_MOD']['redirect_manager'] = array('redirect_404' => 'RedirectManager\Module\Redirect404');


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_asc_redirect'] = 'RedirectManager\Model\Redirect';


/**
 * Styles
 */
 if (version_compare(VERSION, '4.4', '>=')) {
	$GLOBALS['TL_CSS'][] = 'system/modules/redirect_manager/assets/css/backend-contao4.css|static';
}

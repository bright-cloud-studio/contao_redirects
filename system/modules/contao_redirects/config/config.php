<?php

/**
 * Redirect Manager
 *
 * Copyright (C) 2019-2022 Andrew Stevens Consulting
 *
 * @package    asconsulting/redirect_manager
 * @link       https://andrewstevens.consulting
 */



/**
 * Back end modules
 */

/*
if (!array_key_exists('redirect_manager', $GLOBALS['BE_MOD']))
{
    array_insert($GLOBALS['BE_MOD'], 1, array('redirect_manager' => array()));
}

array_insert($GLOBALS['BE_MOD']['redirect_manager'], 0, array
(
	'redirects' => array
	(
		'tables' => array('tl_asc_redirect'),
		'icon'   => '/system/modules/redirect_manager/assets/redirect.svg'
	)
));
*/

// Front end modules
$GLOBALS['FE_MOD']['redirect_manager'] = array('redirect_404' => 'Bcs\Module\Redirect404');


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_asc_redirect'] = 'Bcs\Model\Redirect';


/**
 * Styles
 */
$GLOBALS['TL_CSS'][] = 'system/modules/contao_redirects/assets/css/backend-contao4.css|static';

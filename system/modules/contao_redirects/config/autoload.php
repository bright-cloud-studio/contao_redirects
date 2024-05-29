<?php

/**
 * Bright Cloud Studio's Contao Redirects
 *
 * Copyright (C) 2024-2025 Bright Cloud Studio
 *
 * @package    bright-cloud-studio/contao_redirects
 * @link       https://brightcloudstudio.com
 */



/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
    'Bcs\Model\Redirect' 		=> 'system/modules/contao_redirects/library/Bcs/Model/Redirect.php',
    'Bcs\Frontend\Redirect' 	=> 'system/modules/contao_redirects/library/Bcs/Frontend/Redirect.php',
    'Bcs\Backend\Redirect' 		=> 'system/modules/contao_redirects/library/Bcs/Backend/Redirect.php',
    'Bcs\Module\Redirect404' 	=> 'system/modules/contao_redirects/library/Bcs/Backend/Redirect404.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
  'mod_redirect_manager' 			=> 'system/modules/contao_redirects/templates/modules'
));

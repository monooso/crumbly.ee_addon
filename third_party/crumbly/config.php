<?php

/**
 * Crumbly NSM Add-on Updater information.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Crumbly
 */

if ( ! defined('CAMPAIGNER_NAME'))
{
  define('CRUMBLY_NAME', 'Crumbly');
  define('CRUMBLY_VERSION', '1.2.0');
}

$config['name']     = CRUMBLY_NAME;
$config['version']  = CRUMBLY_VERSION;
$config['nsm_addon_updater']['versions_xml']
  = 'http://experienceinternet.co.uk/software/feeds/crumbly';

/* End of file      : config.php */
/* File location    : third_party/crumbly/config.php */

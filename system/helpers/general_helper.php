<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter URL Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/url_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Site URL
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('generar_menu'))
{
	function generar_menu($uri = '')
	{
            $codigo = '<ul class="nav">
                <li><a href="<?=base_url();?>index.php/dashboard">Dashboard</a></li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">Administrador<b class="caret"></b></a>
                  <ul class="dropdown-menu">
                    <li><a href="<?=base_url();?>index.php/adm_tecnicos">Plantas</a></li>
                    <li><a href="#">Usuarios</a></li>
                  </ul>
                </li>
              </ul>';
           return $codigo; 
	}
}


/* End of file url_helper.php */
/* Location: ./system/helpers/url_helper.php */
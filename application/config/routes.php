<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

//$route['default_controller'] = "welcome";
//$route['404_override'] = '';

$route['administracion/index'] = 'administracion/index';

$route['dashboard/index'] = 'dashboard/index';
$route['dashboard/buscar'] = 'dashboard/buscar';
$route['dashboard/(:any)'] = 'dashboard/view/$1';
$route['dashboard'] = 'dashboard';

/*
$route['adm_tecnicos/editar/(:any)'] = 'adm_tecnicos/editar/$1';
$route['adm_tecnicos/crear'] = 'adm_tecnicos/crear';
$route['adm_tecnicos/(:any)'] = 'adm_tecnicos/view/$1';
$route['adm_tecnicos'] = 'adm_tecnicos';

$route['news/create'] = 'news/create';
$route['news/(:any)'] = 'news/view/$1';
$route['news'] = 'news';
 * 
 */
//$route['(:any)'] = 'dashboard/buscar/$1';
//$route['default_controller'] = 'dashboard/index';
$route['default_controller'] = 'login/index';
/* End of file routes.php */
/* Location: ./application/config/routes.php */
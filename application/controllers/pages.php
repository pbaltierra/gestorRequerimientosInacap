<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of pages
 *
 * @author Tharsis
 */
class Pages extends MX_Controller {

	public function view($page = 'home')
	{
                $this->load->helper('url');
                $this->load->helper('form');
                $this->load->library('form_validation');
                
                if ( ! file_exists('application/views/pages/'.$page.'.php'))
                {
                        // Whoops, we don't have a page for that!
                        show_404();
                }

                $this->load->helper('general');
                
                $dataHeader['title'] = ucfirst($page); // Capitalize the first letter
                //$dataBody['htmlLogin'] = Modules::run('login/index/index');
                //$dataMenu['menu'] = "";
                
                $this->load->view('templates/header', $dataHeader);
                $this->load->view('templates/menu', $dataHeader);
                $this->load->view('pages/'.$page);
                $this->load->view('templates/footer', $dataHeader);
	}       
        
        
        
}

?>

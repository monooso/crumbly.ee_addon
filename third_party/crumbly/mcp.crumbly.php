<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * Crumbly module control panel.
 *
 * @author			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright		Experience Internet
 * @package			Crumbly */

class Crumbly_mcp {

	/* --------------------------------------------------------------
	 * PRIVATE PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * ExpressionEngine object reference.
	 *
	 * @access	private
	 * @var		object
	 */
	private $_ee;
	
	/**
	 * Model.
	 *
	 * @access	private
	 * @var		object
	 */
	private $_model;
	
	
	
	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		$this->_ee =& get_instance();

		// Load the model.
		$this->_ee->load->model('crumbly_model');
		$this->_model = $this->_ee->crumbly_model;

		// Set the base CP query string and URL.
		$this->_base_qs 	= 'C=addons_modules' .AMP .'M=show_module_cp' .AMP .'module=' .$this->_model->get_package_name();
		$this->_base_url	= BASE .AMP .$this->_base_qs;
		
		// Add a base breadcrumb.
		$this->_ee->cp->set_breadcrumb($this->_base_url, $this->_ee->lang->line('crumbly_module_name'));
	}
	
	
	/**
	 * Module index page.
	 *
	 * @access	public
	 * @return	string
	 */
	public function index()
	{
		// Load our glamorous assistants.
		$this->_ee->load->helper('form');
		$this->_ee->load->library('table');
		
		// Retrieve the theme folder URL.
		$theme_url = $this->_model->get_package_theme_url();
		
		// Include the main JS file.
		$this->_ee->cp->add_to_foot('<script type="text/javascript" src="' .$theme_url .'js/cp.js"></script>');
		$this->_ee->javascript->compile();

		// Include the CSS.
		$this->_ee->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' .$theme_url .'css/cp.css" />');
		
		$vars = array(
			'form_action'		=> $this->_base_qs .AMP .'method=run_test',
			'cp_page_title'		=> $this->_ee->lang->line('hd_crumbly_settings'),
			'settings'			=> $this->_model->get_package_settings()
		);
		
		return $this->_ee->load->view('index', $vars, TRUE);
	}
	
}


/* End of file		: mcp.crumbly.php */
/* File location	: third_party/crumbly/mcp.crumbly.php */

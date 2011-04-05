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

		$template_groups	= $this->_model->get_all_template_groups();
		$templates_dd		= array();
		$template_groups_dd	= array();

		foreach ($template_groups AS $template_group)
		{
			$template_groups_dd[$template_group->get_group_id()] = $template_group->get_group_name();

			if ( ! $templates = $this->_model->get_templates_by_template_group($template_group->get_group_id()))
			{
				continue;
			}

			$group_templates = array();

			foreach ($templates AS $template)
			{
				if ($template->get_template_name() != 'index')
				{
					$group_templates[$template->get_template_id()] = $template->get_template_name();
				}
			}

			if ( ! $group_templates)
			{
				continue;
			}

			$templates_dd[$template_group->get_group_name()] = $group_templates;
		}
		
		$vars = array(
			'form_action'		=> $this->_base_qs .AMP .'method=run_test',
			'cp_page_title'		=> $this->_ee->lang->line('hd_crumbly_settings'),
			'settings'			=> $this->_model->get_package_settings(),
			'templates'			=> $templates_dd,
			'template_groups'	=> $template_groups_dd
		);
		
		return $this->_ee->load->view('index', $vars, TRUE);
	}
	
}


/* End of file		: mcp.crumbly.php */
/* File location	: third_party/crumbly/mcp.crumbly.php */

<?php if ( ! defined('BASEPATH')) exit('Direct script access is not permitted.');

/**
 * Crumbly module.
 *
 * @author			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright		Experience Internet
 * @package			Crumbly
 */

class Crumbly {
	
	/* --------------------------------------------------------------
	 * PUBLIC PROPERTIES
	 * ------------------------------------------------------------ */
	
	/**
	 * Return data.
	 *
	 * @access	public
	 * @var 	string
	 */
	public $return_data = '';
	
	
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
		$this->_ee->load->model('crumbly_model');
		$this->_model = $this->_ee->crumbly_model;
	}
	
	
		
	
	/* --------------------------------------------------------------
	 * TEMPLATE TAG METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * 'breadcrumbs' template tag.
	 *
	 * @access	public
	 * @return	string
	 */
	public function breadcrumbs()
	{
		// Shortcuts.
		$fns	= $this->_ee->functions;
		$lang	= $this->_ee->lang;
		$tmpl	= $this->_ee->TMPL;
		$uri	= $this->_ee->uri;

		if ( ! $segments = $uri->segment_array())
		{
			// ROOT BREADCRUMB?
			return;
		}

		// Retrieve the module settings.
		$settings = $this->_model->get_settings();

		// Retrieve the tag parameters.
		$include_root	= $tmpl->fetch_param('include_root', 'yes');
		$root_label		= $tmpl->fetch_param('root_label', $lang->line('default_root_label'));
		$root_url		= $tmpl->fetch_param('root_url', $fns->fetch_site_index());


		/**
		 * Build the breadcrumbs array.
		 * Start with a "root" breadcrumb?
		 */

		$breadcrumbs = $include_root
			? array(array('breadcrumb_title' => $root_label, 'url_segment' => ''))
			: array();

		// Template Group.
		$breadcrumbs[] = array(
			'breadcrumb_title'	=> $settings['template_groups'][$segments[0]],
			'url_segment'		=> $segments[0]
		);

		// Template.
		$breadcrumbs[] = array(
			'breadcrumb_title'	=> $settings['templates'][$segments[1]],
			'url_segment'		=> $segments[1]
		);

		// Channel Entry.
		$breadcrumbs[] = array(
			'breadcrumb_title'	=> $this->_model->get_channel_entry_title_from_url_title($segments[2]),
			'url_segment'		=> $segments[2]
		);

		return $this->_ee->TMPL->parse_variables($this->_ee->TMPL->tagdata, $breadcrumbs);
	}
		
}


/* End of file		: mod.crumbly.php */
/* File location	: third_party/crumbly/mod.crumbly.php */

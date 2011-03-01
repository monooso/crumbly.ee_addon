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

		// Breadcrumbs.
		$breadcrumbs = array();

		// Include a 'root' breadcrumb?
		if ($tmpl->fetch_param('include_root', 'yes') == 'yes')
		{
			$breadcrumbs[] = array(
				'breadcrumb_title'	=> $tmpl->fetch_param('root_label', $lang->line('default_root_label')),
				'target_url'		=> $tmpl->fetch_param('root_url', $fns->fetch_site_index())
			);
		}

		// Retrieve the array segments. If there aren't any, we're done.
		if ( ! $segments = $this->_ee->uri->segment_array())
		{
			return $tmpl->parse_variables($tmpl->tagdata, $breadcrumbs);
		}

		// Retrieve the package settings.
		$settings = $this->_model->get_package_settings();

		// What have we got?
		$has_template_group = count($segments) > 0;
		$has_template		= count($segments) > 1;
		$has_entry			= count($segments) > 2;

		if ($has_template_group)
		{
			$template_groups = $settings['template_groups'];

			if (array_key_exists($segments[0], $template_groups))
			{
				$templates	= $template_groups[$segments[0]]['templates'];
				$title		= $template_groups[$segments[0]]['title'];
			}
			else
			{
				$templates	= array();
				$title		= $this->_model->humanize($segments[0]);
			}

			$breadcrumbs[] = array(
				'breadcrumb_segment'	=> $segments[0],
				'breadcrumb_title'		=> $title,
				'breadcrumb_url'		=> $fns->create_url($segments[0])
			);
		}

		// Template.
		if ($has_template)
		{
			$title = array_key_exists($segments[1], $templates)
				? $templates[$segments[1]]
				: $this->_model->humanize($segments[1]);

			$breadcrumbs[] = array(
				'breadcrumb_segment'	=> $segments[1],
				'breadcrumb_title'		=> $settings['template_groups'][$segments[0]]['templates'][$segments[1]],
				'breadcrumb_url'		=> $fns->create_url($segments[0] .'/' .$segments[1])
			);
		}

		// Channel Entry.
		if ($has_entry)
		{
			$breadcrumbs[] = array(
				'breadcrumb_segment'	=> $segments[2],
				'breadcrumb_title'		=> $this->_model->get_channel_entry_title_from_url_title($segments[2]),
				'breadcrumb_url'		=> $fns->create_url($segments[0] .'/' .$segments[1] .'/' .$segments[2])
			);
		}

		return $tmpl->parse_variables($tmpl->tagdata, $breadcrumbs);
	}

}


/* End of file		: mod.crumbly.php */
/* File location	: third_party/crumbly/mod.crumbly.php */

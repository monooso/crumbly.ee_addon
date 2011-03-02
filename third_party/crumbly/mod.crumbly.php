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

		// Retrieve the URL segments. Can't do much without them.
		if ( ! $segments = $this->_ee->uri->segment_array())
		{
			$breadcrumbs = array();
		}
		else
		{
			/**
			 * Are we dealing with a 'standard' URL structure, which may be decyphered automatically,
			 * or a custom user-supplied URL structure.
			 */

			$breadcrumbs = ($url_pattern = $tmpl->fetch_param('url_pattern'))
				? $this->_build_breadcrumbs_from_url_pattern($segments, $url_pattern)
				: $this->_build_standard_breadcrumbs($segments);

		}

		// Include a 'root' breadcrumb?
		if ($tmpl->fetch_param('root_breadcrumb:include', 'yes') == 'yes')
		{
			array_unshift($breadcrumbs, array(
				'breadcrumb_segment'	=> '',
				'breadcrumb_title'		=> $tmpl->fetch_param('root_breadcrumb:label', $lang->line('default_root_label')),
				'breadcrumb_url'		=> $tmpl->fetch_param('root_breadcrumb:url', $fns->fetch_site_index())
			));
		}

		return $tmpl->parse_variables($tmpl->tagdata, $breadcrumbs);
	}



	/* --------------------------------------------------------------
	 * PRIVATE METHODS
	 * ------------------------------------------------------------ */
	
	/**
	 * Builds a breadcrumbs array, based on a custom user-supplied URL structure.
	 *
	 * @access	private
	 * @param	array		$segments		The URL segments.
	 * @param	string		$pattern		The URL pattern.
	 * @return	array
	 */
	private function _build_breadcrumbs_from_url_pattern(Array $segments = array(), $pattern = '')
	{
		// Shortcuts.
		$fns	= $this->_ee->functions;
		$lang	= $this->_ee->lang;
		$tmpl	= $this->_ee->TMPL;
		$uri	= $this->_ee->uri;

		$breadcrumbs		= array();
		$pattern_segments	= explode('/', strtolower($pattern));
		$settings			= $this->_model->get_package_settings();

		/**
		 * TRICKY:
		 * If one of the custom segments is a template, we need to know the parent template group.
		 * To accomplish this, we make a note of the templates from the last-encountered template
		 * group, as we loop through the URL segments.
		 */

		$templates			= array();
		$pattern_total		= count($pattern_segments);
		$segments_thus_far	= array();

		// Deal with each segment in turn.
		for ($segment_count = 0, $segment_total = count($segments); $segment_count < $segment_total; $segment_count++)
		{
			$segment		= $segments[$segment_count];
			$segment_type	= $segment_count < $pattern_total ? $pattern_segments[$segment_count] : 'ignore';

			switch ($segment_type)
			{
				case 'entry':		// url_title or entry_id
					if ( ! $breadcrumb_title = $this->_model->get_channel_entry_title_from_segment($segment))
					{
						$breadcrumb_title = $this->_model->humanize($segment);
					}

					break;

				case 'template':
					$breadcrumb_title = array_key_exists($segment, $templates)
						? $templates[$segment]
						: $this->_model->humanize($segment);

					break;

				case 'template_group':
					if (array_key_exists($segment, $settings['template_groups']))
					{
						$breadcrumb_title	= $settings['template_groups'][$segment]['title'];
						$templates			= $settings['template_groups'][$segment]['templates'];
					}
					else
					{
						$breadcrumb_title	= $this->_model->humanize($segment);
						$templates			= array();
					}

					break;
				
				case 'ignore':		// Ignore segment.
					break;

				case 'glossary':	// Glossary term, falling back to humanised string.
				default:
					$breadcrumb_title = $this->_model->humanize($segment);
					break;
			}

			if ($segment_type == 'ignore')
			{
				continue;
			}

			// Add the breadcrumb.
			$segments_thus_far[]	= $segment;
			$breadcrumbs[]			= array(
				'breadcrumb_segment'	=> $segment,
				'breadcrumb_title'		=> $breadcrumb_title,
				'breadcrumb_url'		=> $fns->create_url(implode('/', $segments_thus_far))
			);
		}

		return $breadcrumbs;
	}


	/**
	 * Builds a breadcrumbs array based on a 'standard' URL structure.
	 *
	 * @access	private
	 * @param	array		$segments		The URL segments.
	 * @return	array
	 */
	private function _build_standard_breadcrumbs(Array $segments = array())
	{
		// Shortcuts.
		$fns	= $this->_ee->functions;
		$lang	= $this->_ee->lang;
		$tmpl	= $this->_ee->TMPL;
		$uri	= $this->_ee->uri;

		// Breadcrumbs.
		$breadcrumbs = array();

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
				'breadcrumb_title'		=> $title,
				'breadcrumb_url'		=> $fns->create_url($segments[0] .'/' .$segments[1])
			);
		}

		// Channel Entry.
		if ($has_entry)
		{
			if ( ! $breadcrumb_title = $this->_model->get_channel_entry_title_from_segment($segments[2]))
			{
				$breadcrumb_title = $this->_model->humanize($segments[2]);
			}
			
			$breadcrumbs[] = array(
				'breadcrumb_segment'	=> $segments[2],
				'breadcrumb_title'		=> $breadcrumb_title,
				'breadcrumb_url'		=> $fns->create_url($segments[0] .'/' .$segments[1] .'/' .$segments[2])
			);
		}

		return $breadcrumbs;
	}

}


/* End of file		: mod.crumbly.php */
/* File location	: third_party/crumbly/mod.crumbly.php */

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
	 * CONSTANTS
	 * ------------------------------------------------------------ */

	/**
	 * Custom URL pattern segment types.
	 *
	 * @access	public
	 * @var		string
	 */
	const CRUMBLY_ENTRY				= 'entry';
	const CRUMBLY_GLOSSARY			= 'glossary';
	const CRUMBLY_IGNORE			= 'ignore';
	const CRUMBLY_TEMPLATE			= 'template';
	const CRUMBLY_TEMPLATE_GROUP	= 'template_group';

	
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

			if ( ! $url_pattern = $tmpl->fetch_param('custom_url:pattern'))
			{
				$url_pattern = 'template_group/template/entry';
			}

			$breadcrumbs = $this->_build_breadcrumbs_from_url_pattern($segments, $url_pattern);
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

		$ignore_trailing	= (strtolower($tmpl->fetch_param('custom_url:ignore_trailing_segments', 'yes')) == 'yes');
		$templates			= array();
		$pattern_total		= count($pattern_segments);
		$segments_thus_far	= array();

		// Deal with each segment in turn.
		for ($segment_count = 0, $segment_total = count($segments); $segment_count < $segment_total; $segment_count++)
		{
			$segment = $segments[$segment_count];

			// How should we handle 'trailing' segments?
			if ($segment_count < $pattern_total)
			{
				$segment_type = $pattern_segments[$segment_count];
			}
			else
			{
				$segment_type = $ignore_trailing ? self::CRUMBLY_IGNORE : self::CRUMBLY_GLOSSARY;
			}

			switch ($segment_type)
			{
				case self::CRUMBLY_ENTRY:
					if ( ! $breadcrumb_title = $this->_model->get_channel_entry_title_from_segment($segment))
					{
						$breadcrumb_title = $this->_model->humanize($segment);
					}

					break;

				case self::CRUMBLY_TEMPLATE:
					$breadcrumb_title = array_key_exists($segment, $templates)
						? $templates[$segment]
						: $this->_model->humanize($segment);

					break;

				case self::CRUMBLY_TEMPLATE_GROUP:
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
				
				case self::CRUMBLY_IGNORE:
					break;

				case self::CRUMBLY_GLOSSARY:
				default:
					$breadcrumb_title = $this->_model->humanize($segment);
					break;
			}

			if ($segment_type == self::CRUMBLY_IGNORE)
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

}


/* End of file		: mod.crumbly.php */
/* File location	: third_party/crumbly/mod.crumbly.php */

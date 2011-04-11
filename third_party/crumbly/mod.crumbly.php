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
	const CRUMBLY_CATEGORY			= 'category';
	const CRUMBLY_CATEGORY_TRIGGER	= 'category_trigger';
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
			 * The segments array, as retrieved from the EE URI class, is 1-based.
			 * For our purposes, this is pointless hassle, so we convert it to a
			 * zero-based array, before proceeding.
			 */

			$segments = array_values($segments);
			
			/**
			 * Are we dealing with a 'standard' URL structure, which may be decyphered automatically,
			 * or a custom user-supplied URL structure.
			 */

			if ( ! $url_pattern = $tmpl->fetch_param('custom_url:pattern'))
			{
				$url_pattern = '';
			}

			$breadcrumbs = $this->_build_breadcrumbs_from_url_pattern($segments, $url_pattern);
		}

		// Include a 'root' breadcrumb?
		if ($tmpl->fetch_param('root_breadcrumb:include', 'yes') == 'yes')
		{
			$lang->loadfile($this->_model->get_package_name());

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
	 * @param	array		$segments		The URL segments. Zero-based.
	 * @param	string		$pattern		The custom URL pattern. Optional.
	 * @return	array
	 */
	private function _build_breadcrumbs_from_url_pattern(Array $segments = array(), $pattern = '')
	{
		// Shortcuts.
		$config	= $this->_ee->config;
		$fns	= $this->_ee->functions;
		$lang	= $this->_ee->lang;
		$tmpl	= $this->_ee->TMPL;
		$uri	= $this->_ee->uri;

		$reserved_category_word = $config->item('reserved_category_word');
		$use_category_name		= (strtolower($config->item('use_category_name')) == 'y' && $reserved_category_word);

		// Additional prep work is required if the pattern is empty (i.e. default).
		if ( ! $pattern)
		{
			$pattern = 'template_group/template/entry';

			// But wait! What about categories?
			if (isset($segments[1]))
			{
				// Swanky /category/category-name categories.
				if ($use_category_name && $segments[1] == $reserved_category_word)
				{
					$pattern = self::CRUMBLY_TEMPLATE_GROUP .'/' .self::CRUMBLY_CATEGORY_TRIGGER .'/' .self::CRUMBLY_CATEGORY;
				}

				// Standard /C12 categories.
				if ( ! $use_category_name && preg_match('/^c[0-9]+$/i', $segments[1]))
				{
					$pattern = self::CRUMBLY_TEMPLATE_GROUP .'/' .self::CRUMBLY_CATEGORY;
				}
			}
		}

		$breadcrumbs			= array();
		$ignore_trailing		= (strtolower($tmpl->fetch_param('custom_url:ignore_trailing_segments', 'yes')) == 'yes');
		$pattern_segments		= explode('/', strtolower($pattern));
		$pattern_total			= count($pattern_segments);
		$segments_thus_far		= array();
		$template_group_segment	= '';

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
				case self::CRUMBLY_CATEGORY:
					if ($crumbly_category = $this->_model->get_crumbly_category_from_segment($segment))
					{
						$breadcrumb_title = $crumbly_category->get_label();
					}
					else
					{
						// Category ID, or category title?
						if ($use_category_name)
						{
							$breadcrumb_title = ($category = $this->_model->get_category_from_cat_url_title($segment))
								? $category->get_cat_name()
								: $this->_model->humanize($segment, FALSE);
						}
						else
						{
							$breadcrumb_title = ($category = $this->_model->get_category_from_cat_id(intval(substr($segment, 1))))
								? $category->get_cat_name()
								: $this->_model->humanize($segment, FALSE);
						}
					}
					break;

				case self::CRUMBLY_CATEGORY_TRIGGER:
					$breadcrumb_title = $this->_model->humanize($segment);
					break;

				case self::CRUMBLY_ENTRY:
					if ( ! $breadcrumb_title = $this->_model->get_channel_entry_title_from_segment($segment))
					{
						$breadcrumb_title = $this->_model->humanize($segment);
					}

					break;

				case self::CRUMBLY_TEMPLATE:
					$breadcrumb_title = ($template = $this->_model->get_crumbly_template_from_segments($template_group_segment, $segment))
						? $template->get_label()
						: $this->_model->humanize($segment);

					break;

				case self::CRUMBLY_TEMPLATE_GROUP:
					$template_group_segment = $segment;

					$breadcrumb_title = ($template_group = $this->_model->get_crumbly_template_group_from_segment($segment))
						? $template_group->get_label()
						: $this->_model->humanize($segment);

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

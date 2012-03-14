<?php if ( ! defined('BASEPATH')) exit('Direct script access is not permitted.');

/**
 * Crumbly module.
 *
 * @author          Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright       Experience Internet
 * @package         Crumbly
 */

class Crumbly {
    
  const CRUMBLY_CATEGORY          = 'category';
  const CRUMBLY_CATEGORY_TRIGGER  = 'category_trigger';
  const CRUMBLY_ENTRY             = 'entry';
  const CRUMBLY_GLOSSARY          = 'glossary';
  const CRUMBLY_IGNORE            = 'ignore';
  const CRUMBLY_TEMPLATE          = 'template';
  const CRUMBLY_TEMPLATE_GROUP    = 'template_group';

  public $return_data = '';
  
  private $EE;
  private $_model;
  
  
  
  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @return  void
   */
  public function __construct()
  {
    $this->EE =& get_instance();
    $this->EE->load->model('crumbly_model');
    $this->_model = $this->EE->crumbly_model;
  }
  
  
      
  /* --------------------------------------------------------------
   * TEMPLATE TAG METHODS
   * ------------------------------------------------------------ */
  
  /**
   * 'breadcrumbs' template tag.
   *
   * @access  public
   * @return  string
   */
  public function breadcrumbs()
  {
    // Shortcuts.
    $config = $this->EE->config;
    $fns    = $this->EE->functions;
    $lang   = $this->EE->lang;
    $tmpl   = $this->EE->TMPL;

    /**
     * The segments array, as retrieved from the EE URI class, is 1-based.
     * For our purposes, this is pointless hassle, so we convert it to a
     * zero-based array, before proceeding.
     */

    $segments       = array_values($this->EE->uri->segment_array());
    $breadcrumbs    = array();

    // Should we reverse the order of the returned breadcrumbs (useful for a 
    // "title trail")?
    $reverse = ($tmpl->fetch_param('breadcrumbs:reverse') == 'yes');

    // Is this a 'Pages' page?
    $pages          = ($site_pages = $config->item('site_pages')) ? $site_pages[$this->_model->get_site_id()]['uris'] : array();
    $page_pattern   = '#^/' .preg_quote(implode('/', $segments), '#') .'/?#i';
    $pages_url      = FALSE;

    foreach ($pages AS $entry_id => $url_title)
    {
      if (preg_match($page_pattern, $url_title))
      {
        $pages_url = TRUE;
        break;
      }
    }

    if ($pages_url)
    {
      $breadcrumbs = $this->_build_breadcrumbs_from_pages_url(
        $segments, $pages, $reverse);
    }
    else
    {
      // Is this a custom user-supplied URL structure?
      $url_pattern = ($url_pattern = $tmpl->fetch_param('custom_url:pattern'))
        ? $url_pattern : '';

      $breadcrumbs = $this->_build_breadcrumbs_from_url_pattern(
        $segments, $url_pattern, $reverse);
    }

    // Include a 'root' breadcrumb?
    if ($tmpl->fetch_param('root_breadcrumb:include', 'yes') == 'yes')
    {
      $lang->loadfile($this->_model->get_package_name());

      array_unshift($breadcrumbs, array(
        'breadcrumb_segment'    => '',
        'breadcrumb_title'      => $tmpl->fetch_param('root_breadcrumb:label', $lang->line('default_root_label')),
        'breadcrumb_url'        => $tmpl->fetch_param('root_breadcrumb:url', $fns->fetch_site_index())
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
   * @access  private
   * @param   array     $segments     The URL segments. Zero-based.
   * @param   string    $pattern      The custom URL pattern. Optional.
   * @param   bool      $reverse      Reverse the breadcrumbs?
   * @return  array
   */
  private function _build_breadcrumbs_from_url_pattern(
    Array $segments = array(), $pattern = '', $reverse = FALSE
  )
  {
    $config = $this->EE->config;
    $fns    = $this->EE->functions;
    $tmpl   = $this->EE->TMPL;

    $reserved_category_word = $config->item('reserved_category_word');
    $use_category_name      = (strtolower($config->item('use_category_name')) == 'y' && $reserved_category_word);
    $auto_pattern           = ! (bool) $pattern;

    if ( ! $pattern)
    {
      $pattern = 'template_group/template/entry';
    }

    $breadcrumbs            = array();
    $ignore_trailing        = (strtolower($tmpl->fetch_param('custom_url:ignore_trailing_segments', 'yes')) == 'yes');
    $pattern_segments       = explode('/', strtolower($pattern));
    $pattern_total          = count($pattern_segments);
    $segments_thus_far      = array();
    $template_group_segment = '';
    $next_segment_is_category = FALSE;

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
        $segment_type = $ignore_trailing
          ? self::CRUMBLY_IGNORE : self::CRUMBLY_GLOSSARY;
      }

      /**
       * Categories within "auto" patterns are tricky, because they
       * can appear in multiple places.
       *
       * We check for category patterns and trigger words after everything
       * else, overriding the segment type if required.
       */

      if ($auto_pattern)
      {
        /**
         * If the previous segment was the category trigger word,
         * we're now looking for a category.
         */

        if ($next_segment_is_category)
        {
          $segment_type = self::CRUMBLY_CATEGORY;
          $next_segment_is_category = FALSE;
        }
    
        if ($use_category_name && $segment == $reserved_category_word)
        {
          $pattern_segments   = array();
          $pattern_total      = 0;
          $segment_type       = self::CRUMBLY_CATEGORY_TRIGGER;
          $next_segment_is_category = TRUE;
        }

        if ( ! $use_category_name && preg_match('/^c[0-9]+$/i', $segment))
        {
          $pattern_segments   = array();
          $pattern_total      = 0;
          $segment_type       = self::CRUMBLY_CATEGORY;
        }
      }

      switch ($segment_type)
      {
        case self::CRUMBLY_CATEGORY:
          $breadcrumb_title = ($category = $this->_model->get_category_from_segment($segment))
            ? $category->get_cat_name()
            : $this->_model->humanize($segment, FALSE);
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
      $segments_thus_far[]  = $segment;
      $breadcrumbs[]        = array(
        'breadcrumb_segment'  => $segment,
        'breadcrumb_title'    => $breadcrumb_title,
        'breadcrumb_url'  => $fns->create_url(implode('/', $segments_thus_far))
      );
    }

    if ($reverse === TRUE)
    {
      $breadcrumbs = array_reverse($breadcrumbs);
    }

    return $breadcrumbs;
  }


  /**
   * Builds the breadcrumbs array for a 'Pages' URL.
   *
   * @access    public
   * @param     array     $segments     The URL segments.
   * @param     array     $pages        The 'Pages' for this site.
   * @param     bool      $reverse      Reverse the breadcrumbs?
   * @return    array
   */
  public function _build_breadcrumbs_from_pages_url(Array $segments = array(),
    Array $pages = array(), $reverse = FALSE
  )
  {
    $tmpl               = $this->EE->TMPL;
    $breadcrumbs        = array();
    $segments_thus_far  = array();

    $include_unassigned = (strtolower(
      $tmpl->fetch_param('pages:include_unassigned', 'no')) == 'yes');

    foreach ($segments AS $segment)
    {
      $segments_thus_far[] = $segment;
      $page_found = FALSE;

      $current_url = $this->EE->functions->create_url(
        implode('/', $segments_thus_far));

      $pattern = '#^/'
        .preg_quote(implode('/', $segments_thus_far), '#')
        .'/?' .'$#i';

      foreach ($pages AS $entry_id => $url_title)
      {
        if (preg_match($pattern, $url_title))
        {
          $breadcrumb_title
            = $this->_model->get_channel_entry_title_from_segment($entry_id);

          if ( ! $breadcrumb_title)
          {
            $breadcrumb_title = $this->_model->humanize($segment);
          }

          $breadcrumbs[] = array(
            'breadcrumb_segment'  => $segment,
            'breadcrumb_title'    => $breadcrumb_title,
            'breadcrumb_url'      => $current_url
          );

          $page_found = TRUE;
          continue;
        }
      }

      if ( ! $page_found && $include_unassigned)
      {
        $breadcrumbs[] = array(
          'breadcrumb_segment'  => $segment,
          'breadcrumb_title'    => $this->_model->humanize($segment),
          'breadcrumb_url'      => $current_url
        );
      }
    }

    if ($reverse === TRUE)
    {
      $breadcrumbs = array_reverse($breadcrumbs);
    }

    return $breadcrumbs;
  }


}


/* End of file      : mod.crumbly.php */
/* File location    : third_party/crumbly/mod.crumbly.php */

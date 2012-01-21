<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * Crumbly module control panel.
 *
 * @author      Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright   Experience Internet
 * @package     Crumbly */

class Crumbly_mcp {

  private $_ee;
  private $_model;
  private $_theme_url;
  
  
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

    // Load the model.
    $this->EE->load->model('crumbly_model');
    $this->_model = $this->EE->crumbly_model;

    // Basic stuff required by every view.
    $this->_base_qs   = 'C=addons_modules' .AMP .'M=show_module_cp' .AMP .'module=' .$this->_model->get_package_name();
    $this->_base_url  = BASE .AMP .$this->_base_qs;
    $this->_theme_url = $this->_model->get_package_theme_url();
    
    $this->EE->load->helper('form');
    $this->EE->load->library('table');

    $this->EE->cp->set_breadcrumb($this->_base_url, $this->EE->lang->line('crumbly_module_name'));
    $this->EE->cp->add_to_foot('<script type="text/javascript" src="' .$this->_theme_url .'js/libs/jquery.roland.js"></script>');
    $this->EE->cp->add_to_foot('<script type="text/javascript" src="' .$this->_theme_url .'js/cp.js"></script>');
    $this->EE->javascript->compile();

    $this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="' .$this->_theme_url .'css/cp.css" />');

    $nav_array = array(
      'nav_glossary'    => $this->_base_url .AMP .'method=glossary',
      'nav_templates'   => $this->_base_url .AMP .'method=templates',
      'nav_template_groups' => $this->_base_url .AMP .'method=template_groups'
    );

    $this->EE->cp->set_right_nav($nav_array);
  }


  /**
   * Glossary.
   *
   * @access  public
   * @return  string
   */
  public function glossary()
  {
    $vars = array(
      'form_action'   => $this->_base_qs .AMP .'method=save_glossary',
      'cp_page_title' => $this->EE->lang->line('hd_glossary'),
      'glossary'      => $this->_model->get_all_crumbly_glossary_terms(),
      'theme_url'     => $this->_theme_url
    );
    
    return $this->EE->load->view('glossary', $vars, TRUE);
  }
  
  
  /**
   * Module index page.
   *
   * @access  public
   * @return  string
   */
  public function index()
  {
    return $this->glossary();
  }


  /**
   * Saves the Crumbly glossary terms.
   *
   * @access  public
   * @return  void
   */
  public function save_glossary()
  {
    $glossary_input = $this->EE->input->post('glossary');

    if ( ! is_array($glossary_input))
    {
      $glossary_input = array();
    }

    $this->_model->delete_all_crumbly_glossary_terms();

    $success = TRUE;

    foreach ($glossary_input AS $term_data)
    {
      if ( ! $this->_model->save_crumbly_glossary_term(new Crumbly_glossary_term($term_data)))
      {
        $success = FALSE;
      }
    }

    $success
      ? $this->EE->session->set_flashdata('message_success', $this->EE->lang->line('msg_glossary_terms_saved'))
      : $this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('msg_glossary_terms_not_saved'));

    $this->EE->functions->redirect($this->_base_url .AMP .'method=glossary');
  }



  /**
   * Saves the Crumbly templates.
   *
   * @access  public
   * @return  void
   */
  public function save_templates()
  {
    $templates_input = $this->EE->input->post('templates');

    if ( ! is_array($templates_input))
    {
      $templates_input = array();
    }

    $this->_model->delete_all_crumbly_templates();

    $success = TRUE;

    foreach ($templates_input AS $template_data)
    {
      if ( ! $this->_model->save_crumbly_template(new Crumbly_template($template_data)))
      {
        $success = FALSE;
      }
    }

    $success
      ? $this->EE->session->set_flashdata('message_success', $this->EE->lang->line('msg_templates_saved'))
      : $this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('msg_templates_not_saved'));

    $this->EE->functions->redirect($this->_base_url .AMP .'method=templates');
  }


  /**
   * Saves the Crumbly template groups.
   *
   * @access  public
   * @return  void
   */
  public function save_template_groups()
  {
    $groups_input = $this->EE->input->post('template_groups');

    if ( ! is_array($groups_input))
    {
      $groups_input = array();
    }

    $this->_model->delete_all_crumbly_template_groups();

    $success = TRUE;

    foreach ($groups_input AS $group_data)
    {
      if ( ! $this->_model->save_crumbly_template_group(new Crumbly_template_group($group_data)))
      {
        $success = FALSE;
      }
    }

    $success
      ? $this->EE->session->set_flashdata('message_success', $this->EE->lang->line('msg_template_groups_saved'))
      : $this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('msg_template_groups_not_saved'));

    $this->EE->functions->redirect($this->_base_url .AMP .'method=template_groups');
    
  }


  /**
   * Templates.
   *
   * @access  public
   * @return  string
   */
  public function templates()
  {
    $template_groups  = $this->_model->get_all_template_groups();
    $templates_dd   = array('' => $this->EE->lang->line('lbl_template_prompt'));

    // Prepare the drop down options arrays.
    foreach ($template_groups AS $template_group)
    {
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
      'form_action'   => $this->_base_qs .AMP .'method=save_templates',
      'cp_page_title' => $this->EE->lang->line('hd_templates'),
      'templates'     => $this->_model->get_all_crumbly_templates(),
      'templates_dd'  => $templates_dd,
      'theme_url'     => $this->_theme_url
    );
    
    return $this->EE->load->view('templates', $vars, TRUE);
  }


  /**
   * Templates groups.
   *
   * @access  public
   * @return  string
   */
  public function template_groups()
  {
    $template_groups  = $this->_model->get_all_template_groups();
    $template_groups_dd = array('' => $this->EE->lang->line('lbl_template_group_prompt'));

    // Prepare the drop down options arrays.
    foreach ($template_groups AS $template_group)
    {
      $template_groups_dd[$template_group->get_group_id()] = $template_group->get_group_name();
    }
    
    $vars = array(
      'form_action'         => $this->_base_qs .AMP .'method=save_template_groups',
      'cp_page_title'       => $this->EE->lang->line('hd_template_groups'),
      'template_groups'     => $this->_model->get_all_crumbly_template_groups(),
      'template_groups_dd'  => $template_groups_dd,
      'theme_url'           => $this->_theme_url
    );
    
    return $this->EE->load->view('template_groups', $vars, TRUE);
  }
  
}


/* End of file    : mcp.crumbly.php */
/* File location  : third_party/crumbly/mcp.crumbly.php */

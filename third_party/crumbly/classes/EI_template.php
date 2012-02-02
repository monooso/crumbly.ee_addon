<?php

/**
 * EI template.
 *
 * @author      Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright   Experience Internet
 * @package     EI
 */

require_once PATH_THIRD .'crumbly/helpers/EI_number_helper.php';

class EI_template {

  private $_group_id;
  private $_template_id;
  private $_template_name;


  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */
  
  /**
   * Constructor.
   *
   * @access  public
   * @param array   $props    Instance properties.
   * @return  void
   */
  public function __construct(Array $props = array())
  {
    $this->reset();

    foreach ($props AS $key => $val)
    {
      $method_name = 'set_' .$key;

      if (method_exists($this, $method_name))
      {
        $this->$method_name($val);
      }
    }
  }


  /**
   * Returns the template group ID.
   *
   * @access  public
   * @return  int
   */
  public function get_group_id()
  {
    return $this->_group_id;
  }


  /**
   * Returns template ID.
   *
   * @access  public
   * @return  int
   */
  public function get_template_id()
  {
    return $this->_template_id;
  }


  /**
   * Returns the template name.
   *
   * @access  public
   * @return  string
   */
  public function get_template_name()
  {
    return $this->_template_name;
  }


  /**
   * Resets the instance properties.
   *
   * @access  public
   * @return  EI_template
   */
  public function reset()
  {
    $this->_group_id    = 0;
    $this->_template_id   = 0;
    $this->_template_name = '';

    return $this;
  }


  /**
   * Sets the template group ID.
   *
   * @access  public
   * @param int|string    $group_id   The template group ID.
   * @return  int
   */
  public function set_group_id($group_id)
  {
    if (valid_int($group_id, 1))
    {
      $this->_group_id = intval($group_id);
    }

    return $this->get_group_id();
  }


  /**
   * Sets template ID>
   *
   * @access  public
   * @param int   $template_id    The template ID.
   * @return  int
   */
  public function set_template_id($template_id)
  {
    if (valid_int($template_id, 1))
    {
      $this->_template_id = intval($template_id);
    }

    return $this->get_template_id();
  }


  /**
   * Sets the template name.
   *
   * @access  public
   * @param string    $template_name    The template name.
   * @return  string
   */
  public function set_template_name($template_name)
  {
    if (is_string($template_name))
    {
      $this->_template_name = $template_name;
    }

    return $this->get_template_name();
  }


  /**
   * Returns the instance properties as an associative array.
   *
   * @access  public
   * @return  array
   */
  public function to_array()
  {
    return array(
      'group_id'      => $this->get_group_id(),
      'template_id'   => $this->get_template_id(),
      'template_name'   => $this->get_template_name()
    );
  }
  
}

/* End of file    : EI_template.php */
/* File location  : third_party/crumbly/classes/EI_template.php */

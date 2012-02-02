<?php

/**
 * Crumbly template.
 *
 * @author      Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright   Experience Internet
 * @package     Crumbly
 */

require_once PATH_THIRD .'crumbly/helpers/EI_number_helper.php';

class Crumbly_template {

  private $_label;
  private $_template_id;


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
   * Returns the label.
   *
   * @access  public
   * @return  string
   */
  public function get_label()
  {
    return $this->_label;
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
   * Resets the instance properties.
   *
   * @access  public
   * @return  Crumbly_template
   */
  public function reset()
  {
    $this->_label   = '';
    $this->_template_id = 0;

    return $this;
  }
  
  
  /**
   * Sets the label.
   *
   * @access  public
   * @param string    $label    The label.
   * @return  string
   */
  public function set_label($label)
  {
    if (is_string($label))
    {
      $this->_label = $label;
    }

    return $this->get_label();
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
   * Returns the instance properties as an associative array.
   *
   * @access  public
   * @return  array
   */
  public function to_array()
  {
    return array(
      'label'     => $this->get_label(),
      'template_id' => $this->get_template_id()
    );
  }
  
}

/* End of file    : crumbly_template.php */
/* File location  : third_party/crumbly/classes/crumbly_template.php */

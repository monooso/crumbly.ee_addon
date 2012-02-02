<?php

/**
 * Crumbly template group.
 *
 * @author      Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright   Experience Internet
 * @package     Crumbly
 */

require_once PATH_THIRD .'crumbly/helpers/EI_number_helper.php';

class Crumbly_template_group {

  private $_group_id;
  private $_label;


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
   * Returns template group ID.
   *
   * @access  public
   * @return  int
   */
  public function get_group_id()
  {
    return $this->_group_id;
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
   * Resets the instance properties.
   *
   * @access  public
   * @return  Crumbly_template
   */
  public function reset()
  {
    $this->_group_id  = 0;
    $this->_label   = '';

    return $this;
  }
  
  
  /**
   * Sets template group ID.
   *
   * @access  public
   * @param int   $group_id   The template group ID.
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
   * Returns the instance properties as an associative array.
   *
   * @access  public
   * @return  array
   */
  public function to_array()
  {
    return array(
      'group_id'  => $this->get_group_id(),
      'label'   => $this->get_label()
    );
  }
  
}

/* End of file    : crumbly_template.php */
/* File location  : third_party/crumbly/classes/crumbly_template.php */

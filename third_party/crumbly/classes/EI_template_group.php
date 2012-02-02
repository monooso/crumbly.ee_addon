<?php

/**
 * EI template group.
 *
 * @author      Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright   Experience Internet
 * @package     EI
 */

require_once PATH_THIRD .'crumbly/helpers/EI_number_helper.php';

class EI_template_group {

  private $_group_id;
  private $_group_name;


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
   * Returns the template group name.
   *
   * @access  public
   * @return  string
   */
  public function get_group_name()
  {
    return $this->_group_name;
  }


  /**
   * Resets the instance properties.
   *
   * @access  public
   * @return  EI_template
   */
  public function reset()
  {
    $this->_group_id  = 0;
    $this->_group_name  = '';

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
   * Sets the template group name.
   *
   * @access  public
   * @param string    $group_name   The template group name.
   * @return  string
   */
  public function set_group_name($group_name)
  {
    if (is_string($group_name))
    {
      $this->_group_name = $group_name;
    }

    return $this->get_group_name();
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
      'group_id'    => $this->get_group_id(),
      'group_name'  => $this->get_group_name()
    );
  }
  
}

/* End of file    : EI_template_group.php */
/* File location  : third_party/crumbly/classes/EI_template_group.php */

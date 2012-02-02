<?php

/**
 * EI category.
 *
 * @author      Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright   Experience Internet
 * @package     EI
 */

require_once PATH_THIRD .'crumbly/helpers/EI_number_helper.php';

class EI_category {

  private $_cat_id;
  private $_cat_name;
  private $_cat_url_title;


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
   * Returns the category ID.
   *
   * @access  public
   * @return  int
   */
  public function get_cat_id()
  {
    return $this->_cat_id;
  }


  /**
   * Returns the category name.
   *
   * @access  public
   * @return  string
   */
  public function get_cat_name()
  {
    return $this->_cat_name;
  }


  /**
   * Returns the category URL title.
   *
   * @access  public
   * @return  string
   */
  public function get_cat_url_title()
  {
    return $this->_cat_url_title;
  }
  
  
  /**
   * Resets the instance properties.
   *
   * @access  public
   * @return  EI_template
   */
  public function reset()
  {
    $this->_cat_id      = 0;
    $this->_cat_name    = '';
    $this->_cat_url_title = '';

    return $this;
  }


  /**
   * Sets the category ID.
   *
   * @access  public
   * @param int|string    $cat_id   The category ID.
   * @return  int
   */
  public function set_cat_id($cat_id)
  {
    if (valid_int($cat_id, 1))
    {
      $this->_cat_id = intval($cat_id);
    }

    return $this->get_cat_id();
  }


  /**
   * Sets the category name.
   *
   * @access  public
   * @param string    $cat_name   The category name.
   * @return  string
   */
  public function set_cat_name($cat_name)
  {
    if (is_string($cat_name))
    {
      $this->_cat_name = $cat_name;
    }

    return $this->get_cat_name();
  }
  
  
  /**
   * Sets the category URL title.
   *
   * @access  public
   * @param string    $cat_url_title    The category URL title.
   * @return  string
   */
  public function set_cat_url_title($cat_url_title)
  {
    if (preg_match('/^[a-z0-9_\-]*$/i', $cat_url_title))
    {
      $this->_cat_url_title = $cat_url_title;
    }

    return $this->get_cat_url_title();
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
      'cat_id'    => $this->get_cat_id(),
      'cat_name'    => $this->get_cat_name(),
      'cat_url_title' => $this->get_cat_url_title()
    );
  }
  
}

/* End of file    : EI_category.php */
/* File location  : third_party/crumbly/classes/EI_category.php */

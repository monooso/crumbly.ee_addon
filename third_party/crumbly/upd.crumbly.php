<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * Crumbly module update.
 *
 * @author      Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright   Experience Internet
 * @package     Crumbly
 */

class Crumbly_upd {
  
  public $version;
  
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

    // We need to explicitly set the package path.
    $this->EE->load->add_package_path(PATH_THIRD .'crumbly/');
    $this->EE->load->model('crumbly_model');

    $this->_model   = $this->EE->crumbly_model;
    $this->version  = $this->_model->get_package_version();
  }
  
  
  /**
   * Installs the module.
   *
   * @access  public
   * @return  bool
   */
  public function install()
  {
    return $this->_model->install_module();
  }


  /**
   * Uninstalls the module.
   *
   * @access  public
   * @return  bool
   */
  public function uninstall()
  {
    return $this->_model->uninstall_module();
  }


  /**
   * Updates the module.
   *
   * @access  public
   * @param string    $installed_version    The installed version.
   * @return  bool
   */
  public function update($installed_version = '')
  {
    return $this->_model->update_module($installed_version);
  }


}


/* End of file    : upd.crumbly.php */
/* File location  : third_party/crumbly/upd.crumbly.php */

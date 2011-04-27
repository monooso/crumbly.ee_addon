<?php

/**
 * Mock Crumbly model.
 *
 * @author			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright		Experience Internet
 * @package			Crumbly
 */

class Mock_crumbly_model {

	/* --------------------------------------------------------------
	 * PUBLIC METHODS
	 * ------------------------------------------------------------ */

	public function delete_all_crumbly_categories() {}
	public function delete_all_crumbly_glossary_terms() {}
	public function delete_all_crumbly_templates() {}
	public function delete_all_crumbly_template_groups() {}
	public function get_all_categories() {}
	public function get_category_from_cat_id($cat_id) {}
	public function get_category_from_cat_url_title($cat_url_title) {}
	public function get_category_from_segment($segment) {}
	public function get_channel_entry_title_from_segment($segment) {}
	public function get_crumbly_template_from_segments($group_segment, $template_segment) {}
	public function get_crumbly_template_group_from_segment($segment) {}
	public function get_package_name() {}
	public function get_package_settings() {}
	public function get_package_theme_url() {}
	public function get_template_groups() {}
	public function humanize($machine = '', $use_glossary = TRUE) {}
	public function save_crumbly_category(Crumbly_category $category) {}
	public function save_crumbly_glossary_term(Crumbly_glossary_term $glossary_term) {}
	public function save_crumbly_template(Crumbly_template $template) {}
	public function save_crumbly_template_group(Crumbly_template_group $template_group) {}

}

/* End of file		: mock.crumbly_model.php */
/* File location	: third_party/crumbly/tests/mocks/mock.crumbly_model.php */

<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * PyroRoutes Details File
 *
 * @package  	PyroCMS
 * @subpackage  PyroChunks
 * @category  	Details
 * @author  	Parse19 (Adam Fairholm)
 * @modified    el2ro
 */
class Module_Droutes extends Module {

	public $version = '1.0';

	public $db_pre;

 	// --------------------------------------------------------------------------

	public function __construct()
	{
		if(CMS_VERSION >= 1.3)
			$this->db_pre = SITE_REF.'_';
	}

	// --------------------------------------------------------------------------

 	public function info()
	{
		return array(
		    'name' => array(
		        'en' => 'Dynamic Routes'
		    ),
		    'description' => array(
		        'en' => 'Manage dynamic routes.'
		    ),
			'frontend' => false,
			'backend' => true,
			'menu' => 'utilities',
			'author' => 'el2ro',
			'shortcuts' => array(
				array(
				    'name' => 'droutes.routes',
				    'uri' => 'admin/droutes',
				),
				array(
				    'name' => 'droutes.new_route',
				    'uri' => 'admin/droutes/new_route',
				)
		    )
		);
	}

	// --------------------------------------------------------------------------

	public function install()
	{
		$sql = "
			CREATE TABLE IF NOT EXISTS `{$this->db_pre}droutes` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(100) NOT NULL,
			`group_id` int(11) DEFAULT NULL,
			`route_key` varchar(200) NOT NULL,
			`route_value` varchar(200) NOT NULL,
			`when_added` datetime DEFAULT NULL,
			`last_updated` datetime DEFAULT NULL,
			`added_by` int(11) DEFAULT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `route_key - unique` (`route_key`),
			UNIQUE KEY `route_value - unique` (`route_value`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

		return $this->db->query($sql);
	}

	// --------------------------------------------------------------------------

	public function uninstall()
	{
		$this->load->dbforge();

		return $this->dbforge->drop_table('droutes');
	}

	// --------------------------------------------------------------------------

	public function upgrade($old_version)
	{
		return true;
	}

	// --------------------------------------------------------------------------

	public function help()
	{
		return "No documentation has been added for this module.<br/>Contact the module developer for assistance.";
	}
}

/* End of file details.php */
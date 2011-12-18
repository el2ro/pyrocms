<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Route_settings extends CI_Migration {

	public function up()
	{
		$this->db->insert('settings', array(
			'slug'			=> 'blog_route_settings',
			'title'			=> 'Blog Route Settings',
			'description'	=> 'Change routing settings for the Blog',
			'`default`' 	=> 'Default',
			'`value`'		=> '0',
			'type'			=> 'select',
			'`options`'		=> '0=/blog/{date}/{blog}|1=/blog/{category}/{blog}|2=/{category}/{blog}',
			'is_required'	=> 1,
			'is_gui' 		=> 1,
			'module' 		=> 'blog'
		));

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

	public function down()
	{
		$this->load->dbforge();

		$this->db->delete('settings', array('slug' => 'blog_route_settings'));
		return $this->dbforge->drop_table('droutes');
	}
}
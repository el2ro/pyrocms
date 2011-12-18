<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Blog events Class
 *
 * @package     PyroCMS
 * @subpackage  Blog
 * @category    events
 * @author      PyroCMS Dev Team
 */
class Events_blog
{

	public function __construct()
	{
		$this->ci =& get_instance();

		// register the public_controller event when this file is autoloaded
		Events::register('settings_changed', array($this, 'settings_changed'));
	}

	// If route settings changed call route change to all blog posts
	public function settings_changed($slugs)
	{
		if (in_array('blog_route_settings',$slugs))
		{
			$route_setting = Settings::get('blog_route_settings');
			ci()->load->model('blog/blog_m');
			ci()->load->helper('blog/blog');
			ci()->load->library('droutes');

			$blogs = ci()->blog_m->get_many_by(array('status' => 'live'));

			foreach($blogs as $blog)
			{
				ci()->droutes->change(
					array('name'=>'blog',
						  'group_id'=>$blog->id,
						  'route_key'=>get_post_url($blog->id, $blog->slug, $blog->created_on, $blog->category_id),
						  'route_value'=>'blog/view/id/'.$blog->id
						  ));
			}
		}
	}
}
/* End of file events.php */
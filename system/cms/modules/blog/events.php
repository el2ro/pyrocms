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

		ci()->load->library('droutes');

		// register the public_controller event when this file is autoloaded
		Events::register('settings_changed', array($this, 'settings_changed'));

		Events::register('blog_article_published',array($this, 'blog_article_published'));
		Events::register('blog_article_unpublished',array($this, 'blog_article_unpublished'));
		Events::register('blog_article_changed',array($this, 'blog_article_changed'));
		Events::register('blog_article_deleted',array($this, 'blog_article_deleted'));
		Events::register('blog_category_created',array($this, 'blog_category_created'));
		Events::register('blog_category_changed',array($this, 'blog_category_changed'));
		Events::register('blog_category_deleted',array($this, 'blog_category_deleted'));
		Events::register('blog_category_check_title',array($this, 'blog_category_check_title'));
	}

	public function blog_article_published($id = 0)
	{
		//ci()->load->library('droutes');
		ci()->load->helper('blog');
		ci()->load->model('blog/blog_m');
		ci()->load->model('redirects/redirect_m');

		$post = ci()->blog_m->get($id);
		$post_url = get_post_url($id, $post->slug, $post->created_on, $post->category_id);

		//Check if redirect exists from current route and remove it
		if ($redirects = ci()->redirect_m->get_many_by(array('from'=>$post_url)))
		{
			foreach ($redirects as $redirect)
			{
				ci()->redirect_m->delete($redirect->id);
			}
		}
	}

	public function blog_article_unpublished($id = 0)
	{
		ci()->load->helper('blog');
		ci()->load->model('blog/blog_m');
		ci()->load->model('redirects/redirect_m');

		$post = ci()->blog_m->get($id);
		$post_url = get_post_url($id, $post->slug, $post->created_on, $post->category_id);

		// Unpublished will be redirected to home (Should it be to category home?)
		ci()->redirect_m->insert(
			array('from'=>$post_url,
					'to'=>'/'));
	}

	public function blog_article_changed($params)
	{
		ci()->load->helper('blog');
		ci()->load->model('blog/blog_m');
		ci()->load->model('redirects/redirect_m');

		$post = ci()->blog_m->get($params['id']);

		//Check if it has effect to uri
		if($post->slug != $params['old']->slug or $post->category_id != $params['old']->category_id)
		{
			$post_url = get_post_url($id, $post->slug, $post->created_on, $post->category_id);
			$old_post_url = get_post_url($id, $params['old']->slug, $params['old']->created_on, $params['old']->category_id);

			//Add redirect from OLD uri to new one
			ci()->redirect_m->insert(
					array('from'=>$old_post_url,
							'to'=>$post_url));

			//Check if redirect exists from current route and remove it
			if ($redirects = ci()->redirect_m->get_many_by(array('from'=>$post_url)))
			{
				foreach ($redirects as $redirect)
				{
					ci()->redirect_m->delete($redirect->id);
				}
			}
		}
	}

	public function blog_article_deleted($id)
	{
		//TODO redirect to home
		ci()->load->helper('blog');
		ci()->load->model('blog/blog_m');
		ci()->load->model('redirects/redirect_m');

		$post = ci()->blog_m->get($params['id']);
		$post_url = get_post_url($id, $post->slug, $post->created_on, $post->category_id);

		ci()->redirect_m->insert(
			array('from'=>$post_url,
					'to'=>'/'));
	}

	/*
	 * New category created
	 */
	public function blog_category_created($id)
	{
		ci()->load->helper('blog/blog');
		ci()->load->model(array('blog/blog_categories_m'));

		$new_category = ci()->blog_categories_m->get($id);
		ci()->droutes->add(
			array('name'=>'blog_category',
				'group_id'=>$id,
				'route_key'=>$this->get_category_url($new_category->slug),
				'route_value'=>'blog/category/id/'.$id
				));

		ci()->droutes->change(
			array('name'=>'blog',
				'group_id'=>$id,
				'route_key'=>$this->get_category_url($new_category->slug).'/(:any)',
				'route_value'=>'blog/view/$1'
				));
	}

	/*
	 * Blog Category Changed
	 *
	 * Set routing to point new category index
	 * Set routing to point blog posts to point under category
	 *
	 * Set redirect to old category to new one
	 * Set redirect point old blog post urls to point to new ones
	 *
	 */
	public function blog_category_changed($params)
	{
		ci()->load->helper('blog/blog');
		ci()->load->model(array('blog/blog_categories_m'));

		//TODO Check settings for routing
		$new_category = ci()->blog_categories_m->get($params['id']);
		ci()->droutes->change(
			array('name'=>'blog_category',
				'group_id'=>$params['id'],
				'route_key'=>$this->get_category_url($new_category->slug),
				'route_value'=>'blog/category/id/'.$params['id']
				));

		ci()->droutes->change(
			array('name'=>'blog',
				'group_id'=>$params['id'],
				'route_key'=>$this->get_category_url($new_category->slug).'/(:any)',
				'route_value'=>'blog/view/$1'
				));
	}

	/*
	 * Blog Category Deleted
	 */
	public function blog_category_deleted($id)
	{
		ci()->droutes->delete(
		array('name'=>'blog_category',
			  'group_id'=>$id
			  ));

		ci()->droutes->delete(
		array('name'=>'blog',
			  'group_id'=>$id
			  ));
	}

	/*
	 * Check that category slug is not overrapping with pages
	 */
	public function blog_category_check_title($id)
	{
		return TRUE;
	}

	// If Route Settings have been changed call route change to all blog posts
	public function settings_changed($slugs)
	{
//This needs to be redone
/*
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
*/
	}
	/**
	 * Return a users display name based on settings
	 *
	 * @param int $user the users id
	 * @return  string
	 */
	function get_category_url($slug)
	{
		switch (Settings::get('blog_route_settings')) {
			case 0:
				$url = 'blog/category/'.$slug;
				break;
			case 1:
				$url = 'blog/'.$slug;
				break;
			case 2:
				$url = $slug;
				break;
			default:
		}
		return $url;
	}
}
/* End of file events.php */
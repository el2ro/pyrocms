<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Blog Helpers
 *
 * @package		PyroCMS
 * @subpackage	Blog
 * @category	Helpers
 * @author		PyroCMS Dev Team
 */
// ------------------------------------------------------------------------

/*
0=/blog/{date}/{blog}
1=/blog/{category}/{blog}
2=/{category}/{blog}
*/

/**
 * Return a users display name based on settings
 *
 * @param int $user the users id
 * @return  string
 */
function get_post_url($id, $post_slug, $created_on, $category_id, $category_slug=null)
{

	switch (Settings::get('blog_route_settings')) {
		case 0:
			ci()->load->helper('date');
			$url = 'blog/'. date('Y/m', $created_on).'/'.$post_slug;
			break;
		case 1:
			$url = 'blog/' . ($category_slug ? $category_slug : get_category_slug($category_id)) .'/'. $post_slug;
			break;
		case 2:
			$url = ($category_slug ? $category_slug : get_category_slug($category_id)) .'/'. $post_slug;
			break;
		default:
	}
	return $url;
}

function get_category_slug($category_id)
{
	if ($category_id)
	{	ci()->load->model('blog/blog_categories_m');
		$category = ci()->blog_categories_m->get($category_id);
		return $category->slug;
	}
	else
	{
		//Hard coded value, if no category set to blog
		return 'no-category';
	}
}

/* End of file blog/helpers/blog_helper.php */

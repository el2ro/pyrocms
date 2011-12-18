<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dynamic Router Class
 *
 * Dynamic Router class for CodeIgniter
 *
 * @category	Libraries
 * @author		el2ro
 * @link
 * @license		MIT
 * @version		1.0
 */

class Droutes
{
	/**
	 * Constructor - Initializes
	 */
	function __construct()
	{
		log_message('debug', "Drouter Class Initialized.");
	}

	/**
	 * Add route to routing table
	 *
	 * @access	public
	 * @param	array
	 *				name			= route name (group)
	 *				group_id 	= item number inside group
	 *				route_key	= route source
	 *				route_value	= route destination
	 * @param   bool = redirects will be set / fixed
	 * @return	void
	 */
	public function add($route, $check_redirect=true)
	{
		ci()->load->model('droutes/droutes_m');
		ci()->load->model('redirects/redirect_m');

		$route['last_updated'] = date('Y-m-d H:i:s');
		ci()->droutes_m->insert($route);

		//Check if redirect exists from current route and remove it
		if ($check_redirect && $redirects = ci()->redirect_m->get_many_by(array('from'=>$route->route_key)))
		{
			foreach ($redirects as $redirect)
			{
				ci()->redirect_m->delete($redirect->id);
			}
		}
	}

	/**
	 * Change dynamic routing
	 *
	 * Look existing route based on name and group_id. Change to according routes.
	 *
	 * Old route will be redirected to new route.
	 * Older redirects will be corrected to point the new route destination.
	 *
	 * @access	public
	 * @param	array
	 *				name			= route name (group)
	 *				group_id 	= item number inside group
	 *				route_key	= route source
	 *				route_value	= route destination
	 * @param   bool = redirects will be set / fixed
	 * @return	void
	 */
	public function change($route, $check_redirect=true)
	{
		ci()->load->model('droutes/droutes_m');
		ci()->load->model('redirects/redirect_m');

		//Get old route
		if ($old_route = ci()->droutes_m->get_by(array('name'=>$route['name'],'group_id'=>$route['group_id'])))
		{
			// Add redirect from old url to new one
			$check_redirect and ci()->redirect_m->insert(array('from'=>$old_route->route_key, 'to'=>$route['route_key']));

			// Change old route to new one
			ci()->droutes_m->update($old_route->id, $route);

			//Check if there are redirects pointing to changed uri
			if($check_redirect && $redirects = ci()->redirect_m->get_many_by(array('to'=>$old_route->route_key)))
			{
				foreach($redirects as $redirect)
				{
					ci()->redirect_m->update($redirect->id, array('from'=>$redirect->from, 'to'=>$route['route_key']));
				}
			}

			//Check if redirect exists from current route and remove it
			if ($check_redirect && $redirects = ci()->redirect_m->get_many_by(array('from'=>$route['route_key'])))
			{
				foreach ($redirects as $redirect)
				{
					ci()->redirect_m->delete($redirect->id);
				}
			}
		}
		else
		{
			//Old route did not found so lets create new one then
			$this->add($route, $check_redirect);
		}
	}

	/**
	 * Delete route
	 * and redirect deleted pages to home
	 *
	 * @access	public
	 * @param	array
	 *				name		= route name (group)
	 *				group_id 	= item number inside group
	 * @param   bool = redirects will be set to home
	 * @return	void
	 */
	public function delete($route, $check_redirect=true)
	{
		ci()->load->model('droutes/droutes_m');
		ci()->load->model('redirects/redirect_m');

		if ($old_route = ci()->droutes_m->get_by(array('name'=>$route['name'],'group_id'=>$route['group_id'])))
		{
			// Add redirect from old url to front page
			$check_redirect and ci()->redirect_m->insert(array('from'=>$old_route->route_key, '/'));

			//Change also old routes to point
			if($check_redirect && $redirects = ci()->redirect_m->get_many_by(array('to'=>$old_route->route_key)))
			{
				foreach($redirects as $redirect)
				{
					ci()->redirect_m->update($redirect->id, array('from'=>$redirect->from, 'to'=>'/'));
				}
			}

			// Delete old route
			ci()->droutes_m->delete($old_route->id);
		}
	}
}
/* End of file Drouter.php */

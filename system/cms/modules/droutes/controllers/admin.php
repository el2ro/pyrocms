<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PyroRoutes
 *
 * Controller for the redirects module
 *
 * @author 		Parse19
 * @link		http://parse19.com
 * @package 	PyroRoutes
 * @category	Modules
 * @modified	el2ro
 */
class Admin extends Admin_Controller
{
	public $data;

	/**
	 * Constructor method
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->language('droutes');

		$this->load->model('droutes_m');
	}

	// --------------------------------------------------------------------------

	/**
	 * Show routes
	 *
	 * @access	public
	 * @return	void
	 */
	public function index()
	{
		// Get our routes
		$this->data->routes = $this->droutes_m->get_routes('droutes');

		$this->data->pagination = create_pagination(
										'admin/droutes',
										$this->db->count_all('droutes'),
										$this->settings->item('records_per_page'),
										3);

		$this->template->build('admin/index', $this->data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Add a new route
	 *
	 * @access	public
	 * @return 	void
	 */
	public function new_route()
	{
		$this->data->method = 'new';

		$this->load->library('form_validation');
		$this->form_validation->set_rules( $this->droutes_m->fields );

		foreach($this->droutes_m->fields as $field):

			$this->data->route->{$field['field']} = $this->input->post($field['field']);

		endforeach;

		if($this->form_validation->run() === true):

			// Add our route!
			if(!$this->droutes_m->add_route()):

				$this->session->set_flashdata('error', lang('droutes.add_route_error'));

			else:

				$this->session->set_flashdata('success', lang('droutes.add_route_success'));

			endif;

			redirect('admin/droutes');

		endif;

		$this->template->build('admin/form', $this->data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Add a new route
	 *
	 * @access	public
	 * @return 	void
	 */
	public function edit_route()
	{
		// Get the ID of the route
		$route_id = $this->uri->segment(4);

		if(!is_numeric($route_id)) show_error("Invalid route ID.");

		$this->data->method = 'edit';

		// Get the route
		$this->data->route = $this->droutes_m->get_route($route_id);

		if(is_null($this->data->route)) show_error("Invalid route ID.");

		$this->load->library('form_validation');
		$this->form_validation->set_rules( $this->droutes_m->fields );

		if($this->form_validation->run() === true):

			// Add our route!
			if(!$this->droutes_m->update_route($route_id)):

				$this->session->set_flashdata('error', lang('droutes.edit_route_error'));

			else:

				$this->session->set_flashdata('success', lang('droutes.edit_route_success'));

			endif;

			redirect('admin/droutes');

		endif;

		$this->template->build('admin/form', $this->data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Add a new route
	 *
	 * @access	public
	 * @return 	void
	 */
	public function delete_route()
	{
		// Get the ID of the route
		$route_id = $this->uri->segment(4);

		if(!is_numeric($route_id)) show_error("Invalid route ID.");

		// Delete that route!
		if(!$this->droutes_m->delete_route($route_id)):

			$this->session->set_flashdata('error', lang('droutes.delete_route_error'));

		else:

			$this->session->set_flashdata('success', lang('droutes.delete_route_success'));

		endif;

		redirect('admin/droutes');
	}
}

/* End of file admin.php */
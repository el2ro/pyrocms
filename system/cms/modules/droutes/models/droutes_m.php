<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * PyroRoutes Routes Model
 *
 * @package  	PyroCMS
 * @subpackage  PyroRoutes
 * @category  	Models
 * @author  	Parse19
 */
class Droutes_m extends MY_Model {

	/* Fields */
	public $fields = array(
		array('field'=>'name', 'label'=>'Route Name', 'rules'=>'required|max_length[100]'),
		array('field'=>'group_id', 'label'=>'Group Id', 'rules'=>'numeric|max_length[11]'),
		array('field'=>'route_key', 'label'=>'Route Name', 'rules'=>'required|max_length[200]'),
		array('field'=>'route_value', 'label'=>'Route Name', 'rules'=>'required|max_length[200]')
	);

    // --------------------------------------------------------------------------

    /**
     * Get routes
     *
     * @access	public
     * @param	int limit
     * @param	int offset
     * @return	obj
     */
    public function get_routes($limit = FALSE, $offset = FALSE)
	{
		$this->db->order_by('name', 'asc');

		//$this->db->limit($limit);

		$obj = $this->db->get('droutes');

    	return $obj->result();
	}

    // --------------------------------------------------------------------------

    /**
     * Get a single route by ID
     *
     * @access	public
     * @param	int route_id
     * @return	obj
     */
    public function get_route($route_id)
	{
		$obj = $this->db->limit(1)->where('id', $route_id)->get('droutes');

    	if($obj->num_rows() == 0) return null;

    	return $obj->row();
	}

    // --------------------------------------------------------------------------

    /**
     * Add a route into the db
     *
     * @access	public
     * @return 	bool
     */
    public function add_route()
    {
    	$insert_data = array(
    		'name'			=> $this->input->post('name'),
    		'route_key'		=> $this->input->post('route_key'),
    		'route_value'	=> $this->input->post('route_value'),
    		'when_added'	=> date('Y-m-d H:i:s'),
    		'added_by'		=> $this->current_user->id
    	);

    	return $this->db->insert('droutes', $insert_data);
    }

    // --------------------------------------------------------------------------

    /**
     * Update a route in the db
     *
     * @access	public
     * @param	int route_id
     * @return 	bool
     */
    public function update_route($route_id)
    {
    	$update_data = array(
    		'name'			=> $this->input->post('name'),
    		'route_key'		=> $this->input->post('route_key'),
    		'route_value'	=> $this->input->post('route_value'),
    		'last_updated'	=> date('Y-m-d H:i:s')
    	);

    	$route = $this->db->where('id', $route_id)->get('droutes');
    	if (isset($route->group_id)) { $update_data['group_id'] = $route->group_id; }

    	$this->db->where('id', $route_id);
    	return $this->db->update('droutes', $update_data);
    }

    // --------------------------------------------------------------------------

    /**
     * Update a route in the db
     *
     * @access	public
     * @param	int route_id
     * @return 	bool
     */
    public function delete_route($route_id)
    {
    	$this->db->where('id', $route_id);
    	return $this->db->delete('droutes');
    }
}

/* End of file routes_m.php */
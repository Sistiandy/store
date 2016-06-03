<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** 
* Brand Model Class
 *
 * @package     SYSCMS
 * @subpackage  Models
 * @category    Models
 * @author      Sistiandy Syahbana nugraha <sistiandy.web.id>
 */

class Brand_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // Get From Databases
    function get($params = array())
    {
        if(isset($params['id']))
        {
            $this->db->where('brand.brand_id', $params['id']);
        }

        if(isset($params['limit']))
        {
            if(!isset($params['offset']))
            {
                $params['offset'] = NULL;
            }

            $this->db->limit($params['limit'], $params['offset']);
        }

        if(isset($params['order_by']))
        {
            $this->db->order_by($params['order_by'], 'desc');
        }
        else
        {
            $this->db->order_by('brand_last_update', 'desc');
        }

        $this->db->select('brand.brand_id, brand_name
            user_user_id, user.user_name,
            brand_input_date, brand_last_update');
        $this->db->join('user', 'user.user_id = brand.user_user_id', 'left');
        $res = $this->db->get('brand');

        if(isset($params['id']))
        {
            return $res->row_array();
        }
        else
        {
            return $res->result_array();
        }
    }

    // Add and update to database
    function add($data = array()) {
        
         if(isset($data['brand_id'])) {
            $this->db->set('brand_id', $data['brand_id']);
        }
        
         if(isset($data['brand_name'])) {
            $this->db->set('brand_name', $data['brand_name']);
        }
        
         if(isset($data['brand_input_date'])) {
            $this->db->set('brand_input_date', $data['brand_input_date']);
        }
        
         if(isset($data['brand_last_update'])) {
            $this->db->set('brand_last_update', $data['brand_last_update']);
        }
        
         if(isset($data['user_id'])) {
            $this->db->set('user_user_id', $data['user_id']);
        }
        
        if (isset($data['brand_id'])) {
            $this->db->where('brand_id', $data['brand_id']);
            $this->db->update('brand');
            $id = $data['brand_id'];
        } else {
            $this->db->insert('brand');
            $id = $this->db->insert_id();
        }

        $status = $this->db->affected_rows();
        return ($status == 0) ? FALSE : $id;
    }
    
    // Delete to database
    function delete($id) {
        $this->db->where('brand_id', $id);
        $this->db->delete('brand');
    }
    
}

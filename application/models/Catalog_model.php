<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** 
* Catalog Model Class
 *
 * @package     SYSCMS
 * @subpackage  Models
 * @category    Models
 * @author      Sistiandy Syahbana nugraha <sistiandy.web.id>
 */

class Catalog_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // Get From Databases
    function get($params = array())
    {
        if(isset($params['id']))
        {
            $this->db->where('catalog.catalog_id', $params['id']);
        }

        if(isset($params['date_start']) AND isset($params['date_end']))
        {
            $this->db->where('catalog_published_date', $params['date_start']);
            $this->db->or_where('catalog_published_date', $params['date_end']);
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
            $this->db->order_by('catalog_last_update', 'desc');
        }

        $this->db->select('catalog.catalog_id, catalog_name, catalog_description, catalog_weight,
            catalog_buying_price, catalog_selling_price, catalog_discount, catalog_real_stock,
            catalog_virtual_stock, catalog_image, catalog_for_sale,
            catalog_input_date, catalog_last_update');
        $this->db->select('brand_brand_id, brand_name');
        $this->db->select('user_user_id, user_name');
        $this->db->join('brand', 'brand.brand_id = catalog.brand_brand_id', 'left');
        $this->db->join('user', 'user.user_id = catalog.user_user_id', 'left');
        $res = $this->db->get('catalog');

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
        
         if(isset($data['catalog_id'])) {
            $this->db->set('catalog_id', $data['catalog_id']);
        }
        
         if(isset($data['catalog_name'])) {
            $this->db->set('catalog_name', $data['catalog_name']);
        }
        
         if(isset($data['brand_id'])) {
            $this->db->set('brand_brand_id', $data['brand_id']);
        }
        
         if(isset($data['catalog_description'])) {
            $this->db->set('catalog_description', $data['catalog_description']);
        }
        
         if(isset($data['catalog_weight'])) {
            $this->db->set('catalog_weight', $data['catalog_weight']);
        }
        
         if(isset($data['catalog_buying_price'])) {
            $this->db->set('catalog_buying_price', $data['catalog_buying_price']);
        }
        
         if(isset($data['catalog_selling_price'])) {
            $this->db->set('catalog_selling_price', $data['catalog_selling_price']);
        }
        
         if(isset($data['catalog_discount'])) {
            $this->db->set('catalog_discount', $data['catalog_discount']);
        }
        
         if(isset($data['catalog_real_stock'])) {
            $this->db->set('catalog_real_stock', $data['catalog_real_stock']);
        }
        
         if(isset($data['catalog_virtual_stock'])) {
            $this->db->set('catalog_virtual_stock', $data['catalog_virtual_stock']);
        }
        
         if(isset($data['catalog_image'])) {
            $this->db->set('catalog_image', $data['catalog_image']);
        }
        
         if(isset($data['catalog_for_sale'])) {
            $this->db->set('catalog_for_sale', $data['catalog_for_sale']);
        }
        
         if(isset($data['catalog_last_update'])) {
            $this->db->set('catalog_last_update', $data['catalog_last_update']);
        }
        
         if(isset($data['catalog_is_published'])) {
            $this->db->set('catalog_is_published', $data['catalog_is_published']);
        }
        
         if(isset($data['user_id'])) {
            $this->db->set('user_user_id', $data['user_id']);
        }
        
        if (isset($data['catalog_id'])) {
            $this->db->where('catalog_id', $data['catalog_id']);
            $this->db->update('catalog');
            $id = $data['catalog_id'];
        } else {
            $this->db->insert('catalog');
            $id = $this->db->insert_id();
        }

        $status = $this->db->affected_rows();
        return ($status == 0) ? FALSE : $id;
    }
    
    // Delete to database
    function delete($id) {
        $this->db->where('catalog_id', $id);
        $this->db->delete('catalog');
    }
    
    // Get category from database
    function get_category($params = array())
    {
        if(isset($params['id']))
        {
            $this->db->where('category_id', $params['id']);
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
            $this->db->order_by('category_id', 'desc');
        }

        $this->db->select('category_id, category_name');
        $res = $this->db->get('catalog_category');

        if(isset($params['id']))
        {
            return $res->row_array();
        }
        else
        {
            return $res->result_array();
        }
    }
    
    // Add and Update category to database
    function add_category($data = array()) {
        $param = array(
            'category_name' => $data['category_name'],
        );
        $this->db->set($param);
        
        if (isset($data['category_id'])) {
            $this->db->where('category_id', $data['category_id']);
            $this->db->update('catalog_category');
            $id = $data['category_id'];
        } else {
            $this->db->insert('catalog_category');
            $id = $this->db->insert_id();
        }

        $status = $this->db->affected_rows();
        return ($status == 0) ? FALSE : $id;
    }
    
    // Delete category to database
    function delete_category($id) {
        $this->db->where('category_id', $id);
        $this->db->delete('catalog_category');
    }

    // Set Default category
    function set_default_category($id,$params) {
        $this->db->where('catalog_category_category_id', $id);
        $this->db->update('catalog', $params);
    }
    
    
    // Get From Databases
    function get_catalog_has_category($params = array())
    {
        if(isset($params['id']))
        {
            $this->db->where('catalog_has_catalog_category_id', $params['id']);
        }
        
        if(isset($params['catalog_id']))
        {
            $this->db->where('catalog_catalog_id', $params['catalog_id']);
        }
        
        if(isset($params['category_id']))
        {
            $this->db->where('catalog_category_category_id', $params['category_id']);
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
            $this->db->order_by('catalog_has_catalog_category_id', 'desc');
        }

        $this->db->select('catalog_has_catalog_category_id');
        $this->db->select('catalog_catalog_id, catalog_name');
        $this->db->select('catalog_category_category_id, category_name');
        
        $this->db->join('catalog', 'catalog.catalog_id = catalog_has_catalog_category.catalog_catalog_id', 'left');
        $this->db->join('catalog_category', 'catalog_has_catalog_category.category_id = catalog_has_catalog_category.catalog_has_catalog_category_id', 'left');
        $res = $this->db->get('catalog_has_catalog_category');

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
    function add_catalog_has_category($data = array()) {
        
         if(isset($data['id'])) {
            $this->db->set('catalog_has_catalog_category_id', $data['id']);
        }
        
         if(isset($data['catalog_id'])) {
            $this->db->set('catalog_catalog_id', $data['catalog_id']);
        }
        
         if(isset($data['category_id'])) {
            $this->db->set('catalog_category_id', $data['category_id']);
        }
        
        if (isset($data['id'])) {
            $this->db->where('catalog_has_catalog_category_id', $data['id']);
            $this->db->update('catalog_has_catalog_category');
            $id = $data['id'];
        } else {
            $this->db->insert('catalog_has_catalog_category');
            $id = $this->db->insert_id();
        }

        $status = $this->db->affected_rows();
        return ($status == 0) ? FALSE : $id;
    }
    
    // Delete category to database
    function delete_catalog_has_category($id) {
        $this->db->where('catalog_has_catalog_category_id', $id);
        $this->db->delete('catalog_has_catalog_category');
    }
}

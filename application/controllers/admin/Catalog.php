<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Catalog controllers class
 *
 * @package     SYSCMS
 * @subpackage  Controllers
 * @category    Controllers
 * @author      Sistiandy Syahbana nugraha <sistiandy.web.id>
 */
class Catalog extends CI_Controller {

    public function __construct() {
        parent::__construct(TRUE);
        if ($this->session->userdata('logged') == NULL) {
            header("Location:" . site_url('admin/auth/login') . "?location=" . urlencode($_SERVER['REQUEST_URI']));
        }
        $this->load->model(array('Catalog_model', 'Activity_log_model'));
        $this->load->library('upload');
    }

    // Catalog view in list
    public function index($offset = NULL) {
        $this->load->library('pagination');
        $data['catalog'] = $this->Catalog_model->get(array('limit' => 10, 'offset' => $offset, 'status' => TRUE));
        $data['category'] = $this->Catalog_model->get_category();
        $config['base_url'] = site_url('admin/catalog/index');
        $config['total_rows'] = count($this->Catalog_model->get(array('status' => TRUE)));
        $this->pagination->initialize($config);

        $data['title'] = 'Catalog';
        $data['main'] = 'admin/catalog/catalog_list';
        $this->load->view('admin/layout', $data);
    }

    function detail($id = NULL) {
        if ($this->Catalog_model->get(array('id' => $id)) == NULL) {
            redirect('admin/catalog');
        }
        $data['catalog'] = $this->Catalog_model->get(array('id' => $id));
        $data['title'] = 'Detail posting';
        $data['main'] = 'admin/catalog/catalog_view';
        $this->load->view('admin/layout', $data);
    }

    // Category view in list
    public function category($offset = NULL) {
        $this->load->library('pagination');
        $data['categories'] = $this->Catalog_model->get_category(array('limit' => 10, 'offset' => $offset));
        $config['base_url'] = site_url('admin/catalog/category');
        $config['total_rows'] = $this->db->count_all('catalog_category');
        $this->pagination->initialize($config);
        $data['title'] = 'Kategori Catalog';
        $data['main'] = 'admin/catalog/category_list';
        $this->load->view('admin/layout', $data);
    }

    // Add Catalog and Update
    public function add($id = NULL) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('catalog_name', 'Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('catalog_description', 'Description', 'trim|required|xss_clean');
        $this->form_validation->set_rules('brand_id', 'Brand', 'trim|required|xss_clean');
        $this->form_validation->set_rules('catalog_weight', 'Weight', 'trim|required|xss_clean');
        $this->form_validation->set_rules('catalog_selling_price', 'Selling Price', 'trim|required|xss_clean');
        $this->form_validation->set_rules('catalog_foe_sale', 'For sale', 'trim|required|xss_clean');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>', '</div>');
        $data['operation'] = is_null($id) ? 'Tambah' : 'Sunting';

        if ($_POST AND $this->form_validation->run() == TRUE) {
            if ($this->input->post('inputGambarCurrent')) {
                $params['catalog_image'] = $this->input->post('inputGambarCurrent');
            } 

            if ($this->input->post('catalog_id')) {
                $params['catalog_id'] = $this->input->post('catalog_id');
            } else {
                $params['catalog_input_date'] = date('Y-m-d H:i:s');
            }

            $params['user_id'] = $this->session->userdata('user_id');
            $params['catalog_last_update'] = date('Y-m-d H:i:s');
            $params['catalog_name'] = $this->input->post('catalog_name');
            $params['brand_id'] = $this->input->post('brand_id');
            $params['catalog_description'] = $this->input->post('catalog_description');
            $params['catalog_weight'] = $this->input->post('catalog_weight');
            $params['catalog_buying_price'] = $this->input->post('catalog_buying_price');
            $params['catalog_selling_price'] = $this->input->post('catalog_selling_price');
            $params['catalog_real_stock'] = $this->input->post('catalog_real_stock');
            $params['catalog_virtual_stock'] = $this->input->post('catalog_real_stock');
            $params['catalog_for_sale'] = $this->input->post('catalog_for_sale');
            $status = $this->Catalog_model->add($params);


            // activity log
            $this->Activity_log_model->add(
                    array(
                        'log_date' => date('Y-m-d H:i:s'),
                        'user_id' => $this->session->userdata('user_id'),
                        'log_module' => 'Catalog',
                        'log_action' => $data['operation'],
                        'log_info' => 'ID:null;Name:' . $params['catalog_name']
                    )
            );

            $this->session->set_flashdata('success', $data['operation'] . ' posting berhasil');
            redirect('admin/catalog');
        } else {
            if ($this->input->post('catalog_id')) {
                redirect('admin/catalog/edit/' . $this->input->post('catalog_id'));
            }

            // Edit mode
            if (!is_null($id)) {
                $data['catalog'] = $this->Catalog_model->get(array('id' => $id));
            }
            $data['category'] = $this->Catalog_model->get_category();
            $data['title'] = $data['operation'] . ' Catalog';
            $data['main'] = 'admin/catalog/catalog_add';
            $this->load->view('admin/layout', $data);
        }
    }

    // Add Category
    public function add_category($id = NULL) {
        $this->load->library('form_validation');
        if ($this->input->post('category_id')) {
            $this->form_validation->set_rules('category_name', 'Name', 'trim|required|xss_clean');
        } else {
            $this->form_validation->set_rules('category_name', 'Name', 'trim|required|xss_clean|is_unique[catalog_category.category_name]');
        }
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>', '</div>');
        $data['operation'] = is_null($id) ? 'Tambah' : 'Sunting';

        if ($_POST AND $this->form_validation->run() == TRUE) {
            if ($this->input->post('category_id')) {
                $params['category_id'] = $this->input->post('category_id');
                $params['category_input_date'] = $this->input->post('category_input_date');
            } else {
                $params['category_input_date'] = date('Y-m-d H:i:s');
            }
            $params['category_last_update'] = date('Y-m-d H:i:s');
            $params['category_name'] = $this->input->post('category_name');
            $res = $this->Catalog_model->add_category($params);

            // activity log
            $this->Activity_log_model->add(
                    array(
                        'log_date' => date('Y-m-d H:i:s'),
                        'user_id' => $this->session->userdata('user_id'),
                        'log_module' => 'Catalog',
                        'log_action' => $data['operation'],
                        'log_info' => 'ID:null;Name:' . $params['category_name']
                    )
            );

            if ($this->input->is_ajax_request()) {
                echo $res;
            } else {
                $this->session->set_flashdata('success', $data['operation'] . ' kategori berhasil');
                redirect('admin/catalog/category');
            }
        } else {
            if ($this->input->post('category_id')) {
                redirect('admin/catalog/category/edit/' . $this->input->post('category_id'));
            }

            // Edit mode
            if (!is_null($id)) {
                if ($id == 1) {
                    redirect('admin/catalog/category/');
                }
                $data['category'] = $this->Catalog_model->get_category(array('id' => $id));
            }
            $data['title'] = 'Tambah Kategori';
            $data['main'] = 'admin/catalog/category_add';
            $this->load->view('admin/layout', $data);
        }
    }

    protected function get_category() {
        $res = json_encode($this->Catalog_model->get_category());
        return $res;
    }

    // Delete Catalog
    public function delete($id = NULL) {
        if ($_POST) {
            $this->Catalog_model->delete($this->input->post('del_id'));
            // activity log
            $this->Activity_log_model->add(
                    array(
                        'log_date' => date('Y-m-d H:i:s'),
                        'user_id' => $this->session->userdata('user_id'),
                        'log_module' => 'Catalog',
                        'log_action' => 'Hapus',
                        'log_info' => 'ID:' . $this->input->post('del_id') . ';Name:' . $this->input->post('del_name')
                    )
            );
            $this->session->set_flashdata('success', 'Hapus posting berhasil');
            redirect('admin/catalog');
        } elseif (!$_POST) {
            $this->session->set_flashdata('delete', 'Delete');
            redirect('admin/catalog/edit/' . $id);
        }
    }

    // Delete Category
    public function delete_category($id = NULL) {
        if ($_POST) {
            $params['category_id'] = '1';
            $this->Catalog_model->set_default_category($id, $params);

            $this->Catalog_model->delete_category($this->input->post('del_id'));
            // activity log
            $this->Activity_log_model->add(
                    array(
                        'log_date' => date('Y-m-d H:i:s'),
                        'user_id' => $this->session->userdata('user_id'),
                        'log_module' => 'Kategori Catalog',
                        'log_action' => 'Hapus',
                        'log_info' => 'ID:' . $this->input->post('del_id') . ';Name:' . $this->input->post('del_name')
                    )
            );
            $this->session->set_flashdata('success', 'Hapus kategori posting berhasil');
            redirect('admin/catalog/category');
        } elseif (!$_POST) {
            $this->session->set_flashdata('delete', 'Delete');
            redirect('admin/catalog/category/edit/' . $id);
        }
    }

}

/* End of file catalog.php */
/* Location: ./application/controllers/admin/catalog.php */

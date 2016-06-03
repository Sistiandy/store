<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
 
/**
 * Brand controllers class
 *
 * @package     SYSCMS
 * @subpackage  Controllers
 * @category    Controllers
 * @author      Sistiandy Syahbana nugraha <sistiandy.web.id>
 */
class Brand extends CI_Controller {

    public function __construct() {
        parent::__construct(TRUE);
        if ($this->session->userdata('logged') == NULL) {
            header("Location:" . site_url('admin/auth/login') . "?location=" . urlencode($_SERVER['REQUEST_URI']));
        }
        $this->load->model(array('Brand_model', 'Activity_log_model'));
        $this->load->helper('string');
    }

    // Brand view in list
    public function index($offset = NULL) {
        $this->load->library('pagination');
        $data['brand'] = $this->Brand_model->get(array('limit' => 10, 'offset' => $offset));
        $config['base_url'] = site_url('admin/brand/index');
        $config['total_rows'] = count($this->Brand_model->get(array('status' => TRUE)));
        $this->pagination->initialize($config);

        $data['title'] = 'Brand';
        $data['main'] = 'admin/brand/brand_list';
        $this->load->view('admin/layout', $data);
    }

    function detail($id = NULL) {
        if ($this->Brand_model->get(array('id' => $id)) == NULL) {
            redirect('admin/brand');
        }
        $data['brand'] = $this->Brand_model->get(array('id' => $id));
        $data['title'] = 'Detail Brand';
        $data['main'] = 'admin/brand/brand_view';
        $this->load->view('admin/layout', $data);
    }

    // Add Brand and Update
    public function add($id = NULL) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('brand_name', 'Name', 'trim|required|xss_clean');        
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>', '</div>');
        $data['operation'] = is_null($id) ? 'Tambah' : 'Sunting';

        if ($_POST AND $this->form_validation->run() == TRUE) {

            if ($this->input->post('brand_id')) {
                $params['brand_id'] = $this->input->post('brand_id');
            } else {                
                
            }

            $params['brand_name'] = $this->input->post('brand_name');            
            $params['user_id'] = $this->session->userdata('user_id');            
            $status = $this->Brand_model->add($params);

            // activity log
            $this->Activity_log_model->add(
                array(
                    'log_date' => date('Y-m-d H:i:s'),
                    'user_id' => $this->session->userdata('user_id'),
                    'log_module' => 'Brand',
                    'log_action' => $data['operation'],
                    'log_info' => 'ID:'.$status.';Title:' . $params['brand_name']
                    )
                );

            $this->session->set_flashdata('success', $data['operation'] . ' Brand berhasil');
            redirect('admin/brand');
        } else {
            if ($this->input->post('brand_id')) {
                redirect('admin/brand/edit/' . $this->input->post('brand_id'));
            }

            // Edit mode
            if (!is_null($id)) {
                $data['brand'] = $this->Brand_model->get(array('id' => $id));
            }
            $data['title'] = $data['operation'] . ' Brand';
            $data['main'] = 'admin/brand/brand_add';
            $this->load->view('admin/layout', $data);
        }
    }

    // Delete Brand
    public function delete($id = NULL) {
        if ($_POST) {
            $this->Brand_model->delete($this->input->post('del_id'));
            // activity log
            $this->Activity_log_model->add(
                array(
                    'log_date' => date('Y-m-d H:i:s'),
                    'user_id' => $this->session->userdata('user_id'),
                    'log_module' => 'Brand',
                    'log_action' => 'Hapus',
                    'log_info' => 'ID:' . $this->input->post('del_id') . ';Title:' . $this->input->post('del_name')
                    )
                );
            $this->session->set_flashdata('success', 'Hapus Brand berhasil');
            redirect('admin/brand');
        } elseif (!$_POST) {
            $this->session->set_flashdata('delete', 'Delete');
            redirect('admin/brand/edit/' . $id);
        }
    }

}

/* End of file brand.php */
/* Location: ./application/controllers/admin/brand.php */

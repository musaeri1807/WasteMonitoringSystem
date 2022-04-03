<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lokasi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_login();

        $this->load->model('Admin_model', 'admin');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = "Lokasi";
        $data['lokasi'] = $this->admin->getLokasi();
        $this->template->load('templates/dashboard', 'lokasi/data', $data);
    }

    private function _validasi()
    {
        $this->form_validation->set_rules('nama_lokasi', 'nama_lokasi', 'required|trim');
        $this->form_validation->set_rules('id_role_lokasi', 'id_role_lokasi', 'required|trim');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required|trim');
    }

    public function add()
    {
        $this->_validasi();
        if ($this->form_validation->run() == false) {
            $data['title'] = "Lokasi";
            $this->template->load('templates/dashboard', 'lokasi/add', $data);
        } else {
            $input = $this->input->post(null, true);
            $save = $this->admin->insert('lokasi', $input);
            if ($save) {
                set_pesan('data berhasil disimpan.');
                redirect('lokasi');
            } else {
                set_pesan('data gagal disimpan', false);
                redirect('lokasi/add');
            }
        }
    }


    public function edit($getId)
    {
        $id = encode_php_tags($getId);
        $this->_validasi();

        if ($this->form_validation->run() == false) {
            $data['title'] = "Lokasi";
            $data['lokasi'] = $this->admin->get('lokasi', ['id_lokasi' => $id]);
            $this->template->load('templates/dashboard', 'lokasi/edit', $data);
        } else {
            $input = $this->input->post(null, true);
            $update = $this->admin->update('lokasi', 'id_lokasi', $id, $input);

            if ($update) {
                set_pesan('data berhasil diedit.');
                redirect('lokasi');
            } else {
                set_pesan('data gagal diedit.');
                redirect('lokasi/edit/' . $id);
            }
        }
    }

    public function delete($getId)
    {
        $id = encode_php_tags($getId);
        if ($this->admin->delete('lokasi', 'id_lokasi', $id)) {
            set_pesan('data berhasil dihapus.');
        } else {
            set_pesan('data gagal dihapus.', false);
        }
        redirect('lokasi');
    }
}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan extends CI_Controller
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
        $this->form_validation->set_rules('transaksi', 'Transaksi', 'required|in_list[barang_masuk,barang_keluar]');
        $this->form_validation->set_rules('tanggal', 'Periode Tanggal', 'required');

        if ($this->form_validation->run() == false) {
            $data['title'] = "Laporan Transaksi";
            $this->template->load('templates/dashboard', 'laporan/form', $data);
        } else {
            $input = $this->input->post(null, true);
            $table = $input['transaksi'];
            $tanggal = $input['tanggal'];
            $pecah = explode(' - ', $tanggal);
            $mulai = date('Y-m-d', strtotime($pecah[0]));
            $akhir = date('Y-m-d', strtotime(end($pecah)));

            $query = '';
            if ($table == 'barang_masuk') {
                $query = $this->admin->getBarangMasuk(null, null, ['mulai' => $mulai, 'akhir' => $akhir]);
            } else {
                $query = $this->admin->getBarangKeluar(null, null, ['mulai' => $mulai, 'akhir' => $akhir]);
            }

            $this->_cetak($query, $table, $tanggal);
        }
    }

    private function _cetak($data, $table_, $tanggal)
    {
        $this->load->library('CustomPDF');
        $table = $table_ == 'barang_masuk' ? 'Sampah Masuk' : 'Sampah Keluar';

        $pdf = new FPDF();
        $pdf->AddPage('L', 'Letter');
        $pdf->SetFont('Times', 'B', 16);
        $pdf->Cell(250, 7, 'Laporan ' . $table, 0, 1, 'C');
        $pdf->SetFont('Times', '', 10);
        $pdf->Cell(250, 4, 'Tanggal : ' . $tanggal, 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 10);

        if ($table_ == 'barang_masuk') :
            $pdf->Cell(10, 7, 'No.', 1, 0, 'C');
            $pdf->Cell(25, 7, 'Tgl Masuk', 1, 0, 'C');
            $pdf->Cell(35, 7, 'ID Transaksi', 1, 0, 'C');
            $pdf->Cell(55, 7, 'Nama Sampah', 1, 0, 'C');
            $pdf->Cell(35, 7, 'Lokasi', 1, 0, 'C');
            $pdf->Cell(35, 7, 'Kategori', 1, 0, 'C');
            $pdf->Cell(30, 7, 'Jumlah Masuk', 1, 0, 'C');
            $pdf->Cell(35, 7, 'Petugas', 1, 0, 'C');
            $pdf->Ln();

            $no = 1;
            foreach ($data as $d) {
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(10, 7, $no++ . '.', 1, 0, 'C');
                $pdf->Cell(25, 7, $d['tanggal_masuk'], 1, 0, 'C');
                $pdf->Cell(35, 7, $d['id_barang_masuk'], 1, 0, 'C');
                $pdf->Cell(55, 7, $d['nama_barang'], 1, 0, 'L');
                $pdf->Cell(35, 7, $d['nama_lokasi'], 1, 0, 'L');
                $pdf->Cell(35, 7, $d['nama_kategori'], 1, 0, 'L');
                $pdf->Cell(30, 7, $d['jumlah_masuk'] . ' ' . $d['nama_satuan'], 1, 0, 'C');
                $pdf->Cell(35, 7, $d['nama'], 1, 0, 'L');
                $pdf->Ln();
            }
        else :
            $pdf->Cell(10, 7, 'No.', 1, 0, 'C');
            $pdf->Cell(25, 7, 'Tgl Keluar', 1, 0, 'C');
            $pdf->Cell(35, 7, 'ID Transaksi', 1, 0, 'C');
            $pdf->Cell(55, 7, 'Nama Sampah', 1, 0, 'C');
            $pdf->Cell(35, 7, 'Lokasi', 1, 0, 'C');
            $pdf->Cell(35, 7, 'Kategori', 1, 0, 'C');
            $pdf->Cell(30, 7, 'Jumlah Keluar', 1, 0, 'C');
            $pdf->Cell(35, 7, 'Petugas', 1, 0, 'C');
            $pdf->Ln();

            $no = 1;
            foreach ($data as $d) {
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(10, 7, $no++ . '.', 1, 0, 'C');
                $pdf->Cell(25, 7, $d['tanggal_keluar'], 1, 0, 'C');
                $pdf->Cell(35, 7, $d['id_barang_keluar'], 1, 0, 'C');
                $pdf->Cell(55, 7, $d['nama_barang'], 1, 0, 'L');
                $pdf->Cell(35, 7, $d['nama_lokasi'], 1, 0, 'L');
                $pdf->Cell(35, 7, $d['nama_kategori'], 1, 0, 'L');
                $pdf->Cell(30, 7, $d['jumlah_keluar'] . ' ' . $d['nama_satuan'], 1, 0, 'C');
                $pdf->Cell(35, 7, $d['nama'], 1, 0, 'L');
                $pdf->Ln();
            }
        endif;
        $pdf->Cell(10, 7, 'No.', 1, 0, 'C');
        $pdf->Cell(25, 7, 'Tgl Masuk', 1, 0, 'C');
        $pdf->Cell(35, 7, 'ID Transaksi', 1, 0, 'C');
        $pdf->Cell(55, 7, 'Nama Sampah', 1, 0, 'C');
        $pdf->Cell(35, 7, 'Lokasi', 1, 0, 'C');
        $pdf->Cell(35, 7, 'Kategori', 1, 0, 'C');
        if($table=='Sampah Masuk'){
            $pdf->Cell(30, 7, 'Jumlah Masuk', 1, 0, 'C');

        }else{

            $pdf->Cell(30, 7, 'Jumlah Keluar', 1, 0, 'C');
        }
        $pdf->Cell(35, 7, 'Petugas', 1, 0, 'C');
        $pdf->Ln();

        $file_name = $table . ' ' . $tanggal;
        $pdf->Output('I', $file_name);
    }
}

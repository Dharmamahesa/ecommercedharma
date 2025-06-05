<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Model_app $Model_app
 * @property Model_reseller $Model_reseller // Jika diperlukan untuk data reseller terkait pesanan
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Loader $load
 * @property CI_URI $uri
 * @property CI_Form_validation $form_validation
 * @property CI_Upload $upload         // Untuk upload bukti transfer
 * @property CI_Config $config
 * @property CI_Template $template     // Asumsi library template sudah di-load
 */
class Konfirmasi extends CI_Controller {

    protected $default_data;

    public function __construct() {
        parent::__construct();
        // Load helper dan library dasar
        $this->load->helper(array('url', 'form', 'phpmu', 'text', 'string')); // phpmu helper dari PDF
        $this->load->library(array('session', 'form_validation', 'pagination', 'template', 'upload'));

        // Load model yang sering digunakan
        $this->load->model('Model_app');
        $this->load->model('Model_reseller'); // Mungkin dibutuhkan untuk detail penjual saat tracking

        // Data default untuk view (bisa diambil dari database identitas)
        $identitas_query = $this->Model_app->edit('tb_identitas', ['id_identitas' => 1]);
        $identitas = $identitas_query ? $identitas_query->row_array() : null;

        $this->default_data = array(
            'website_name'    => isset($identitas['nama_website']) ? $identitas['nama_website'] : 'Toko Online Saya',
            'controller_name' => 'konfirmasi',
            'iden'            => $identitas
        );
    }

    /**
     * Menampilkan form konfirmasi pembayaran.
     * Bisa menerima kode transaksi via GET untuk pra-isi form.
     */
    public function index() {
        $data = $this->default_data;
        $data['title'] = 'Konfirmasi Pembayaran';

        $kode_transaksi_get = $this->input->get('kode', TRUE);
        $data['rows'] = array(); // Untuk pra-isi form jika kode transaksi ada
        $data['total'] = array('total' => 0);
        $data['rekening_tujuan_list'] = array();

        if (!empty($kode_transaksi_get)) {
            // Ambil data transaksi berdasarkan kode
            // Query ini dari method `index()` controller `Konfirmasi` di PDF halaman 209-210
            $cek_transaksi = $this->db->query("SELECT a.id_penjualan, a.kode_transaksi, a.ongkir, b.id_reseller 
                                               FROM rb_penjualan a 
                                               JOIN rb_reseller b ON a.id_penjual = b.id_reseller 
                                               WHERE a.status_penjual = 'reseller' AND a.kode_transaksi = ".$this->db->escape($kode_transaksi_get));
            
            $data['rows'] = $cek_transaksi ? $cek_transaksi->row_array() : null;

            if ($data['rows']) {
                // Ambil total belanja untuk transaksi ini
                $total_query = $this->db->query("SELECT SUM((a.harga_jual*a.jumlah)-a.diskon) as total 
                                                FROM rb_penjualan_detail a 
                                                WHERE a.id_penjualan = '".$data['rows']['id_penjualan']."'");
                $data['total'] = $total_query ? $total_query->row_array() : array('total' => 0);
                
                // Ambil rekening tujuan berdasarkan id_reseller (penjual)
                $rekening_query = $this->Model_app->view_where('rb_rekening_reseller', array('id_reseller' => $data['rows']['id_reseller']));
                $data['rekening_tujuan_list'] = $rekening_query ? $rekening_query->result_array() : [];
            } else {
                $this->session->set_flashdata('message_error', '<div class="alert alert-danger">Kode Transaksi tidak ditemukan atau tidak valid.</div>');
                // Tidak redirect agar form tetap bisa diisi manual
            }
        }

        // Jika tidak ada kode transaksi via GET atau tidak ditemukan, tampilkan form kosong
        // atau dengan data dari submit sebelumnya jika ada error
        // PDF halaman 209 controller konfirmasi.php menggunakan POST untuk mencari invoice
        if ($this->input->post('submit1') && !$kode_transaksi_get) { // Cek invoice via POST
            $kode_transaksi_post = $this->input->post('a', TRUE); // 'a' adalah nama input kode transaksi di form
            $cek_transaksi_post = $this->db->query("SELECT a.id_penjualan, a.kode_transaksi, a.ongkir, b.id_reseller 
                                               FROM rb_penjualan a 
                                               JOIN rb_reseller b ON a.id_penjual = b.id_reseller 
                                               WHERE a.status_penjual = 'reseller' AND a.kode_transaksi = ".$this->db->escape($kode_transaksi_post));
            $data['rows'] = $cek_transaksi_post ? $cek_transaksi_post->row_array() : null;

            if ($data['rows']) {
                $total_query_post = $this->db->query("SELECT SUM((a.harga_jual*a.jumlah)-a.diskon) as total 
                                                FROM rb_penjualan_detail a 
                                                WHERE a.id_penjualan = '".$data['rows']['id_penjualan']."'");
                $data['total'] = $total_query_post ? $total_query_post->row_array() : array('total' => 0);

                $rekening_query_post = $this->Model_app->view_where('rb_rekening_reseller', array('id_reseller' => $data['rows']['id_reseller']));
                $data['rekening_tujuan_list'] = $rekening_query_post ? $rekening_query_post->result_array() : [];
            } else {
                $this->session->set_flashdata('message_error', '<div class="alert alert-danger">Kode Transaksi tidak ditemukan. Silakan coba lagi.</div>');
                // Data rows dan total akan kosong, view harus menghandle ini
                 $data['rows'] = []; $data['total'] = ['total'=>0]; $data['rekening_tujuan_list'] = [];
            }
        }


        // Asumsi view ada di template_frontend/reseller/view_konfirmasi_pembayaran.php
        // Sesuaikan dengan path template dan view Anda
        $this->template->load(template().'/template', template().'/reseller/view_konfirmasi_pembayaran', $data);
    }

    /**
     * Memproses submit form konfirmasi pembayaran.
     */
    public function submit_konfirmasi() {
        if (!$this->input->post('submit_konfirmasi')) { // 'submit_konfirmasi' adalah name tombol submit utama
            redirect('konfirmasi');
        }

        // Aturan validasi
        $this->form_validation->set_rules('id_penjualan', 'ID Penjualan', 'required|integer'); // Hidden input
        $this->form_validation->set_rules('total_transfer', 'Total Transfer', 'required|trim');
        $this->form_validation->set_rules('id_rekening_tujuan', 'Rekening Tujuan', 'required|integer');
        $this->form_validation->set_rules('nama_pengirim', 'Nama Pengirim', 'required|trim|strip_tags');
        $this->form_validation->set_rules('tanggal_transfer', 'Tanggal Transfer', 'required|trim');
        // Validasi untuk file upload bisa ditambahkan jika diperlukan (misal wajib atau tipe file)

        if ($this->form_validation->run() === FALSE) {
            // Jika validasi gagal, kembali ke form konfirmasi dengan error
            // Kita perlu mengirimkan kembali data yang dibutuhkan form (seperti di method index)
            $this->session->set_flashdata('message_error', validation_errors('<div class="alert alert-danger">', '</div>'));
            // Untuk pra-isi form lagi jika validasi gagal, kita butuh kode transaksi
            // Ini agak tricky, mungkin lebih baik redirect ke konfirmasi?kode=KODE_TRX atau simpan di session
            $kode_transaksi_asal = $this->input->post('kode_transaksi_asal', TRUE); // Hidden input kode transaksi di form
            if ($kode_transaksi_asal) {
                redirect('konfirmasi?kode=' . $kode_transaksi_asal . '&validation_error=true');
            } else {
                redirect('konfirmasi?validation_error=true');
            }
            return;

        } else {
            // Proses data konfirmasi
            $id_penjualan = $this->input->post('id_penjualan', TRUE);
            $total_transfer_raw = $this->input->post('total_transfer', TRUE);
            $total_transfer = preg_replace("/[^0-9]/", "", $total_transfer_raw); // Bersihkan format Rupiah

            $data_konfirmasi = array(
                'id_penjualan'      => $id_penjualan,
                'total_transfer'    => $total_transfer,
                'id_rekening'       => $this->input->post('id_rekening_tujuan', TRUE), // Ini adalah id_rekening dari rb_rekening_reseller
                'nama_pengirim'     => $this->input->post('nama_pengirim', TRUE),
                'tanggal_transfer'  => $this->input->post('tanggal_transfer', TRUE),
                'waktu_konfirmasi'  => date('Y-m-d H:i:s')
            );

            // Handle upload bukti transfer
            $config_upload['upload_path']   = './asset/bukti_transfer/'; // Pastikan folder ini ada dan writable
            $config_upload['allowed_types'] = 'gif|jpg|png|jpeg|pdf';
            $config_upload['max_size']      = '2048'; // 2MB
            $config_upload['encrypt_name']  = TRUE;

            if (!is_dir($config_upload['upload_path'])) {
                mkdir($config_upload['upload_path'], 0777, TRUE);
            }
            $this->upload->initialize($config_upload);

            if (!empty($_FILES['bukti_transfer_file']['name'])) {
                if ($this->upload->do_upload('bukti_transfer_file')) {
                    $upload_data = $this->upload->data();
                    $data_konfirmasi['bukti_transfer'] = $upload_data['file_name'];
                } else {
                    $this->session->set_flashdata('message_error', '<div class="alert alert-danger">Gagal mengupload bukti transfer: ' . $this->upload->display_errors('', '') . '</div>');
                    $kode_transaksi_asal = $this->input->post('kode_transaksi_asal', TRUE);
                     if ($kode_transaksi_asal) {
                        redirect('konfirmasi?kode=' . $kode_transaksi_asal);
                    } else {
                        redirect('konfirmasi');
                    }
                    return;
                }
            }

            // Simpan ke tabel rb_konfirmasi_pembayaran (sesuai PDF, ini adalah rb_konfirmasi_pembayaran_konsumen)
            // Pastikan nama tabel sesuai dengan yang ada di SQL Anda: rb_konfirmasi_pembayaran
            if ($this->Model_app->insert('rb_konfirmasi_pembayaran', $data_konfirmasi)) {
                // Update status pesanan di tabel rb_penjualan menjadi 'sudah dikonfirmasi' / 'menunggu verifikasi'
                // PDF menggunakan proses '2' untuk "dikonfirmasi"
                $this->Model_app->update('rb_penjualan', array('proses' => '2'), array('id_penjualan' => $id_penjualan));
                
                $this->session->set_flashdata('message_success', '<div class="alert alert-success">Terima kasih! Konfirmasi pembayaran Anda telah berhasil dikirim. Pesanan Anda akan segera kami proses.</div>');
                redirect('members/orders_report/success'); // Arahkan ke histori pesanan member dengan notif sukses
            } else {
                $this->session->set_flashdata('message_error', '<div class="alert alert-danger">Gagal menyimpan data konfirmasi. Silakan coba lagi.</div>');
                $kode_transaksi_asal = $this->input->post('kode_transaksi_asal', TRUE);
                if ($kode_transaksi_asal) {
                    redirect('konfirmasi?kode=' . $kode_transaksi_asal);
                } else {
                    redirect('konfirmasi');
                }
            }
        }
    }

    /**
     * Menampilkan form pelacakan pesanan atau hasil pelacakan.
     */
    public function tracking() {
        $data = $this->default_data;
        $data['title'] = 'Lacak Pesanan Anda';

        $kode_transaksi_track = $this->input->post('kode_transaksi_track', TRUE); // Dari form submit
        if (empty($kode_transaksi_track)) {
             $kode_transaksi_track = $this->uri->segment(3) ? $this->uri->segment(3) : NULL; // Dari URL jika ada
        }


        if ($kode_transaksi_track) {
            // Ambil detail pesanan berdasarkan kode transaksi
            // Query ini dari method `tracking()` controller `Konfirmasi` di PDF halaman 211
            $cek_pesanan_query = $this->db->query("SELECT a.*, b.nama_lengkap as nama_pembeli, b.email as email_pembeli, b.no_telp as telp_pembeli,
                                                d.nama_reseller as nama_penjual, d.no_telpon as telp_penjual, e.nama_kota as kota_tujuan
                                                FROM rb_penjualan a 
                                                JOIN rb_konsumen b ON a.id_pembeli = b.id_konsumen
                                                LEFT JOIN rb_kota e ON b.kota_id = e.kota_id
                                                LEFT JOIN rb_reseller d ON a.id_penjual = d.id_reseller
                                                WHERE a.kode_transaksi = ".$this->db->escape($kode_transaksi_track));
            $data['rows_pesanan'] = $cek_pesanan_query ? $cek_pesanan_query->row_array() : null;

            if ($data['rows_pesanan']) {
                // Ambil detail item produk
                $item_pesanan_query = $this->db->query("SELECT b.*, c.nama_produk, c.satuan, c.berat, c.produk_seo 
                                                       FROM rb_penjualan a 
                                                       JOIN rb_penjualan_detail b ON a.id_penjualan = b.id_penjualan 
                                                       JOIN rb_produk c ON b.id_produk = c.id_produk 
                                                       WHERE a.kode_transaksi = ".$this->db->escape($kode_transaksi_track));
                $data['record_items'] = $item_pesanan_query ? $item_pesanan_query->result_array() : [];

                // Ambil total dari PDF (sedikit disesuaikan)
                 $total_pesanan_query = $this->db->query("SELECT a.kode_transaksi, a.kurir, a.service, a.proses, a.ongkir, 
                                                       sum((b.harga_jual*b.jumlah)-b.diskon) as total_harga_produk, 
                                                       sum(c.berat*b.jumlah) as total_berat 
                                                       FROM rb_penjualan a 
                                                       JOIN rb_penjualan_detail b ON a.id_penjualan=b.id_penjualan 
                                                       JOIN rb_produk c ON b.id_produk=c.id_produk 
                                                       WHERE a.kode_transaksi= ".$this->db->escape($kode_transaksi_track)." GROUP BY a.id_penjualan"); // Tambah GROUP BY
                $data['total_pesanan'] = $total_pesanan_query ? $total_pesanan_query->row_array() : null;


                // Muat view hasil tracking
                $this->template->load(template().'/template', template().'/reseller/view_tracking_view', $data);
            } else {
                $this->session->set_flashdata('message_tracking_error', '<div class="alert alert-warning">Pesanan dengan kode transaksi tersebut tidak ditemukan.</div>');
                // Tampilkan form tracking lagi
                $this->template->load(template().'/template', template().'/reseller/view_tracking', $data);
            }
        } else {
            // Tampilkan form tracking
            $this->template->load(template().'/template', template().'/reseller/view_tracking', $data);
        }
    }
}
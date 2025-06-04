<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Template {
    protected $_ci; // Variabel untuk menyimpan instance CodeIgniter
    var $template_data = array(); // Array untuk menyimpan data yang akan dikirim ke template dan view

    /**
     * Constructor
     * Mengambil instance CodeIgniter.
     */
    public function __construct() {
        $this->_ci = &get_instance();
    }

    /**
     * Method untuk mengatur data yang akan dikirim ke template/view.
     *
     * @param string $name Nama variabel data
     * @param mixed $value Nilai variabel data
     */
    function set($name, $value) {
        $this->template_data[$name] = $value;
    }

    /**
     * Method utama untuk memuat template dan view konten.
     *
     * @param string $template_view_file Path ke file template utama (relatif terhadap folder views)
     * @param string $content_view_file Path ke file view konten (relatif terhadap folder views)
     * @param array $data Data yang akan diteruskan ke view konten dan template utama
     * @param bool $return Jika TRUE, akan mengembalikan output sebagai string, jika FALSE (default) akan langsung menampilkan.
     */
    function load($template_view_file, $content_view_file, $data = array(), $return = FALSE) {
        // Menggabungkan data yang sudah di-set sebelumnya dengan data yang baru dikirim
        $data = array_merge($this->template_data, $data);

        // Memuat view konten dan menyimpan outputnya ke dalam variabel $data['contents']
        // Argumen ketiga TRUE pada $this->_ci->load->view() akan membuat output dikembalikan sebagai string
        $data['contents'] = $this->_ci->load->view($content_view_file, $data, TRUE);

        // Memuat template utama dan mengirimkan semua data (termasuk $data['contents'])
        // Jika $return adalah TRUE, output dari template utama akan dikembalikan sebagai string
        // Jika FALSE, akan langsung ditampilkan ke browser
        if ($return) {
            return $this->_ci->load->view($template_view_file, $data, TRUE);
        } else {
            $this->_ci->load->view($template_view_file, $data, FALSE);
        }
    }
}
/* End of file Template.php */
/* Location: ./application/libraries/Template.php */
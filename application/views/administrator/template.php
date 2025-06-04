<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Data default dan data dari controller
$title_page = isset($title) ? $title : (isset($iden['nama_website']) ? $iden['nama_website'] . ' - Admin Panel' : 'Admin Panel');
$website_name_template = isset($website_name) ? $website_name : (isset($iden['nama_website']) ? $iden['nama_website'] : 'Admin Panel');
$user_nama_lengkap = $this->session->userdata('nama_lengkap') ? $this->session->userdata('nama_lengkap') : 'Administrator';
$user_level = $this->session->userdata('level') ? ucfirst($this->session->userdata('level')) : 'Admin';
$user_foto = $this->session->userdata('foto') ? $this->session->userdata('foto') : 'default_avatar.png'; // Ganti dengan avatar default Anda
$user_foto_path = base_url('asset/foto_user/' . $user_foto); // Sesuaikan path

if (!$this->session->userdata('foto') && $this->session->userdata('username')) {
    $usr_data_template = $this->Model_app->view_where('rb_reseller', array('username' => $this->session->userdata('username')))->row_array();
    if ($usr_data_template && !empty($usr_data_template['foto'])) {
        $user_foto_path = base_url('asset/foto_user/' . $usr_data_template['foto']);
    } else {
        $user_foto_path = base_url('asset/adminlte/dist/img/avatar5.png'); // Default AdminLTE avatar
    }
}

$asset_path_admin = base_url('asset/adminlte/'); // Path ke aset AdminLTE
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo htmlspecialchars($title_page); ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <link rel="stylesheet" href="<?php echo $asset_path_admin; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $asset_path_admin; ?>bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo $asset_path_admin; ?>bower_components/Ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="<?php echo $asset_path_admin; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $asset_path_admin; ?>dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="<?php echo $asset_path_admin; ?>dist/css/skins/skin-blue.min.css">
    <link rel="stylesheet" href="<?php echo $asset_path_admin; ?>bower_components/morris.js/morris.css">
    <link rel="stylesheet" href="<?php echo $asset_path_admin; ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">


    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
        .main-header .navbar {
            /* Kustomisasi warna navbar jika skin default tidak cukup */
            /* background-color: #34495e !important; */
        }
        .main-header .logo {
            /* background-color: #2c3e50 !important; */
        }
        .content-wrapper {
            background-color: #ecf0f5; /* Warna latar konten yang sedikit berbeda */
        }
        .alert-fixed-template {
            position: fixed;
            top: 60px; /* Sesuaikan jika tinggi header berbeda */
            right: 20px;
            width: auto;
            min-width: 300px;
            max-width: 450px;
            z-index: 1051; /* Di atas content-wrapper */
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .breadcrumb {
            background-color: #f9f9f9; /* Latar breadcrumb lebih lembut */
            border-radius: 4px;
            margin-bottom: 15px;
        }
        /* Styling untuk box/card yang lebih modern */
        .box {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            border-top: 3px solid #007bff; /* Warna aksen primer */
        }
        .box.box-primary { border-top-color: #007bff; }
        .box.box-success { border-top-color: #28a745; }
        .box.box-info    { border-top-color: #17a2b8; }
        .box.box-warning { border-top-color: #ffc107; }
        .box.box-danger  { border-top-color: #dc3545; }
    </style>
</head>
<body class="hold-transition skin-blue sidebar-mini fixed"> 
<div class="wrapper">

    <header class="main-header">
        <?php
        if (file_exists(APPPATH.'views/administrator/main_header.php')) {
            // Anda mungkin perlu meneruskan variabel $iden atau $website_name_header jika header membutuhkannya
            $header_data['website_name_header'] = $website_name_template;
            $header_data['nama_lengkap_header'] = $user_nama_lengkap;
            $header_data['level_header'] = $user_level;
            $header_data['path_foto_user_header'] = $user_foto_path;
            // $header_data['iden'] = isset($iden) ? $iden : null; // Jika header butuh data identitas
            $this->load->view('administrator/main_header', $header_data);
        } else {
            echo "";
        }
        ?>
    </header>

    <aside class="main-sidebar">
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?php echo $user_foto_path; ?>" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p><?php echo $user_nama_lengkap; ?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>

            <form action="#" method="get" class="sidebar-form">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Cari...">
                    <span class="input-group-btn">
                        <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                    </span>
                </div>
            </form>

            <?php
            if (file_exists(APPPATH.'views/administrator/menu_admin.php')) {
                // Jika menu_admin.php membutuhkan Model_app untuk umenu_akses,
                // pastikan Model_app sudah di-load di controller Administrator sebelum template ini di-load.
                $this->load->view('administrator/menu_admin');
            } else {
                echo "<ul class='sidebar-menu' data-widget='tree'><li class='header text-red'>ERROR: menu_admin.php tidak ditemukan!</li></ul>";
            }
            ?>
        </section>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                <?php echo isset($page_title) ? htmlspecialchars($page_title) : htmlspecialchars($title_page); ?>
                <small><?php echo isset($page_desc) ? htmlspecialchars($page_desc) : 'Panel Kontrol'; ?></small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo site_url('administrator/home'); ?>"><i class="fa fa-dashboard"></i> Beranda</a></li>
                <?php if (isset($breadcrumb) && is_array($breadcrumb)): ?>
                    <?php foreach ($breadcrumb as $bc_item): ?>
                        <?php if (isset($bc_item['url']) && !empty($bc_item['url'])): ?>
                            <li><a href="<?php echo $bc_item['url']; ?>"><?php echo htmlspecialchars($bc_item['title']); ?></a></li>
                        <?php else: ?>
                            <li class="active"><?php echo htmlspecialchars($bc_item['title']); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php elseif (isset($page_title) && ($this->uri->segment(2) !== 'home' && $this->uri->segment(2) !== '')): ?>
                     <li class="active"><?php echo htmlspecialchars($page_title); ?></li>
                <?php endif; ?>
            </ol>
        </section>

        <section class="content">
            <?php if($this->session->flashdata('message')): ?>
            <div class="alert alert-info alert-dismissible fade in alert-fixed-template" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <?php echo $this->session->flashdata('message'); ?>
            </div>
            <?php endif; ?>

            <?php
            // Di sinilah konten utama dari view spesifik akan dimuat oleh library Template Anda
            if (isset($contents)) {
                echo $contents;
            } elseif (isset($template['body'])) { // Fallback jika library Anda menggunakan $template['body']
                echo $template['body'];
            } else {
                echo "<div class='alert alert-danger text-center'><strong>Error:</strong> Konten tidak dapat dimuat. Variabel konten (biasanya \$contents) tidak ditemukan di template. Periksa library Template Anda.</div>";
            }
            ?>
        </section>
        </div>
    <footer class="main-footer">
        <?php
        // Memuat file footer
        if (file_exists(APPPATH.'views/administrator/footer.php')) {
             // Anda mungkin perlu meneruskan variabel $iden atau $website_name_footer jika footer membutuhkannya
            $footer_data['website_name_footer'] = $website_name_template;
            $footer_data['iden'] = isset($iden) ? $iden : null;
            $this->load->view('administrator/footer', $footer_data);
        } else {
            echo "";
        }
        ?>
    </footer>

    <div class="control-sidebar-bg"></div>
</div>
<script src="<?php echo $asset_path_admin; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo $asset_path_admin; ?>bower_components/jquery-ui/jquery-ui.min.js"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button); // Resolve conflict jQuery UI dan Bootstrap tooltip
</script>
<script src="<?php echo $asset_path_admin; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<script src="<?php echo $asset_path_admin; ?>bower_components/raphael/raphael.min.js"></script>
<script src="<?php echo $asset_path_admin; ?>bower_components/morris.js/morris.min.js"></script>
<script src="<?php echo $asset_path_admin; ?>bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<script src="<?php echo $asset_path_admin; ?>plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?php echo $asset_path_admin; ?>plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<script src="<?php echo $asset_path_admin; ?>bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<script src="<?php echo $asset_path_admin; ?>bower_components/moment/min/moment.min.js"></script>
<script src="<?php echo $asset_path_admin; ?>bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<script src="<?php echo $asset_path_admin; ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo $asset_path_admin; ?>plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<script src="<?php echo $asset_path_admin; ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo $asset_path_admin; ?>bower_components/fastclick/lib/fastclick.js"></script>
<script src="<?php echo $asset_path_admin; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo $asset_path_admin; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script src="<?php echo $asset_path_admin; ?>dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function () {
    // Contoh inisialisasi untuk DataTables
    // $('.datatables').DataTable(); // Jika Anda menggunakan class .datatables pada tabel
    // $('#example1').DataTable(); // Sesuai PDF sering ada #example1

    // Script untuk auto-hide alert flashdata
    window.setTimeout(function() {
        $(".alert-fixed-template").fadeTo(500, 0).slideUp(500, function(){
            $(this).remove();
        });
    }, 7000); // Alert hilang setelah 7 detik

    // Untuk mengaktifkan kembali menu treeview AdminLTE jika ada
    // $('.sidebar-menu').tree(); // Jika Anda tidak menggunakan data-widget="tree" di HTML

    // Contoh inisialisasi Datepicker
    // $('.datepicker').datepicker({
    //   autoclose: true,
    //   format: 'yyyy-mm-dd'
    // });

    // Contoh inisialisasi WYSIHTML5 Editor
    // $('.wysihtml5').wysihtml5();
  });
</script>

</body>
</html>
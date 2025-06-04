<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Default values dan helper (sama seperti sebelumnya, bisa disesuaikan)
$rows = isset($rows) ? (object)$rows : (object)[
    'id_produk' => '', 'id_kategori_produk' => '', 'id_kategori_produk_sub' => '',
    'nama_produk' => '', 'satuan' => '', 'berat' => '', 'harga_beli' => '',
    'harga_konsumen' => '', 'keterangan' => '', 'gambar' => ''
];
$record = isset($record) ? $record : []; // Kategori utama
$sub_kategori_produk_terpilih = isset($sub_kategori_produk_terpilih) ? $sub_kategori_produk_terpilih : [];
$disk = isset($disk) ? (object)$disk : (object)['diskon' => 0];
$stok_saat_ini = isset($stok_saat_ini) ? $stok_saat_ini : 0;
$controller_name_path = isset($controller_name) ? $controller_name : 'reseller';

if (!function_exists('get_first_image_filename')) {
    function get_first_image_filename($gambar_string) {
        if (!empty($gambar_string)) {
            $img_array = explode(';', $gambar_string);
            return trim($img_array[0]);
        }
        return NULL;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - <?php echo htmlspecialchars($rows->nama_produk ?: 'Produk Baru'); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f6; /* Warna latar yang lebih lembut */
            font-family: 'Inter', sans-serif; /* Font modern */
            color: #333;
        }
        /* Mengimpor font Inter dari Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        .card-edit-produk {
            background-color: #fff;
            border-radius: 12px; /* Sudut lebih membulat */
            box-shadow: 0 8px 25px rgba(0,0,0,0.08); /* Bayangan lebih halus */
            margin-top: 30px;
            margin-bottom: 30px;
            overflow: hidden; /* Untuk menjaga border-radius gambar */
        }
        .card-header-custom {
            background-color: #007bff; /* Biru primer Bootstrap */
            color: white;
            padding: 20px 25px;
            border-bottom: none;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .card-header-custom h4 {
            margin: 0;
            font-weight: 600;
        }
        .card-body-custom {
            padding: 25px 30px; /* Padding lebih besar */
        }
        .form-group {
            margin-bottom: 1.75rem; /* Jarak antar grup form */
        }
        .form-control {
            border-radius: 8px; /* Sudut input lebih membulat */
            border: 1px solid #ced4da;
            padding: 0.75rem 1rem; /* Padding input */
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .control-label {
            font-weight: 500; /* Berat font label */
            color: #495057;
            margin-bottom: .5rem; /* Jarak label ke input */
        }
        .btn-submit-custom {
            background-color: #28a745; /* Hijau untuk submit */
            border-color: #28a745;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 8px;
            transition: background-color .2s ease;
        }
        .btn-submit-custom:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-cancel-custom {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            color: #6c757d;
        }
        .image-preview-container {
            margin-top: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px dashed #ced4da;
        }
        .image-preview-container img {
            max-width: 150px;
            max-height: 150px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .current-image-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .upload-instructions {
            font-size: 0.85rem;
            color: #6c757d;
        }
        /* Style untuk input group harga */
        .input-group-text {
            border-radius: 8px 0 0 8px; /* Menyesuaikan border-radius input */
        }
        .form-row > .col, .form-row > [class*="col-"] {
            padding-right: 10px;
            padding-left: 10px;
        }
        .form-row {
            margin-right: -10px;
            margin-left: -10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card-edit-produk">
                <div class="card-header-custom">
                    <h4><i class="fas fa-pencil-alt mr-2"></i>Edit Detail Produk</h4>
                </div>
                <div class="card-body-custom">
                    <?php
                    if (isset($error_upload) && !empty($error_upload)) {
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $error_upload . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    }
                    if ($this->session->flashdata('message')) {
                        echo $this->session->flashdata('message');
                    }

                    $attributes = array('name' => 'form_edit_produk');
                    echo form_open_multipart($controller_name_path . '/edit_produk/' . $rows->id_produk, $attributes);
                    ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($rows->id_produk); ?>">

                    <div class="form-row">
                        <div class="form-group col-md-6 <?php if(form_error('b')) echo 'has-danger';?>">
                            <label for="nama_produk" class="control-label">Nama Produk</label>
                            <input type="text" class="form-control <?php if(form_error('b')) echo 'is-invalid';?>" name="b" value="<?php echo set_value('b', htmlspecialchars($rows->nama_produk)); ?>" placeholder="Contoh: Kemeja Batik Modern" required>
                            <?php echo form_error('b', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-6 <?php if(form_error('a')) echo 'has-danger';?>">
                            <label for="kategori" class="control-label">Kategori</label>
                            <select name="a" class="form-control <?php if(form_error('a')) echo 'is-invalid';?>" id="kategori_produk" onchange="getSubKategoriProduk()" required>
                                <option value="">Pilih Kategori</option>
                                <?php
                                foreach ($record as $kat) {
                                    $selected = ($rows->id_kategori_produk == $kat['id_kategori_produk']) ? "selected" : "";
                                    echo "<option value='" . htmlspecialchars($kat['id_kategori_produk']) . "' " . $selected . ">" . htmlspecialchars($kat['nama_kategori']) . "</option>";
                                }
                                ?>
                            </select>
                             <?php echo form_error('a', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                    </div>

                     <div class="form-group <?php if(form_error('aa')) echo 'has-danger';?>">
                        <label for="sub_kategori" class="control-label">Sub Kategori</label>
                        <select name="aa" class="form-control <?php if(form_error('aa')) echo 'is-invalid';?>" id="sub_kategori_produk">
                            <option value="">Pilih Sub Kategori</option>
                            <?php
                            if (isset($sub_kategori_produk_terpilih) && !empty($sub_kategori_produk_terpilih)) {
                                foreach ($sub_kategori_produk_terpilih as $sub_kat) {
                                    $selected_sub = ($rows->id_kategori_produk_sub == $sub_kat['id_kategori_produk_sub']) ? "selected" : "";
                                    echo "<option value='" . htmlspecialchars($sub_kat['id_kategori_produk_sub']) . "' " . $selected_sub . ">" . htmlspecialchars($sub_kat['nama_kategori_sub']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                         <?php echo form_error('aa', '<small class="form-text text-danger">', '</small>'); ?>
                    </div>

                    <div class="form-group <?php if(form_error('ff')) echo 'has-danger';?>">
                        <label for="keterangan" class="control-label">Deskripsi Produk</label>
                        <textarea class="form-control <?php if(form_error('ff')) echo 'is-invalid';?>" name="ff" rows="4" placeholder="Jelaskan detail produk Anda di sini..."><?php echo set_value('ff', htmlspecialchars($rows->keterangan)); ?></textarea>
                        <?php echo form_error('ff', '<small class="form-text text-danger">', '</small>'); ?>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4 <?php if(form_error('d')) echo 'has-danger';?>">
                            <label for="harga_modal" class="control-label">Harga Modal</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" class="form-control <?php if(form_error('d')) echo 'is-invalid';?>" name="d" value="<?php echo set_value('d', htmlspecialchars($rows->harga_beli)); ?>" placeholder="0">
                            </div>
                            <?php echo form_error('d', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-4 <?php if(form_error('f')) echo 'has-danger';?>">
                            <label for="harga_jual" class="control-label">Harga Jual</label>
                             <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" class="form-control <?php if(form_error('f')) echo 'is-invalid';?>" name="f" value="<?php echo set_value('f', htmlspecialchars($rows->harga_konsumen)); ?>" placeholder="0" required>
                            </div>
                            <?php echo form_error('f', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-4 <?php if(form_error('diskon')) echo 'has-danger';?>">
                            <label for="diskon" class="control-label">Diskon (Nominal)</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                <input type="number" class="form-control <?php if(form_error('diskon')) echo 'is-invalid';?>" name="diskon" value="<?php echo set_value('diskon', htmlspecialchars($disk->diskon)); ?>" placeholder="0">
                            </div>
                            <?php echo form_error('diskon', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                    </div>

                     <div class="form-row">
                        <div class="form-group col-md-4 <?php if(form_error('stok')) echo 'has-danger';?>">
                            <label for="stok" class="control-label">Stok Saat Ini (Efektif)</label>
                            <input type="number" class="form-control" value="<?php echo htmlspecialchars($stok_saat_ini); ?>" readonly title="Stok efektif (Pembelian - Penjualan)">
                        </div>
                        <div class="form-group col-md-4 <?php if(form_error('tambah_stok')) echo 'has-danger';?>">
                            <label for="tambah_stok" class="control-label">Tambah Stok</label>
                            <input type="number" class="form-control <?php if(form_error('tambah_stok')) echo 'is-invalid';?>" name="stok" value="<?php echo set_value('stok', 0); ?>" placeholder="0">
                            <?php echo form_error('stok', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                        <div class="form-group col-md-4 <?php if(form_error('berat')) echo 'has-danger';?>">
                            <label for="berat" class="control-label">Berat (gram)</label>
                            <input type="number" class="form-control <?php if(form_error('berat')) echo 'is-invalid';?>" name="berat" value="<?php echo set_value('berat', htmlspecialchars($rows->berat)); ?>" placeholder="0">
                            <?php echo form_error('berat', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                         <div class="form-group col-md-4 <?php if(form_error('c')) echo 'has-danger';?>">
                            <label for="satuan" class="control-label">Satuan</label>
                            <input type="text" class="form-control <?php if(form_error('c')) echo 'is-invalid';?>" name="c" value="<?php echo set_value('c', htmlspecialchars($rows->satuan)); ?>" placeholder="Pcs, Kg, Box">
                            <?php echo form_error('c', '<small class="form-text text-danger">', '</small>'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="gambar_produk" class="control-label">Gambar Produk</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="fileupload" name="userfile[]" multiple onchange="updateFileLabel(this)">
                            <label class="custom-file-label" for="fileupload" data-browse="Pilih File">Kosongkan jika tidak ganti...</label>
                        </div>
                        <small class="form-text upload-instructions">Anda bisa unggah beberapa gambar (gif, jpg, png, jpeg). Gambar pertama akan menjadi utama.</small>

                        <div class="image-preview-container">
                            <div id="dvPreview" class="d-flex flex-wrap"></div>
                             <?php
                            $gambar_produk_saat_ini_arr = !empty($rows->gambar) ? explode(';', $rows->gambar) : [];
                            if (!empty($gambar_produk_saat_ini_arr[0])):
                            ?>
                                <div class="mt-2">
                                    <p class="current-image-label mb-1"><strong>Gambar Saat Ini:</strong></p>
                                    <?php foreach($gambar_produk_saat_ini_arr as $gbr): if(empty(trim($gbr))) continue; ?>
                                    <img src="<?php echo base_url('asset/foto_produk/' . htmlspecialchars(trim($gbr))); ?>" alt="Gambar Produk" class="img-thumbnail" style="max-width: 100px; max-height: 100px; margin:3px;">
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="mt-2 text-secondary current-image-label">Belum ada gambar untuk produk ini.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="form-group text-right mb-0">
                        <a href="<?php echo site_url($controller_name_path); // Arahkan ke daftar produk reseller ?>" class="btn btn-cancel-custom mr-2"><i class="fas fa-times mr-1"></i> Batal</a>
                        <button type="submit" name="submit" class="btn btn-submit-custom"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                    </div>

                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script type="text/javascript">
    function updateFileLabel(input) {
        var fileNames = [];
        for (var i = 0; i < input.files.length; ++i) {
            fileNames.push(input.files[i].name);
        }
        var label = input.files.length > 1 ? input.files.length + ' file dipilih' : fileNames.join(', ');
        $(input).next('.custom-file-label').html(label || 'Kosongkan jika tidak ganti...');
    }

    $(function() {
        var dvPreview = $("#dvPreview");
        $("#fileupload").change(function() {
            dvPreview.html(""); // Clear old previews from this session
            if (this.files && this.files.length > 0) {
                 if (typeof(FileReader) != "undefined") {
                    var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpg|.jpeg|.gif|.png)$/i;
                    $(this.files).each(function(index, file) {
                        if (regex.test(file.name.toLowerCase())) {
                            var reader = new FileReader();
                            reader.onload = function(e) {
                                var img = $("<img />");
                                img.attr("style", "max-height:100px; margin:5px; border:1px solid #ddd; padding:3px; border-radius:4px;");
                                img.attr("src", e.target.result);
                                dvPreview.append(img);
                            }
                            reader.readAsDataURL(file);
                        } else {
                            dvPreview.append('<small class="text-danger d-block p-2">File: ' + file.name + ' tidak valid.</small>');
                        }
                    });
                } else {
                    alert("Browser Anda tidak mendukung FileReader untuk preview gambar.");
                }
            }
        });
    });

    function getSubKategoriProduk() {
        var id_kategori = $("#kategori_produk").val();
        var subKategoriDropdown = $("#sub_kategori_produk");
        var currentSubKategori = "<?php echo $rows->id_kategori_produk_sub; ?>"; // Untuk memilih kembali sub kategori yang sudah ada
        subKategoriDropdown.html('<option value="">- Memuat...</option>');

        if (id_kategori) {
            $.ajax({
                url: "<?php echo site_url($controller_name_path . '/get_sub_kategori_by_kategori_id'); ?>",
                type: "POST",
                data: {
                    id_kategori_produk: id_kategori,
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                dataType: "json",
                success: function(data) {
                    subKategoriDropdown.html('<option value="">Pilih Sub Kategori</option>');
                    if (data && data.length > 0) {
                        $.each(data, function(key, value) {
                            var selected = (value.id_kategori_produk_sub == currentSubKategori) ? ' selected' : '';
                            subKategoriDropdown.append('<option value="' + value.id_kategori_produk_sub + '"' + selected + '>' + value.nama_kategori_sub + '</option>');
                        });
                    } else {
                        subKategoriDropdown.append('<option value="">Tidak ada sub kategori</option>');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error: " + textStatus, errorThrown);
                    subKategoriDropdown.html('<option value="">Gagal memuat</option>');
                }
            });
        } else {
            subKategoriDropdown.html('<option value="">Pilih Kategori dulu</option>');
        }
    }

    // Panggil saat halaman dimuat jika kategori sudah terpilih (untuk mempertahankan subkategori saat edit)
    $(document).ready(function() {
        if ($("#kategori_produk").val()) {
            // Jika subkategori tidak dirender dari PHP (karena mungkin AJAX adalah satu-satunya cara), panggil ini.
            // Namun, karena kita sudah merender subkategori terpilih dari PHP, pemanggilan ini mungkin tidak
            // diperlukan kecuali jika logika PHP untuk merender subkategori awal dihilangkan.
            // getSubKategoriProduk();
        }
    });
</script>

</body>
</html>
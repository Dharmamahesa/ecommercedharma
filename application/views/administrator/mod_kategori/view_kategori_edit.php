<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Data default jika $rows tidak terdefinisi (seharusnya selalu ada dari controller)
$rows = isset($rows) && is_array($rows) ? $rows : array(
    'id_kategori'   => '',
    'nama_kategori' => '',
    'aktif'         => 'Y', // Default aktif
    'sidebar'       => ''
);
$controller_name_path = isset($controller_name) ? $controller_name : 'administrator';

?>

<div class="col-md-12">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fas fa-edit"></i> Edit Kategori Berita</h3>
        </div>
        <div class="box-body">
            <?php
            // Menampilkan pesan error atau sukses dari flashdata (jika ada)
            if ($this->session->flashdata('message')) {
                echo $this->session->flashdata('message');
            }

            $attributes = array('class' => 'form-horizontal', 'role' => 'form');
            echo form_open($controller_name_path . '/edit_kategoriberita/' . $rows['id_kategori'], $attributes);
            ?>

            <input type="hidden" name="id" value="<?php echo htmlspecialchars($rows['id_kategori']); ?>">

            <div class="form-group <?php if(form_error('a')) echo 'has-error';?>">
                <label for="nama_kategori" class="col-sm-3 control-label">Nama Kategori <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="a" placeholder="Masukkan Nama Kategori"
                           value="<?php echo set_value('a', htmlspecialchars($rows['nama_kategori'])); ?>" required>
                    <?php echo form_error('a', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>

            <div class="form-group <?php if(form_error('b')) echo 'has-error';?>">
                <label for="aktif" class="col-sm-3 control-label">Aktif <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                    <div class="radio">
                        <label style="margin-right: 10px;">
                            <input type="radio" name="b" value="Y" <?php echo set_radio('b', 'Y', ($rows['aktif'] == 'Y')); ?> required> Ya
                        </label>
                        <label>
                            <input type="radio" name="b" value="N" <?php echo set_radio('b', 'N', ($rows['aktif'] == 'N')); ?>> Tidak
                        </label>
                    </div>
                    <?php echo form_error('b', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>

            <div class="form-group <?php if(form_error('c')) echo 'has-error';?>">
                <label for="sidebar" class="col-sm-3 control-label">Posisi Sidebar</label>
                <div class="col-sm-4">
                    <input type="number" class="form-control" name="c" placeholder="Urutan di sidebar (Angka)"
                           value="<?php echo set_value('c', htmlspecialchars($rows['sidebar'])); ?>">
                    <small class="text-muted">Kosongkan jika tidak ditampilkan di sidebar tertentu, atau isi dengan nomor urut.</small>
                    <?php echo form_error('c', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>

            <div class="box-footer">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    <a href="<?php echo site_url($controller_name_path . '/kategoriberita'); ?>" class="btn btn-default pull-right"><i class="fas fa-times-circle"></i> Batal</a>
                </div>
            </div>

            <?php echo form_close(); ?>
        </div>
    </div>
</div>
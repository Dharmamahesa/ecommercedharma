<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 2 | <?php echo $title; ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/asset/admin/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/asset/admin/dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>/asset/admin/plugins/iCheck/square/blue.css">
    <style type="text/css">
        .sekolah{
            float: left;
            background-color: transparent;
            background-image: none;
            padding: 15px 15px;
            font-family: fontAwesome;
            color:#fff;
        }
        .sekolah:hover{
            color:#fff;
        }
    </style>
    </head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <a href="index.php" class="logo">
            <span class="logo-mini"></span>
            <span class="logo-lg"><b>ADMINISTRATOR</b> </span>
        </a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <?php if ($this->session->level=='admin'){ ?>
                        <li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-envelope-o"></i> Pesan Masuk
                                <?php $jmlh = $this->model_app->view_where ('tb_hubungi', array('dibaca'=>'N'))->num_rows(); ?>
                                <span class="label label-success"><?php echo $jmlh; ?></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have <?php echo $jmlh; ?> new messages</li>
                                <li>
                                    <ul class="menu">
                                        <?php
                                        // This part would ideally come from a separate 'menu_admin.php' or similar
                                        // and would dynamically load messages. For brevity, it's a placeholder here.
                                        echo "<li><a>No new messages.</a></li>";
                                        ?>
                                    </ul>
                                </li>
                                <li class="footer"><a href="<?php echo base_url(). $this->uri->segment(1); ?>/pesanmasuk">See All Messages</a></li>
                            </ul>
                        </li>
                    <?php } ?>
                    <li>
                        <a target='_BLANK' href="<?php echo base_url(); ?>"><i class="glyphicon glyphicon-new-window"> </i></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <aside class="main-sidebar">
        <?php $this->load->view('administrator/menu_admin'); ?>
        </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                <?php echo $title; ?>
                <small>Control panel</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active"><?php echo $title; ?></li>
            </ol>
        </section>

        <section class="content">
            <?php echo $contents; ?>
        </section></div><footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 2.3.0
        </div>
        <strong>Copyright &copy; 2021 - <?php echo date('Y'); ?></strong> All rights reserved.
    </footer>

</div><script src="<?php echo base_url(); ?>/asset/admin/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<script src="<?php echo base_url(); ?>/asset/admin/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>/asset/admin/plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>
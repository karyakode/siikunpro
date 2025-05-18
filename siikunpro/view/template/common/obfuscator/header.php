<!DOCTYPE html>
  <!-- saved from url=(0043)http://127.0.0.1/sebongsoft/siikun_app/2.2/ -->
  <html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title> <?php echo $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="PHP Obfuscator. This is a free tool to ofuscate your php files. Feel free to use it">
    <meta name="author" content="Sebongsoft APP">
    <meta name="keywords" content="php, siikun, sebongsoft, obfuscator, obfuscate, php obfuscator, free, online, script, encode scripts, decode scripts, obfuscate scripts">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  	<meta name="robots" content="noindex, nofollow">
  	<meta name="theme-color" content="#4285f4">
  	<link rel="icon" type="image/png" href="siikunpro/view/image/favicon-32x32.png" sizes="32x32">
  	<link rel="icon" type="image/png" href="siikunpro/view/image/favicon-16x16.png" sizes="16x16">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="siikunpro/view/javascript/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="siikunpro/view/javascript/pace-progress/themes/blue/pace-theme-minimal.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="siikunpro/view/javascript/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="siikunpro/view/javascript/toastr/toastr.min.css">

    <!-- CSS -->
    <link href="siikunpro/view/stylesheet/adminlte.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="siikunpro/view/stylesheet/stylesheet.css" />

    <style>
    body,
    .navbar.main-header,
    .content-wrapper,
    .main-footer {
      background: url('siikunpro/view/image/background.png') #f4f6f9 !important;
    }
    .layout-navbar-fixed .wrapper .content-wrapper {
        margin-top: calc(3.5rem + 15px);
    }
    .navbar.main-header {
      /*padding-bottom: 0rem !important;*/
    }
    .navbar.main-header .nav-tabs .nav-item.show .nav-link,
    .navbar.main-header .nav-tabs .nav-link.active {
        color: #fff;
        background: #17a2b8 linear-gradient(180deg,#3ab0c3,#17a2b8) repeat-x!important;
        border: 1px solid transparent;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
        border-bottom-left-radius: .25rem;
        border-bottom-right-radius: .25rem;
    }

    .navbar.main-header .nav-tabs.tab-gradient-danger .nav-item.show .nav-link,
    .navbar.main-header .nav-tabs.tab-gradient-danger .nav-link.active {
      background: #dc3545 linear-gradient(180deg,#e15361,#dc3545) repeat-x!important;
    }

    .navbar.main-header .nav-tabs.tab-gradient-success .nav-item.show .nav-link,
    .navbar.main-header .nav-tabs.tab-gradient-success .nav-link.active {
      background: #28a745 linear-gradient(180deg,#48b461,#28a745) repeat-x!important;
    }

    .navbar.main-header .nav-tabs.tab-gradient-dark .nav-item.show .nav-link,
    .navbar.main-header .nav-tabs.tab-gradient-dark .nav-link.active {
      background: #343a40 linear-gradient(180deg,#52585d,#343a40) repeat-x!important;
    }

    .navbar.main-header .nav-tabs .nav-item .nav-link:hover{
        background: transparent;
        border: 1px solid transparent;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
        border-bottom-left-radius: .25rem;
        border-bottom-right-radius: .25rem;

    }

    [data-toggle="collapse"]:hover,
    .nav-tabs .nav-item:hover,
    .nav-tabs .nav-item .nav-link:hover{
      cursor: pointer;
    }

    .card-primary.card-outline-tabs>.card-header .nav-link.active {
        border-top: 3px solid #007bff;
    }
    .card-primary.card-outline-tabs>.card-header .nav-link {
        border-top: 3px solid transparent;
    }
    </style>
  </head>

  <body class="hold-transition layout-top-nav sidebar-mini layout-footer-fixed layout-navbar-fixed pace-done" onloadd="cek_update()" style="background: #f4f6f9;">
    <div class="pace pace-inactive">
      <div class="pace-progress" data-progress-text="100%" data-progress="99" style="transform: translate3d(100%, 0px, 0px);">
        <div class="pace-progress-inner"></div>
      </div>
      <div class="pace-activity"></div>
    </div>
    <div class="wrapper">

      <nav class="main-header navbar navbar-expand-md navbar-light navbar-white border-bottom-0" style="/*background: #f4f6f9;*/">
          <div class="container" style="display: block;" >
            <div class="row" >
              <div class="col-12">
                <div class="navbar-brand">
                  <!--img src="../../dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"-->
                  <h1 class="m-0 text-dark">
                  <span class="brand-text font-weight-light">
                    <?php echo substr($appname,0,6).' '.strtoupper($mode_license); ?>
                  </span>
                  <span class="badge bagde-info text-success"><?php echo substr($version,1,3); ?></span>
                </h1>
                </div>

              </div>
            </div>

            <div class="row" >
              <div class="col-12">
                <ul class="nav nav-tabs tab-gradient-blue order-1 order-md-3 navbar-nav navbar-no-expand ml-auto border-bottom-0" id="custom-content-below-tab" role="tablist">
                  <li class="nav-item">
                    <span class="nav-link active" id="tab-dashboard" data-toggle="pill" href="#content-dashboard" role="tab" aria-controls="tab-dashboard" aria-selected="true"><?php echo $text_tab_dashboard; ?></span>
                  </li>
                  <li class="nav-item">
                    <span class="nav-link" id="tab-system-settings" data-toggle="pill" href="#content-system-settings" role="tab" aria-controls="tab-system-settings" aria-selected="false"><?php echo $text_tab_settings; ?></span>
                  </li>
                  <li class="nav-item">
                    <span class="nav-link" id="tab-system-cofiguration" data-toggle="pill" href="#content-system-cofiguration" role="tab" aria-controls="tab-system-cofiguration" aria-selected="false"><?php echo $text_tab_system_cofiguration; ?></span>
                  </li>
                  <li class="nav-item">
                    <span class="nav-link" id="custom-content-below-tentang-sistem-tab" data-toggle="pill" href="#content-system-information" role="tab" aria-controls="tab-system-information" aria-selected="false"><?php echo $text_tab_system_information; ?></span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

        </nav>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

          <!-- Content Header (Page header) -->
          <div class="content-header"></div>
          <!-- /.content-header -->

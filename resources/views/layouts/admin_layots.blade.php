<!DOCTYPE HTML>
<html>
<head>
<title>Dashboard OBBI</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="" />
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="stylesheet" href="/css/all.css">
@yield('css')
</head>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">
        <header class="main-header">
            <!-- Logo -->
            <a href="{{ url('/')}}" class="logo">
              <!-- mini logo for sidebar mini 50x50 pixels -->
              <span class="logo-mini"><b>O</b>T</span>
              <!-- logo for regular state and mobile devices -->
              <span class="logo-lg"><b>OBBI</b>Tech</span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">
              <!-- Sidebar toggle button-->
              <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
              </a>
        
              <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                  <!-- User Account: style can be found in dropdown.less -->
                  <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      <img src= {{ \Cookie::get('profile_pic')}} class="user-image" alt="User Image">
                      <span class="hidden-xs">{{Cookie::get('fullname')}}</span>
                    </a>
                    <ul class="dropdown-menu">
                      <!-- User image -->
                      <li class="user-header">
                        <img src={{ \Cookie::get('profile_pic')}} class="img-circle" alt="User Image">
                      </li>
                      <!-- Menu Footer-->
                      <li class="user-footer">
                        <div class="pull-left">
                          <a href="#" class="btn btn-default btn-flat">Profile</a>
                        </div>
                        <div class="pull-right">
                            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();" 
                                class="btn btn-default btn-flat"><i class="fa fa-lock"></i> 
                                Sign Out</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </div>
                      </li>
                    </ul>
                  </li>
                </ul>
              </div>
            </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                <img src={{ \Cookie::get('profile_pic')}} class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                <p>{{Cookie::get('fullname')}}</p>
                </div>
            </div>
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">MAIN NAVIGATION</li>
                <li class="treeview">
                <a href="{{ url('/home')}}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                    <span class="pull-right-container">
                    </span>
                </a>
                </li>
                <li class="treeview">
                <a href="#">
                    <i class="fa fa-files-o"></i>
                    <span>Master</span>
                    <span class="pull-right-container">
                    <span class="label label-primary pull-right"></span>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="{{ route('user.index') }}"><i class="fa fa-circle-o"></i> User</a></li>
                    <li><a href=""><i class="fa fa-circle-o"></i> Produk</a></li>
                    <li><a href=""><i class="fa fa-circle-o"></i> Warna</a></li>
                    <li><a href=""><i class="fa fa-circle-o"></i> Size</a></li>
                </ul>
                </li>
                <li>
                  <a href="{{ url('/saldo')}}">
                    <i class="fa fa-bookmark"></i>
                    <span>Saldo</span>
                    <span class="pull-right-container">
                      <span class="label label-primary pull-right"></span>
                      </span>
                    </a>
                </li>
                <li class="treeview">
                <a href="#">
                    <i class="fa fa-trophy"></i>
                    <span>Jabatan</span>
                    <span class="pull-right-container">
                    <span class="label label-primary pull-right"></span>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/badge/opf"><i class="fa fa-circle-o"></i> OPF</a></li>
                    <li><a href=""><i class="fa fa-circle-o"></i> Mitra</a></li>
                </ul>
                </li>
                <li>
                  <a href=""><i class="fa fa-btn fa-shopping-cart"></i> 
                    <span>Orders</span>
                    <span class="pull-right-container">
                    <span class="label label-primary pull-right"></span>
                    </span></a></a>
                </li>
				
				<li class="treeview">
                <a href="#">
                    <i class="fa fa-folder-open"></i>
                    <span>Laporan</span>
                    <span class="pull-right-container">
                    <span class="label label-primary pull-right"></span>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/laporan/laporan_customer"><i class="fa fa-circle-o"></i> Laporan Data Customer </a></li>
					<li><a href="/laporan/laporan_opf"><i class="fa fa-circle-o"></i> Laporan Data OPF </a></li>
					<li><a href="/laporan/laporan_herobi"><i class="fa fa-circle-o"></i> Laporan Data Herobi </a></li>
					<li><a href="/laporan/laporan_saldo"><i class="fa fa-circle-o"></i> Laporan Transaksi Saldo</a></li>
					<li><a href="/laporan/laporan_saldoamal"><i class="fa fa-circle-o"></i> Laporan Transaksi Saldo Amal</a></li>
					<li><a href="/laporan/laporan_saldoasik"><i class="fa fa-circle-o"></i> Laporan Transaksi Saldo Asik</a></li>
					<li><a href="/laporan/laporan_referalopf"><i class="fa fa-circle-o"></i> Laporan Referal OPF</a></li>
				</ul>
                </li>
            </ul>
            </section>
            <!-- /.sidebar -->
        </aside>

        <div class="content-wrapper">
            @yield('content')
        </div>

        <!-- /.content-wrapper -->
        <footer class="main-footer">
          <div class="pull-right hidden-xs">
            <b>Version</b> 2.4.0
          </div>
          <strong>Copyright &copy; 2018 <a href="https://obbi.id">OBBI</a>.</strong> All rights
          reserved.
        </footer>
        <div class="control-sidebar-bg"></div>
    </div>

</body>

<script src="/js/all.js"></script>
@yield('js')

</html>
@yield('modal')
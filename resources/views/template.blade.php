<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials_head')
	@yield('head')
</head>
<body class="wrapper">

    <!-- Navigasi -->

    <?php
        $nama = auth()->user()->name;
        if(strlen($nama)>15)
            $nama=substr($nama,0,15)."...";
    ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo" width="30" height="30" class="d-inline-block align-top">
            </a>                
            <a class="navbar-brand" href="#">WA BLAST BPJN PROV. SULTRA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="{{ url('/') }}">Halaman Depan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/pesan') }}">Pesan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/pegawai') }}">Pegawai</a>
                    </li>
                </ul>
                <div class="d-flex ms-auto">
                    <input class="form-control me-2" type="search" placeholder="Search" id="search-input" aria-label="Search">
                    <button type="submit" class="btn btn-primary cari-data">Cari</button>
                </div>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('images/user-avatar.png') }}" alt="User" width="30" height="30" class="rounded-circle">
                            {{ $nama }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="#">Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ url('/logout') }}">Keluar</a></li>
                        </ul>
                    </li>
                </ul>

            </div>
        </div>
    </nav>

    <!-- Konten -->
    <div class="container mt-3">
	    @yield('container')
    </div>


    <!-- Footer -->
    @include('partials_footer')    
    @yield('script')
</body> 
</html>


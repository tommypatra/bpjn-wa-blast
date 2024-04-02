@extends('template')

@section('head')
<title>Kirim Whatsapp Blast</title>
@endsection

@section('container')

    <h3>Dashboard WA Blast BPJN Prov. Sultra</h3>
    <hr>
    Selamat Datang {{ auth()->user()->name }}
    <p>Review Tahun {{ date('Y') }}</p>

    <div>
        <p class="card-text">
            <ol class="list-group list-group-numbered">
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        Total Penerima
                    </div>
                    <span class="badge text-bg-primary rounded-pill" id="total">0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        Jumlah Terkirim
                    </div>
                    <span class="badge text-bg-primary rounded-pill" id="sudah">0</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        Jumlah Belum Terkirim
                    </div>
                    <span class="badge text-bg-primary rounded-pill" id="belum">0</span>
                </li>
            </ol>                
        </p>
    </div>

    <p>Grafik Perkembangan Tahun {{ date('Y') }}</p>

    <div class="container text-center">
        <div class="row">
            <div class="col-lg-8">
                <canvas id="monthlyChart"></canvas>
            </div>
            <div class="col-lg-4">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>    
@endsection

@section('script')
<script src="{{ asset('js/token.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // alert('selamat datang');
    var thn={{ date('Y') }};

    $.get('/api/jumlah_pesan_bulanan/'+thn, function(response) {
        var values = Object.values(response); // Mendapatkan nilai-nilai dari objek JSON
        grafikBatang(values);
    }).fail(function() {
        console.error('Gagal memuat data.');
    });

    $.get('/api/jumlah_pegawai', function(response) {
        var values = Object.values(response); // Mendapatkan nilai-nilai dari objek JSON
        pieChart(values);
    }).fail(function() {
        console.error('Gagal memuat data.');
    });    

    $.get('/api/pengiriman_wa_blast/'+thn, function(response) {
        console.log(response);
        $('#total').text(parseInt(response.total));
        $('#sudah').text(parseInt(response.berhasil));
        $('#belum').text(parseInt(response.gagal)+parseInt(response.belum));
    }).fail(function() {
        console.error('Gagal memuat data.');
    });    

    function grafikBatang(pData) {
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        // const data = [10, 20, 15, 30, 25, 40, 35, 50, 45, 60, 55, 70];

        const monthlyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jumlah WA Blast bulanan',
                    data: pData,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)', // Warna latar belakang batang
                    borderColor: 'rgba(54, 162, 235, 1)', // Warna garis batas batang
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true // Mulai sumbu Y dari nol
                    }
                }
            }
        });
    }
    
    function pieChart(pData){
        var data = {
            labels: ['Pegawai Aktif', 'Pegawai Tidak Aktif'],
            datasets: [{
                data: pData, 
                backgroundColor: ['#36A2EB', '#FF6384'] 
            }]
        };
        var options = {
            responsive: true,
            maintainAspectRatio: false,
            title: {
                display: true,
                text: 'Status Pegawai'
            }
        };
        var ctx = document.getElementById('pieChart').getContext('2d');
        var pieChart = new Chart(ctx, {
            type: 'pie',
            data: data,
            options: options
        });
    }


</script>
@endsection

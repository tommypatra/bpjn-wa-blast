@extends('template')

@section('head')
<title>Kirim Whatsapp Blast</title>
@endsection

@section('container')
    <div class="accordion" id="frmAcr">
        <div class="accordion-item">
            <h2 class="accordion-header" id="frm-acr-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#bodyAcr" aria-expanded="false" aria-controls="aria-acr-controls">
                    <h3>Kirim Pesan</h3>
                </button>
            </h2>
            <div id="bodyAcr" class="accordion-collapse collapse show" aria-labelledby="frm-acr-header" data-bs-parent="#frmAcr">
                <div class="accordion-body">
                    <div class="mb-3">
                        <span id="judul"></span>
                    </div>
                    <div class="mb-3">
                        <span id="pesan"></span>
                    </div>
                    <button class="btn btn-primary" id="prosesBaru">Buat Proses Baru</button>
                </div>
            </div>
        </div>
    </div>


    <hr>

    <h3>Daftar Proses Pengiriman WA Blast <span class="proses-berjalan"></span></h3>

    <ul class="nav nav-tabs" id="prosesTabs" role="tablist"></ul>
    <div class="tab-content" id="prosesTabContent">
        <div id="progress-kirim" style="display:none;">
            <a href="javascript:;" class="btn btn-success btn-sm mt-2 bt-2" onclick="kirimSemua()">Kirim Semua</a>
            <div>Progres Pesan Terkirim</div>
            <div class="progress" role="progressbar" aria-label="data" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: 0%"></div> <span class="progress-value">0%</span>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-sm-6">
                <span class="badge rounded-pill text-bg-success" id="jumlah-berhasil">0</span>
                <span class="badge rounded-pill text-bg-danger" id="jumlah-gagal">0</span>
                <span class="badge rounded-pill text-bg-primary" id="jumlah-belum">0</span> 
            </div>
            <div class="col-sm-6">
                <div class="input-group justify-content-end">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="refresh">Refresh</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false"  id="btn-paging">
                            Paging
                        </button>
                    <ul class="dropdown-menu dropdown-menu-end" id="list-select-paging">
                    </ul>
                </div>
            </div>
        </div>

        <div class="table-responsive">

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col"><input type='checkbox' class='cek-semua'></th>
                        <th scope="col">No</th>
                        <th scope="col">Nama Lengkap</th>
                        <th scope="col">No. HP</th>
                        <th scope="col">Update Waktu</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody id="data-list">
                    <!-- Data pesan akan dimuat di sini -->
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center" id="pagination">
            </ul>
        </nav>
    </div>

@endsection

@section('script')
<script src="{{ asset('js/pagination.js') }}"></script>
<script src="{{ asset('js/token.js') }}"></script>

<script>
    var vid={{ $id }};
    dataProses(vid);
    
    // Handle page change
    $(document).on('click', '.page-link', function() {
        dataKirim($(this).data('page'));
    });

    // Handle search form submission
    $('.cari-data').click(function(){
        dataKirim(1);
    })

    $('#refresh').on('click', function(e) {
        dataKirim(1);
    });

    $('.dropdown-item').on('click', function() {
        vPaging=$(this).data('nilai');
        dataProses(vid);
    })

    function dataProses(id){
        $.ajax({
            url: '/api/proses',
            method: 'GET',
            data: {
                wa_pesan_id: id,
            },            
            dataType: 'json',
            success: function(response) {
                $("#judul").html(response.judul);
                $("#pesan").html(response.pesan);
                var tabsHTML = '';
                $.each(response.proses, function(index, proses) {
                    var actv = (index==0)?'active':'';
                    tabsHTML = tabsHTML+` <li class="nav-item" role="presentation">
                                            <button class="nav-link ${actv}" id="pills-home-tab" onclick="dataKirim()" data-proses_id="${proses.id}" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">${proses.created_at}
                                                <a href="javascript:;" class="btn btn-danger btn-sm" onclick="hapusData(${proses.id})">x</a>    
                                            </button>
                                        </li>`;
                });
                $('#prosesTabs').html(tabsHTML);
                dataKirim();
            },
            error: function() {
                alert('data tidak ditemukan!');
            }
        });                
    }

    function dataKirim(page=1){
        var id = $('.nav-tabs .nav-link.active').attr('data-proses_id');
        var search = $('#search-input').val();
        $.ajax({
            url: '/api/kirim?page=' + page + '&search=' + search + '&paging=' + vPaging,
            method: 'GET',
            data: {
                proses_id: id,
            },            
            dataType: 'json',
            success: function(response) {
                var dataList = $('#data-list');
                var pagination = $('#pagination');
                var sudah = 0;
                var belum = 0;
                var gagal = 0;
                var total = 0;
                var sudah_progress = 0;

                dataList.empty();
                
                $.each(response.data, function(index, dt) {
                    var cekProses='';
                    var btnHps='';

                    total++;
                    if (dt.is_berhasil === "gagal") {
                        gagal++;
                    } else if (dt.is_berhasil === "sudah") {
                        sudah++;
                    } else {
                        belum++;
                    }

                    if(dt.is_berhasil!=='sudah'){
                        cekProses=`<input type='checkbox' class='cek-kirim' data-kirim_pesan_id='${dt.id}'>`;
                        btnHps=`<button href="javascript:;" class="btn btn-danger btn-sm" onclick="hapusTujuan(${dt.id})">x</button>`;
                    }

                    dataList.append(`<tr> 
                            <td>${cekProses}</td> 
                            <td>${dt.nomor}</td> 
                            <td>${dt.pegawai.nama}</td> 
                            <td>${dt.pegawai.hp}</td> 
                            <td>${dt.updated_at}</td> 
                            <td>${dt.is_berhasil}</td> 
                            <td>${btnHps}</td>
                        </tr>`);
                });


                sudah_progress = Math.round((sudah/total)*100);

                $('#progress-kirim').hide();
                if(response.data.length>0){
                    $('.progress-bar').css('width', sudah_progress+'%');
                    $('.progress-value').text(sudah_progress+'%');
                    $('#progress-kirim').show();
                }

                $('#jumlah-berhasil').text('berhasil : '+sudah);
                $('#jumlah-gagal').text('gagal : '+gagal);
                $('#jumlah-belum').text('belum : '+belum);
                renderPagination(response, pagination);            
            },
            error: function() {
                alert('data tidak ditemukan!');
            }
        });                
    }    

    

    function hapusData(id){
        if(confirm('apakah anda yakin?'))
            $.ajax({
                url: '/api/proses/' + id,
                method: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    dataProses(vid);
                    // alert('data berhasil dihapus!');
                },
                error: function() {
                    alert('Gagal menghapus data.');
                }
            });        
    }

    function hapusTujuan(id){
        if(confirm('apakah anda yakin?'))
            $.ajax({
                url: '/api/kirim/' + id,
                method: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    dataKirim();
                    // alert('data berhasil dihapus!');
                },
                error: function() {
                    alert('Gagal menghapus data.');
                }
            });                
    }

    $(document).on('click', '.cek-semua', function(){
        $('.cek-kirim').prop('checked', this.checked);
    });


    $('#prosesBaru').on('click', function(e) {
        e.preventDefault();
        if(confirm('apakah anda yakin?'))
            $.ajax({
                url: '/api/kirim',
                type: 'POST',
                data: {
                    wa_pesan_id: vid,
                },            
                dataType: 'json',
                success: function(response) {
                    // alert('data berhasil dikirim!');
                    dataProses(vid);
                },
                error: function() {
                    alert('Gagal mengirim data.');
                }
            });
    });

    function updateStatus(kirim_pesan_id){
        $.ajax({
            url: '/api/kirim/'+kirim_pesan_id,
            type: 'PUT',
            data: {
                is_berhasil: 1,
            },            
            dataType: 'json',
            success: function(response) {
                console.log('berhasil update status');
            },
            error: function() {
                console.log('Gagal update status');
            }
        });
    }

    var dataTerproses = 0;
    var dataTerpilih = 0;
    function kirimSemua() {
        dataTerpilih = $('.cek-kirim:checked').length;
        dataTerproses = 0;
        if(confirm('apakah anda yakin?')){
            $('.proses-berjalan').html('');
            proses(0);
        }
    }

    function proses(index) {
        var rows = $('#data-list tr');
        if (index >= rows.length) {
            return;
        }
        var row = $(rows[index]);
        var isChecked = row.find('.cek-kirim').prop('checked');
        var keterangan = row.find('td:nth-child(6)').text();
        var kirim_pesan_id = row.find('.cek-kirim').data('kirim_pesan_id');
        if (isChecked && keterangan!=='sudah') {
            var formData = {
                number: row.find('td:nth-child(4)').text(),
                message: $('#pesan').html(),
                file_dikirim : null,
            };
            row.find('td:nth-child(6)').text('kirim...');

            $.ajax({
                type: 'POST',
                url: 'https://wa-bpjn.gwpsvc.net/send-message',
                data: formData,
                success: function(response){
                    console.log('pesan terkirim:', response);
                    updateStatus(kirim_pesan_id);
                    row.find('.cek-kirim').remove();
                    row.find('td:nth-child(6)').text('sudah');
                    proses(index + 1);
                    // setTimeout(function() {
                    //     proses(index + 1);
                    // }, 1000);
                    dataTerproses++;
                    $('.proses-berjalan').html(Math.ceil((dataTerproses / dataTerpilih) * 100) + '%');
                },
                error: function(xhr, status, error){
                    console.log('Error kirim pesan:', error);
                    row.find('td:nth-child(6)').text('gagal');
                    proses(index + 1);
                    // setTimeout(function() {
                    //     proses(index + 1);
                    // }, 1000);
                    dataTerproses++;
                    $('.proses-berjalan').html(Math.ceil((dataTerproses / dataTerpilih) * 100) + '%');

                }
            });

        } else {
            proses(index + 1);
            // setTimeout(function() {
            //     proses(index + 1);
            // }, 1000);
            // dataTerproses++;
            // $('.proses-berjalan').html(Math.ceil((dataTerproses / dataTerpilih) * 100) + '%');
        }
    }

</script>
@endsection

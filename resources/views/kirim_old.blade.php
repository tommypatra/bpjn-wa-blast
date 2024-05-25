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
    <div class="tab-content" id="prosesTabContent"></div>

@endsection

@section('script')
<script src="{{ asset('js/pagination.js') }}"></script>
<script src="{{ asset('js/token.js') }}"></script>

<script>

var vid={{ $id }};
$(document).ready(function(){
    dataProses(vid);

    function dataProses(id){
        $.ajax({
            url: '/api/kirim',
            method: 'GET',
            data: {
                wa_pesan_id: vid,
            },            
            dataType: 'json',
            success: function(response) {
                $("#judul").html(response.judul);
                $("#pesan").html(response.pesan);
                createTabs(response.proses);
                console.log(response);
            },
            error: function() {
                alert('data tidak ditemukan!');
            }
        });                
    }

    function hitungStatusPesan(pesan) {
        let sudah = 0;
        let belum = 0;
        let gagal = 0;
        let total = 0;

        pesan.forEach(item => {
            total++;
            if (item.is_berhasil === "gagal") {
                gagal++;
            } else if (item.is_berhasil === "sudah") {
                sudah++;
            } else {
                belum++;
            }
        });

        return { total: total,belum: belum, sudah: sudah, gagal : gagal };
    }
    
    function createTabs(dtProses) {
        var tabsHTML = '';
        var panesHTML = '';
        var jumlahProses =[];

        $.each(dtProses, function(index, proses) {
            var rowTable ='';
            var progress = hitungStatusPesan(proses.kirimpesan);
            // console.log(progress);

            var sudah_progress = (progress.sudah/progress.total)*100;

            $.each(proses.kirimpesan, function(index, pesan) {
                var cekProses='';
                if(pesan.is_berhasil!=='sudah'){
                    cekProses=`<input type='checkbox' class='cek-kirim' data-kirim_pesan_id='${pesan.id}'>`;
                }
                rowTable += `
                    <tr>
                        <td>${cekProses}</td>
                            <td>${index+1}</td>
                        <td>${pesan.pegawai.nama}</td>
                        <td>${pesan.pegawai.hp}</td>
                        <td>${pesan.updated_at}</td>
                        <td>${pesan.is_berhasil}</td>
                        <td><button href="javascript:;" class="btn btn-danger btn-sm" onclick="hapusTujuan(${pesan.id})">x</button></td>
                        
                    </tr>
                `;
            });
            // console.log(progress.total);
            
            tabsHTML += `
                <li class="nav-item" role="presentation">
                    <button class="nav-link${index === 0 ? ' active' : ''}" id="tab-${index}" data-bs-toggle="tab" data-bs-target="#pane-${index}" type="button" role="tab" aria-controls="pane-${index}" aria-selected="${index === 0 ? 'true' : 'false'}">${proses.created_at} <a href="javascript:;" class="btn btn-danger btn-sm" onclick="hapusData(${proses.id})">x</a> </button>
                </li>
            `;
            
            panesHTML += `
                <div class="tab-pane fade${index === 0 ? ' show active' : ''}" id="pane-${index}" role="tabpanel" aria-labelledby="tab-${index}">
                    <a href="javascript:;" class="btn btn-success btn-sm mt-2 bt-2 kirimSemua">Kirim Pesan Ditandai</a>
                    <div>Progress</div>
                    <div class="progress" role="progressbar" aria-label="data" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: ${sudah_progress}%"></div> ${sudah_progress}%
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
                                `+rowTable+`
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
        });

        $('#prosesTabs').html(tabsHTML);
        $('#prosesTabContent').html(panesHTML);
    }    
    

    function hapusData(id){
        if(confirm('apakah anda yakin?'))
            $.ajax({
                url: '/api/kirim/' + id,
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
                url: '/api/tujuan/' + id,
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

    $(document).on('click', '.cek-semua', function(){
        var tabAktif = $('.tab-pane.active');
        tabAktif.find('.cek-kirim').prop('checked', this.checked);
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

    $(document).on('click','.kirimSemua',function() {
        dataTerpilih = $('.cek-kirim:checked').length;
        dataTerproses = 0;
        if(confirm('apakah anda yakin?')){
            $('.proses-berjalan').html('');
            proses(0);
        }
    });

    function proses(index) {
        var tabAktif = $('.tab-pane.active');
        var rows = tabAktif.find('#data-list tr');
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
});

</script>
@endsection

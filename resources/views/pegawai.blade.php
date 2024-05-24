@extends('template')

@section('head')
<title>Daftar Pegawai</title>
@endsection

@section('container')
    <div class="accordion" id="frmAcr">
        <div class="accordion-item">
            <h2 class="accordion-header" id="frm-acr-header">
                <button class="accordion-button collapsed" id="tambahForm" type="button" data-bs-toggle="collapse" data-bs-target="#bodyAcr" aria-expanded="false" aria-controls="aria-acr-controls">
                    <h3>Pegawai Baru</h3>
                </button>
            </h2>
            <div id="bodyAcr" class="accordion-collapse collapse" aria-labelledby="frm-acr-header" data-bs-parent="#frmAcr">
                <div class="accordion-body">
                    <form id="form">
                        <input type="hidden" name="id" id="id" >
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama" required value="">
                        </div>
                        <div class="mb-3">
                            <label for="hp" class="form-label">Nomor HP</label>
                            <input type="text" class="form-control" id="hp" name="hp" required value="">
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Pegawai</button>
                        <button class="btn btn-warning" id="tambahBaru">Form Baru</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <hr>

    <h3>Daftar Pegawai</h3>
    <div class="row">
        <div class="col-sm-12">
            <div class="input-group justify-content-end">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="tambah" onclick="tambah()">Tambah</button>
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
                    <th scope="col">#</th>
                    <th scope="col">Nama Lengkap</th>
                    <th scope="col">No. HP</th>
                    <th scope="col">User</th>
                    <th scope="col">Waktu</th>
                    <th scope="col">Status Aktif</th>
                    <th scope="col">Aksi</th>
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
@endsection

@section('script')
<script src="{{ asset('js/pagination.js') }}"></script>
<script src="{{ asset('js/token.js') }}"></script>

<script>

    function tambah(){
        $('#bodyAcr').collapse('show'); // Menampilkan accordion
        resetForm();
        $('#nama').focus();
    }

    function loadData(page = 1, search = '') {
            $.ajax({
                url: '/api/pegawai?page=' + page + '&search=' + search + '&paging=' + vPaging,
                method: 'GET',
                success: function(response) {
                    var dataList = $('#data-list');
                    var pagination = $('#pagination');
                    dataList.empty();

                    $.each(response.data, function(index, dt) {
                        let defcek="";
                        if(dt.is_aktif) 
                            defcek="checked";

                        dataList.append(`<tr> 
                                <td>${dt.nomor}</td> 
                                <td>${dt.nama}</td> 
                                <td>${dt.hp}</td> 
                                <td>${dt.user.name}</td> 
                                <td>${dt.created_at_formatted}</td> 
                                <td> 
                                    <div class="form-check form-switch">
                                        <input class="form-check-input ganti-status" type="checkbox" role="switch" data-id="${dt.id}" ${defcek}>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-warning" onclick="ganti(${dt.id})" id="proses">Ganti</button>
                                        <button type="button" class="btn btn-danger" onclick="hapusData(${dt.id})" id="hapus">Hapus</button>
                                    </div>                                        
                                </td>
                            </tr>`);
                    });

                    renderPagination(response, pagination);
                }
            });
        }

        // Load pesan list on page load
        loadData();

        // Handle page change
        $(document).on('click', '.page-link', function() {
            var page = $(this).data('page');
            var search = $('#search-input').val();
            loadData(page, search);
        });
        
        $(document).on('change', '.ganti-status', function() {
            var vId = $(this).data('id');
            var row = $(this).closest('tr');
            var vMama = row.find('td:eq(1)').text();
            var vHp = row.find('td:eq(2)').text();
            var vIs_aktif = 1;
            if (!$(this).is(':checked')) 
                vIs_aktif = 0;
        
            // console.log(vData);
            $.ajax({
                url: '/api/pegawai/'+vId,
                type: 'PUT',
                data: {
                    nama:vMama,
                    hp:vHp,
                    is_aktif:vIs_aktif,
                },
                dataType: 'json',
                success: function(response) {
                    console.log('update status berhasil');
                    toastr.success('operasi berhasil dilakukan!', 'berhasil');
                    // loadData(); // Reload pesan list after submission
                },
                error: function() {
                    toastr.danger('operasi gagal dilakukan!', 'gagal');
                }
            });            
        });

        function resetForm(){
            $('#form input').val('');
            $('#form')[0].reset();
        }

        $('#tambahBaru').on('click', function(e) {
            e.preventDefault();
            let konfirmasi=false;
            if($('#nama').val()!=='' || $('#hp').val()!==''){
                konfirmasi=true;
            }            
            if(konfirmasi){
                if(confirm('yakin isian form dikosongkan untuk tambah baru?')){
                    resetForm();
                }
            }else{
                resetForm();
            }
            $('#nama').focus();
        });


        $('#refresh').on('click', function(e) {
            loadData();
        });

        $('.dropdown-item').on('click', function() {
            vPaging=$(this).data('nilai');
            loadData();
        })


        // Handle search form submission
        $('.cari-data').click(function(){
            var search = $("#search-input").val();
            if (search.length > 3) {
                loadData(1, search);
            } else if (search.length === 0) {
                loadData(1, '');
            }
        })

        function redirectTo(id){
            var goUrl = `{{ url('/proses/${id}') }}`;
            window.open(goUrl, '_blank');        
        }

        function ganti(id){
            $.ajax({
                url: '/api/pegawai/' + id,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    $('#id').val(response.id);
                    $('#nama').val(response.nama);
                    $('#hp').val(response.hp);
                    $('#bodyAcr').collapse('show'); // Menampilkan accordion
                    $('#nama').focus(); // Fokuskan input nama                
                },
                error: function() {
                    alert('maaf, data tidak ditemukan!');
                }
            });                
        }


        function hapusData(id){
            if(confirm('apakah anda yakin?'))
                $.ajax({
                    url: '/api/pegawai/' + id,
                    method: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        loadData();
                        toastr.success('operasi berhasil dilakukan!', 'berhasil');
                        // alert('data berhasil dihapus!');
                    },
                    error: function() {
                        alert('operasi gagal dilakukan!');
                    }
                });                
        }

        $('#frm-acr-header button').on('click', function() {
            resetForm();
            $('#nama').focus();
        });        

        $("#form").validate({
            submitHandler: function(form) {
                let vType=($('#id').val()==='')?'POST':'PUT';
                let vUrl = '/api/pegawai';
                if(vType==='PUT')
                    vUrl = vUrl+'/'+$('#id').val();

                $.ajax({
                    url: vUrl,
                    type: vType,
                    data: $(form).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        // alert('data berhasil dikirim!');
                        toastr.success('operasi berhasil dilakukan!', 'berhasil');
                        loadData(); // Reload pesan list after submission
                        resetForm();
                    },
                    error: function() {
                        alert('operasi gagal dilakukan!');
                    }
                });
            }
        });    
</script>
@endsection

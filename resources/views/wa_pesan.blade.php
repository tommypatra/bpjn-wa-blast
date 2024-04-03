@extends('template')

@section('head')
<title>Pesan Blast</title>
@endsection

@section('container')
    <div class="accordion" id="pesanAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="pesanHeader">
                <button class="accordion-button collapsed" id="tambahForm" type="button" data-bs-toggle="collapse" data-bs-target="#bodyPesan" aria-expanded="false" aria-controls="judulPesan">
                    <h3>Pesan Baru</h3>
                </button>
            </h2>
            <div id="bodyPesan" class="accordion-collapse collapse" aria-labelledby="pesanHeader" data-bs-parent="#pesanAccordion">
                <div class="accordion-body">
                    <form id="form">
                        <input type="hidden" name="id" id="id" >
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Pesan</label>
                            <input type="text" class="form-control" id="judul" name="judul" required >
                        </div>
                        <div class="mb-3">
                            <label for="pesan" class="form-label">Isi Pesan</label>
                            <textarea class="form-control" id="pesan" name="pesan" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Proses Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <hr>

    <h3>Daftar Pesan</h3>
    <div class="row">
        <div class="col-sm-12">
            <div class="input-group justify-content-end">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="refresh">Refresh</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
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
                    <th scope="col">Judul</th>
                    <th scope="col">Pesan</th>
                    <th scope="col">User</th>
                    <th scope="col">Terkirim</th>
                    <th scope="col">Gagal</th>
                    <th scope="col">Belum</th>
                    <th scope="col">Waktu</th>
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

    $('#tambahForm').on('click', function() {
        resetForm();
        $('#judul').focus();
    });        

    var oldValue="";
    $('#data-list').on('dblclick', 'td:nth-child(3)', function() {
        oldValue = $(this).text(); 
        $(this).html('<textarea class="form-control" id="edt-pesan" name="pesan">' + oldValue + '</textarea>'); // Ganti dengan textarea
        $('#edt-pesan').focus(); 
    });

    $('#data-list').on('dblclick', 'td:nth-child(2)', function() {
        oldValue = $(this).text(); 
        $(this).html('<input type="text" class="form-control" id="edt-judul" name="judul" value="' + oldValue + '">'); // Ganti dengan input teks
        $('#edt-judul').focus(); 
    });

    $('#data-list').on('focusout', 'input, textarea', function() {
        var tr = $(this).closest('tr');
        var newValue = $(this).val();
        $(this).closest('td').html(newValue);

        var wa_pesan_id = $(tr).data('wa_pesan_id');
        var judul = $(tr).find('td:nth-child(2)').text();
        var pesan = $(tr).find('td:nth-child(3)').text();    

        console.log('wa : '+wa_pesan_id+' ,judul : '+judul+' pesan : '+pesan);
        if(oldValue!==newValue)
            $.ajax({
                url: '/api/pesan/'+wa_pesan_id,
                type: 'PUT',
                data: {
                    judul:judul,
                    pesan:pesan,
                },
                dataType: 'json',
                success: function(response) {
                    toastr.success('operasi berhasil dilakukan!', 'berhasil');
                },
                error: function() {
                    alert('operasi gagal dilakukan!');
                }
            });  

    });
    
    function loadData(page = 1, search = '') {
            $.ajax({
                url: '/api/pesan?page=' + page + '&search=' + search + '&paging=' + vPaging,
                method: 'GET',
                success: function(response) {
                    var dataList = $('#data-list');
                    var pagination = $('#pagination');
                    dataList.empty();

                    $.each(response.data, function(index, pesan) {
                        dataList.append(`<tr data-wa_pesan_id="${pesan.id}"> 
                                <td>${pesan.nomor}</td> 
                                <td>${pesan.judul}</td> 
                                <td>${pesan.pesan}</td> 
                                <td>${pesan.user.name}</td> 
                                <td style="text-align: center;"><span class="badge rounded-pill text-bg-success">${pesan.jumlah_berhasil}</span></td> 
                                <td style="text-align: center;"><span class="badge rounded-pill text-bg-danger">${pesan.jumlah_gagal}</span></td> 
                                <td style="text-align: center;"><span class="badge rounded-pill text-bg-primary">${pesan.jumlah_null}</span></td> 
                                <td>${pesan.created_at_formatted}</td> 
                                <td>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-primary" onclick="redirectTo(${pesan.id})" id="proses">Proses</button>
                                        <button type="button" class="btn btn-danger" onclick="hapusData(${pesan.id})" id="hapus">Hapus</button>
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
            var goUrl = `{{ url('/kirim/${id}') }}`;
            window.open(goUrl, '_blank');        
        }

        function hapusData(id){
            if(confirm('apakah anda yakin?'))
                $.ajax({
                    url: '/api/pesan/' + id,
                    method: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        loadData();
                        // alert('data berhasil dihapus!');
                        toastr.success('operasi berhasil dilakukan!', 'berhasil');

                    },
                    error: function() {
                        alert('operasi gagal dilakukan!');
                    }
                });                
        }

        $('#refresh').on('click', function(e) {
            loadData();
        });

        $('.dropdown-item').on('click', function() {
            vPaging=$(this).data('nilai');
            loadData();
        })


        function resetForm(){
            $('#form judul').val('');
            $('#nama').focus();
        }

        $("#form").validate({
            submitHandler: function(form) {
                let vType=($('#id').val()==='')?'POST':'PUT';
                $.ajax({
                    url: '/api/pesan',
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

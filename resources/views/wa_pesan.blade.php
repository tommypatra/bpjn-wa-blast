@extends('template')

@section('head')
<title>Pesan Blast</title>
@endsection

@section('container')
    <div class="accordion" id="pesanAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="pesanHeader">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#bodyPesan" aria-expanded="false" aria-controls="judulPesan">
                    <h3>Pesan Baru</h3>
                </button>
            </h2>
            <div id="bodyPesan" class="accordion-collapse collapse" aria-labelledby="pesanHeader" data-bs-parent="#pesanAccordion">
                <div class="accordion-body">
                    <form id="pesanform">
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
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Judul</th>
                <th scope="col">Pesan</th>
                <th scope="col">User</th>
                <th scope="col">Waktu</th>
                <th scope="col">Aksi</th>
            </tr>
        </thead>
        <tbody id="data-list">
            <!-- Data pesan akan dimuat di sini -->
        </tbody>
    </table>

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
   function loadData(page = 1, search = '') {
            $.ajax({
                url: '/api/pesan?page=' + page + '&search=' + search,
                method: 'GET',
                success: function(response) {
                    var dataList = $('#data-list');
                    var pagination = $('#pagination');
                    dataList.empty();

                    $.each(response.data, function(index, pesan) {
                        dataList.append(`<tr> 
                                <td>${pesan.nomor}</td> 
                                <td>${pesan.judul}</td> 
                                <td>${pesan.pesan}</td> 
                                <td>${pesan.user.name}</td> 
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
                    },
                    error: function() {
                        alert('Gagal menghapus data.');
                    }
                });                
        }

        $("#pesanform").validate({
            submitHandler: function(form) {
                let vType=($('#id').val()==='')?'POST':'PUT';
                $.ajax({
                    url: '/api/pesan',
                    type: vType,
                    data: $(form).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        // alert('data berhasil dikirim!');
                        loadData(); // Reload pesan list after submission
                    },
                    error: function() {
                        alert('Gagal mengirim data.');
                    }
                });
            }
        });    
</script>
@endsection

<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials_head')
    <title>Login Form</title>
</head>
<body class="wrapper">

<div class="container mt-5">
    <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo" width="50" height="50">

                        <h3 class="card-title text-center">Login</h3>
                        <h4 class="card-title text-center mb-4">WA BLAST BPJN PROV. SULTRA</h4>
                        <form id="myform">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required >
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                </div>
            </div>
    </div>
</div>

@include('partials_footer')

<script>
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function(){

    $("#myform").validate({
        submitHandler: function(form) {
            $.ajax({
                url: '/api/login',
                type: 'post',
                data: $(form).serialize(),
                dataType: 'json',
                success: function(response) {
                    toastr.success('login berhasil, proses set session!', 'login berhasil', {timeOut: 1000});
                    localStorage.setItem('access_token', response.access_token);
                    setSession(form);
                },
                error: function() {
                    alert('login gagal, user atau password anda salah!');
                }
            });
        }
    });  
    
    function setSession(form){
        $.ajax({
            url: '/set-session',
            type: 'POST',
            data: $(form).serialize(),
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function (response) {
                toastr.success('set session berhasil, akan diarahkan ke halaman dashboard!', 'login berhasil', {timeOut: 1000});
                var goUrl = `{{ url('/dashboard') }}`;
                window.location.replace(goUrl);
            },
            error: function (error) {
                toastr.danger('set session gagal, hubungi admin!', 'login gagal');
                console.error(error);
            }
        });
    }    
});

</script>

</body>
</html>

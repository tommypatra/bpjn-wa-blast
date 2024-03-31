<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="website yang dikembangkan untuk mengirimkan multi pesan Whatsapp kepada seluruh pegawai BPJN prov. sultra yang terdaftar">
<meta name="author" content="WA PBJN">
<meta name="keywords" content="wa blast BPJN Prov. Sultra">
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="shortcut icon" href="{{ asset('images/logo.jpg') }}" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    html, body {
        height: 100%;
    }

    .wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }

    .content {
        flex: 1;
    }
</style>
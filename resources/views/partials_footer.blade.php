<footer class="footer mt-auto py-3 bg-light">
    <div class="container text-center">
        <span class="text-muted">wa-blast.bpjn-prov-sultra@2024</span>
    </div>
</footer>   

<div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img src="{{ asset('images/loading.gif') }}" height="150px" alt="Loading..." />
                <p>Sabar... lagi proses <span class='proses-berjalan'></span></p>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/loading.js') }}"></script>
<script src="{{ asset('plugins/toastr/build/toastr.min.js') }}"></script>
<script>
    toastr.options.closeButton = true;
</script>
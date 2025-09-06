<!DOCTYPE html>
<html>

<head>
    <title>Laravel Signature Pad</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.css">
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css"
        rel="stylesheet">
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="http://keith-wood.name/js/jquery.signature.js"></script>
    <link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">
    <style>
        .kbw-signature {
            width: 100%;
            height: 200px;
        }

        #sig canvas {
            width: 100% !important;
            height: auto;
        }

        /* --- Media Query for Mobile Devices --- */
        @media (max-width: 768px) {
            .kbw-signature {
                height: 350px; /* Taller height for smaller screens */
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3 mt-5">
                <div class="card">
                    <div class="card-header">
                        <h5>Signature Pad</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="form"
                            action="{{ route('signature.store', ['pesanan' => $pesanan->kd_pesanan]) }}">
                            @csrf
                            <div class="col-md-12">
                                <label class="" for="">Kode Pesanan:</label>
                                <input type="text" name="kd_pesanan" class="form-control"
                                    value="{{ $pesanan->kd_pesanan }}" readonly="readonly">
                            </div>
                            <div class="col-md-12">
                                <label class="" for="">Signature:</label>
                                <br />
                                <div id="sig"></div>
                                <br />
                                <button id="clear" class="btn btn-danger btn-sm">Hapus Signature</button>
                                <textarea id="signature64" name="signed" style="display: none" required></textarea>
                            </div>
                            <br />
                        </form>
                        <button form="form" type="submit" class="btn btn-success">Simpan</button>
                        <a class="btn btn-primary"
                            href="{{ route('pesanan.detail', ['pesanan' => $pesanan->kd_pesanan]) }}"><i></i>Batal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var sig = $('#sig').signature({ syncField: '#signature64', syncFormat: 'PNG' });

        @if(isset($signature) && $signature->signature)
            sig.signature('draw', '{{ $signature->signature }}');
        @endif

        $('#clear').click(function (e) {
            e.preventDefault();
            sig.signature('clear');
            $("#signature64").val('');
        });
    </script>
</body>

</html>
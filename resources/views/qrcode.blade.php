<!DOCTYPE html>
<html>
<head>
    <title>Código QR del usuario: {{ $user->nombre_usuario }}</title>
    <link href='https://fonts.googleapis.com/css?family=Prompt' rel='stylesheet'/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        * {
    margin: 0;
    padding: 0
}

body {
    background-color: #000
}

.card {
    width: 350px;
    background-color: #efefef;
    border: none;
    cursor: pointer;
    transition: all 0.5s;
}


.name {
    font-size: 22px;
    font-weight: bold
}

.idd {
    font-size: 14px;
    font-weight: 600
}

.idd1 {
    font-size: 12px
}

.number {
    font-size: 22px;
    font-weight: bold
}

.follow {
    font-size: 12px;
    font-weight: 500;
    color: #444444
}

.btn1 {
    height: 40px;
    width: 150px;
    border: none;
    background-color: #000;
    color: #aeaeae;
    font-size: 15px
}

.text span {
    font-size: 13px;
    color: #545454;
    font-weight: 500
}

.icons i {
    font-size: 19px
}

hr .new1 {
    border: 1px solid
}

.join {
    font-size: 14px;
    color: #a0a0a0;
    font-weight: bold
}

.date {
    background-color: #ccc
}

    </style>
</head>
<body>
    <div class="container mt-4 mb-4 p-3 d-flex justify-content-center">
   <div class="card p-4" id="qr">
      <div class=" image d-flex flex-column justify-content-center align-items-center">
         <button class="btn btn-secondary"> <img src="{{Vite::asset('resources/images/logo_uaqroo.png')}}" height="100" width="100" /></button> <span class="name mt-3">{{ $user->nombre_usuario }}</span> <span class="idd">{{ $user->email }}</span> 

         <div class=" d-flex mt-2">
            
        </div>

        {!! $qrCode !!}

      </div>

   </div>
</div>

<br>
      <div align="center">
<button id="boton-descarga" type="button" class="btn btn-primary">Descargar</button>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    document.getElementById('boton-descarga').addEventListener('click', function () {
        //  Seleccionar solo el qr
        const cardQr = document.getElementById('qr');
        html2canvas(cardQr, { scale: 2 }).then(canvas => {
            //  Descargar el canvas como imagen
            const img = canvas.toDataURL('image/png');
            // crear enlace de descarga
            const link = document.createElement('a');
            link.download = 'qr {{ $user->nombre_usuario }}.png';
            link.href = img;
            link.click();
        });
    });
    </script>
</body>
</html>
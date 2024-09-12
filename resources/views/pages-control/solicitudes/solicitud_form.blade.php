<x-base-layout :scrollspy="true">

    <x-slot:pageTitle>
        Nueva solicitud 
    </x-slot>

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <x-slot:headerFiles>
        <!--  BEGIN CUSTOM STYLE FILE  -->
        @vite(['resources/scss/light/assets/components/timeline.scss'])
                <!--  BEGIN CUSTOM STYLE FILE  -->
                <link rel="stylesheet" href="{{asset('plugins/apex/apexcharts.css')}}">

@vite(['resources/scss/light/assets/components/list-group.scss'])
@vite(['resources/scss/light/assets/widgets/modules-widgets.scss'])

@vite(['resources/scss/dark/assets/components/list-group.scss'])
@vite(['resources/scss/dark/assets/widgets/modules-widgets.scss'])
<!-- Estilos del sweet alert-->
@vite(['resources/css/sweet.css'])

<link rel="stylesheet" href="{{asset('plugins/table/datatable/datatables.css')}}">
@vite(['resources/scss/light/plugins/table/datatable/dt-global_style.scss'])
@vite(['resources/scss/dark/plugins/table/datatable/dt-global_style.scss'])

@vite(['resources/scss/light/assets/components/carousel.scss'])
@vite(['resources/scss/light/assets/components/modal.scss'])
@vite(['resources/scss/light/assets/components/tabs.scss'])
@vite(['resources/scss/dark/assets/components/carousel.scss'])
@vite(['resources/scss/dark/assets/components/modal.scss'])
@vite(['resources/scss/dark/assets/components/tabs.scss'])
    <!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Incluir Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Incluir Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Librería de Sweet Alert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!--  END CUSTOM STYLE FILE  -->
        
        <style>
            .toggle-code-snippet { margin-bottom: 0px; }
            body.dark .toggle-code-snippet { margin-bottom: 0px; }
        </style>
    </x-slot>
    <!-- END GLOBAL MANDATORY STYLES -->

    <x-slot:scrollspyConfig>
        data-bs-spy="scroll" data-bs-target="#navSection" data-bs-offset="100"
    </x-slot>
    
    <!-- BREADCRUMB -->
    <div class="page-meta">
        <nav class="breadcrumb-style-one" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"></li>
                <li class="breadcrumb-item active" aria-current="page"></li>
            </ol>
        </nav>
    </div>
    <!-- /BREADCRUMB -->


    <div class="card shadow-sm w-100 mx-auto">
  <div class="card-header">
    <h3 class="h5">Solicitud de acceso</h3>
    <p class="text-muted small">Llena todos los campos porfavor.</p>
  </div>
  <div class="card-body">
    <div class="row mb-3">
    </div>
    <form method="POST" action="{{ route('solicitud.store') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label" for="area">Area deseada</label>
      <select class="form-select" aria-label="Default select example" name="area">
            <option>Selecciona un área</option>
            @foreach ($areas as $area)
            <option value="{{ $area->area_id }}">{{ $area->nombre }}</option>
            @endforeach
</select>
    </div>
    <div class="mb-3">
      <label class="form-label" for="expiration">¿Hasta cúando?</label>
      <input type="datetime-local" class="form-control" id="expiration" name="expiracion">
    </div>
    <div class="mb-3">
      <label class="form-label" for="expiration">Usuarios para acceso</label>
      <select id="mi-select" name="usuarios[]" multiple="multiple" class="form-control" style="width: 100%;">  
                                    @foreach($users_i as $user)
                                    <option value="{{ $user->usuario_id }}">{{ $user->nombre .' '. $user->ape_materno . ' ' . $user->ape_paterno }}</option>
                                    @endforeach
                                </select>
                           </select>
    </div>
    <div class="mb-3">
      <label class="form-label" for="justification">Justificación</label>
      <textarea
        class="form-control"
        id="justification"
        name="mensaje"
        placeholder="Explica por que se necesita el acceso"
        rows="3"
      ></textarea>
    </div>
  </div>
  <div class="card-footer text-end">
    <button type="submit" class="btn btn-primary">Enviar</button>
  </div>
</form>
</div>

    </div>

 
    </div>
    
    <!--  BEGIN CUSTOM SCRIPTS FILE  -->
    <x-slot:footerFiles>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#mi-select').select2({
                placeholder: "Selecciona una o más opciones",
                allowClear: true // Asegura que el dropdown se renderice dentro del modal
            });
        });
    </script>

@if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                     // Opcional: añadir configuración de customClass para aislar estilos
                     customClass: {
                        container: 'swal-container',
                        popup: 'swal-popup',
                        header: 'swal-header',
                        title: 'swal-title',
                        text: 'swal-text',
                        closeButton: 'swal-close-button',
                        icon: 'swal-icon',
                        image: 'swal-image',
                        content: 'swal-content',
                        input: 'swal-input',
                        actions: 'swal-actions',
                        confirmButton: 'swal-confirm-button',
                        cancelButton: 'swal-cancel-button',
                        footer: 'swal-footer'
                    }
                });
            });
        </script>
        @endif


    </x-slot>
    <!--  END CUSTOM SCRIPTS FILE  -->
</x-base-layout>
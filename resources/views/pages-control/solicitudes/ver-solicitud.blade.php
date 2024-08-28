<x-base-layout :scrollspy="true">

    <x-slot:pageTitle>
        Solicitud 
    </x-slot>

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <x-slot:headerFiles>
        <!--  BEGIN CUSTOM STYLE FILE  -->
                      <!-- Incluir jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Incluir Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Incluir Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Librería de Sweet Alert 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
    </x-slot>
    <!-- END GLOBAL MANDATORY STYLES -->

    <x-slot:scrollspyConfig>
        data-bs-spy="scroll" data-bs-target="#navSection" data-bs-offset="100"
    </x-slot>
    
    <div class="page-meta">
        <nav class="breadcrumb-style-one" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page"></li>
            </ol>
        </nav>
    </div>
    <!-- BREADCRUMB -->
  <!-- Inicio de la card -->
  <div class="card shadow-sm w-100 max-w-4xl">
  <div class="card-body p-4">
    <h3 class="card-title text-2xl font-semibold">Solicitud con ID {{ $solicitud->solicitud_id }}</h3>
    <p class="card-text text-muted">Detalles de la solicitud</p>
  </div>
  <div class="card-body p-4">
    <div class="row g-4">
      <div class="col-6">
        <div>
          <label class="form-label text-sm fw-medium">Estatus</label>
          @if ($solicitud->estado === 'PENDIENTE')
            <span class="badge bg-warning mb-2 me-4">PENDIENTE</span>
          @elseif ($solicitud->estado === 'DENEGADA')
            <span class="badge bg-danger mb-2 me-4">DENEGADA</span>
          @elseif ($solicitud->estado === 'ACEPTADA')
            <span class="badge bg-success mb-2 me-4">ACEPTADA</span>
          @endif
        </div>
      </div>
      <div class="col-6">
        <div>
          <label class="form-label text-sm fw-medium">Creado</label>
          <div>{{ $fecha_creacion }}</div>
        </div>
      </div>
      <div class="col-6">
        <div>
          <label class="form-label text-sm fw-medium">Fecha deseada</label>
          <div>{{ $fecha_deseada }}</div>
        </div>
      </div>
      <div class="col-6">
        <div>
          <label class="form-label text-sm fw-medium">Usuario quien solicita</label>
          <div>{{ $solicitud->nombre . ' ' . $solicitud->ape_materno . ' ' . $solicitud->ape_paterno }}</div>
        </div>
      </div>
    </div>
    <div class="mt-4">
      <label class="form-label text-sm fw-medium">Mensaje</label>
      <div class="text-muted">{{ $solicitud->mensaje }}</div>
    </div>
    <hr class="my-4">
    <div class="mb-4">
      <!-- Inicio de la tabla -->
      <div class="table-responsive">
        <form method="POST" action="{{ route('solicitud.acceso') }}">
          @csrf
          <label class="form-label text-sm fw-medium">Usuarios que requieren acceso</label>
          @if(Auth::user()->rol_id == 2)
          <div class="text-muted mb-2">Elija todos o algunos usuarios para que se les conceda el acceso.</div>
          @endif
          <!-- Inicio del thead de la tabla -->
          <table class="table table-hover table-striped table-bordered">
            <thead>
              <tr>
                <th class="checkbox-area" scope="col">
                  <div class="form-check">
                    <input class="form-check-input" id="select-all" type="checkbox">
                  </div>
                </th>
                <th scope="col">Nombre</th>
                <th scope="col">Rol</th>
              </tr>
            </thead>
            <!-- Fin del thead de la tabla -->

            <!-- Inicio del tbody de la tabla -->
             <input type="hidden" name="area" value="{{ $solicitud->area_id }}">
             <input type="hidden" name="fecha" value="{{ $solicitud->fecha_deseada }}">
             <input type="hidden" name="solicitud" value="{{ $solicitud->solicitud_id }}">

            <tbody>
              @foreach ($usuarios as $u)
                <tr>
                  <td>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="{{ $u->usuario_id }}" name="selected_items[]">
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="flex-shrink-0 me-2">
                        <!-- Imagen o iniciales del usuario pueden ir aquí -->
                      </div>
                      <div>
                        <h6 class="mb-0">{{ $u->full_name }}</h6>
                      </div>
                    </div>
                  </td>
                  <td>
                    <p class="mb-0">{{ $u->rol }}</p>
                  </td>
                </tr>
              @endforeach
            </tbody>
            <!-- Fin del tbody de la tabla -->
          </table>
          <!-- Fin de la tabla -->
          @if(Auth::user()->rol_id == 2)
          <button type="submit" class="btn btn-success">Conceder permiso a todos los usuarios seleccionados</button>
          @endif
        </form>
      </div>
    </div>
  </div>
  <div class="card-footer d-flex justify-content-end gap-2 p-4">
    <!-- Botón de Denegar fuera del formulario -->
    @if(Auth::user()->rol_id == 2)
    <button class="btn btn-danger">Denegar</button>
    @endif
  </div>
</div>

<!--  BEGIN CUSTOM SCRIPTS FILE  -->
<x-slot:footerFiles>
<script>
  document.getElementById('select-all').addEventListener('change', function() {
    const isChecked = this.checked;
    const checkboxes = document.querySelectorAll('input[name="selected_items[]"]');
    checkboxes.forEach(checkbox => {
      checkbox.checked = isChecked;
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


        @vite(['resources/assets/js/widgets/_wTwo.js'])
        @vite(['resources/assets/js/widgets/_wOne.js'])
        @vite(['resources/assets/js/widgets/_wChartOne.js'])
        @vite(['resources/assets/js/widgets/_wChartTwo.js'])
        @vite(['resources/assets/js/widgets/_wActivityFour.js'])
        @include('sweetalert::alert', ['cdn' => "https://cdn.jsdelivr.net/npm/sweetalert2@9"])



        <script src="{{asset('plugins/editors/quill/quill.js')}}"></script>
        <script src="{{asset('plugins/filepond/filepond.min.js')}}"></script>
        <script src="{{asset('plugins/filepond/FilePondPluginFileValidateType.min.js')}}"></script>
        <script src="{{asset('plugins/filepond/FilePondPluginImageExifOrientation.min.js')}}"></script>
        <script src="{{asset('plugins/filepond/FilePondPluginImagePreview.min.js')}}"></script>
        <script src="{{asset('plugins/filepond/FilePondPluginImageCrop.min.js')}}"></script>
        <script src="{{asset('plugins/filepond/FilePondPluginImageResize.min.js')}}"></script>
        <script src="{{asset('plugins/filepond/FilePondPluginImageTransform.min.js')}}"></script>
        <script src="{{asset('plugins/filepond/filepondPluginFileValidateSize.min.js')}}"></script>

</x-slot:footerFiles>

    <!--  END CUSTOM SCRIPTS FILE  -->
</x-base-layout>
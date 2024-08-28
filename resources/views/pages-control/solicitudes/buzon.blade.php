<x-base-layout :scrollspy="true">

    <x-slot:pageTitle>
        Buzón 
    </x-slot>

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <x-slot:headerFiles>
        <!--  BEGIN CUSTOM STYLE FILE  -->
   
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
<div class="card">
  
  <!-- Inicio del header de la card -->
  <div class="card-header">
    <h3 class="card-title">Buzón de solicitudes</h3>
    <p class="card-text text-muted">Puedes mirar y responder solicitudes</p>
    <a type="button" class="btn btn-primary mb-2 mr-2" href="/solicitud-crear">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-plus">
               <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
               <polyline points="14 2 14 8 20 8"></polyline>
               <line x1="12" y1="18" x2="12" y2="12"></line>
               <line x1="9" y1="15" x2="15" y2="15"></line>
            </svg>
            Nueva solicitud
</a>
  </div>
  <!-- Fin del header de la card -->
   @php
      $perPage = 10;
      $page = request()->get('page', 1);
      $offset = ($page - 1) * $perPage;

      $totalSolicitudes = count($solicitudes_array);
      $solicitudes = array_slice($solicitudes_array, $offset, $perPage);
      $totalPages = ceil($totalSolicitudes / $perPage);
   @endphp
  
  <!-- Inicio del body de la card -->
  <div class="card-body" style="color: currentColor;">
    <div class="list-group">
    @foreach($solicitudes_array as $s)

      <!-- Inicio del primer list-group-item -->
      <div class="list-group-item d-flex justify-content-between align-items-center">
        <!-- Inicio de la información de la solicitud -->
        <div>
          <p class="mb-1 fw-bold" style="color: black;">De: {{ $s['nombre'] }}</p>
          @if ($s['estado'] === 'PENDIENTE')
            <span class="badge bg-warning mb-2 me-4">PENDIENTE</span><br>
          @elseif ($s['estado'] === 'DENEGADA')
            <span class="badge bg-danger mb-2 me-4">DENEGADA</span><br>
          @elseif ($s['estado'] === 'ACEPTADA')
            <span class="badge bg-success mb-2 me-4">ACEPTADA</span><br>
          @endif  
          <small class="text-muted">{{ $s['fecha'] }}</small><br>     
         </div>
        <!-- Fin de la información de la solicitud -->
        
        <!-- Inicio del grupo de botones -->
        <div class="btn-group">
          <a class="btn btn-primary" href="/ver-solicitud/{{ $s['id'] }}" type="button">
            Ver solicitud
          </a>
        </div>
        <!-- Fin del grupo de botones -->
               <!-- Inicio del contenido colapsable -->
      <div class="collapse" id="collapseExample">
        
        <!-- Inicio de la card dentro del colapsable -->
        <div class="card card-body" style="border-top-left-radius: 0; border-top-right-radius: 0;">
          Some placeholder content for the collapse component. This panel is hidden by default but revealed when the user activates the relevant trigger.


        </div>
        <!-- Fin de la card dentro del colapsable -->
      
      </div>
      <!-- Fin del contenido colapsable -->
      
      </div>
      <!-- Fin del primer list-group-item -->
      @endforeach
    
    </div>
  </div>
  <!-- Fin del body de la card -->

  <!-- Paginación -->
   <div class="d-flex justify-content-center mt-3">
    @if ($totalPages > 1)
      <nav>
        <ul class="pagination">
          @for ($i = 1; $i <= $totalPages; $i++)
            <li class="page-item {{ $page == $i ? 'active' : '' }}">
              <a class="page-link" href="?page={{ $i }}">{{ $i }} </a>
            </li>
          @endfor
        </ul>
      </nav>
      @endif
   </div>
</div>
<!-- Fin de la card -->

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

    </x-slot>
    <!--  END CUSTOM SCRIPTS FILE  -->
</x-base-layout>
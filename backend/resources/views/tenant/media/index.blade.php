@extends('tenant.layouts.app')

@section('title', 'Galería de Medios')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Galería de Medios</h1>
        <button onclick="document.getElementById('upload-modal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            Subir Archivos
        </button>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar archivos..." class="w-full rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
            </div>
            <select name="type" onchange="this.form.submit()" class="rounded-lg border border-slate-300 px-4 py-2">
                <option value="">Todos los tipos</option>
                <option value="image" @selected(request('type') === 'image')>Imágenes</option>
                <option value="document" @selected(request('type') === 'document')>Documentos</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">Filtrar</button>
        </form>
    </div>

    @if($media->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-slate-900">No hay archivos</h3>
            <p class="mt-2 text-slate-500">Sube archivos para empezar a usarlos en tu tienda.</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($media as $item)
                <div class="group relative bg-white rounded-xl border border-slate-200 overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="aspect-square bg-slate-100 flex items-center justify-center">
                        @if($item->type === 'image')
                            <img src="{{ $item->url }}" alt="{{ $item->filename }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        @endif
                    </div>
                    
                    {{-- Overlay con acciones --}}
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        <button onclick="copyUrl('{{ $item->url }}')" class="p-2 bg-white rounded-lg text-slate-700 hover:bg-slate-100" title="Copiar URL">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                        </button>
                        <a href="{{ $item->url }}" target="_blank" class="p-2 bg-white rounded-lg text-slate-700 hover:bg-slate-100" title="Ver">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('tenant.media.destroy', [$tenant, $item]) }}" onsubmit="return confirm('¿Eliminar este archivo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-red-500 rounded-lg text-white hover:bg-red-600" title="Eliminar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                    
                    <div class="p-2">
                        <p class="text-xs text-slate-600 truncate" title="{{ $item->filename }}">{{ $item->filename }}</p>
                        <p class="text-xs text-slate-400">{{ number_format($item->size / 1024, 1) }} KB</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $media->withQueryString()->links() }}
        </div>
    @endif

    {{-- Modal de subida --}}
    <div id="upload-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4">
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Subir Archivos</h3>
                    <button onclick="document.getElementById('upload-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <form method="POST" action="{{ route('tenant.media.store', $tenant) }}" enctype="multipart/form-data" class="p-6">
                @csrf
                <div id="drop-zone" class="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center hover:border-indigo-500 transition-colors">
                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <p class="mt-4 text-slate-600">Arrastra archivos aquí o</p>
                    <label class="mt-2 inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg cursor-pointer hover:bg-indigo-700">
                        Seleccionar archivos
                        <input type="file" name="files[]" multiple accept="image/*,.pdf" class="hidden" id="file-input">
                    </label>
                    <p class="mt-2 text-xs text-slate-400">JPG, PNG, GIF, WebP, SVG, PDF. Máximo 5MB cada uno.</p>
                </div>
                <div id="file-list" class="mt-4 space-y-2 hidden"></div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('upload-modal').classList.add('hidden')" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="submit" id="upload-btn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50" disabled>
                        Subir
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function copyUrl(url) {
        navigator.clipboard.writeText(url).then(() => {
            alert('URL copiada al portapapeles');
        });
    }

    // Drag & Drop y preview de archivos
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const fileList = document.getElementById('file-list');
    const uploadBtn = document.getElementById('upload-btn');

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.add('border-indigo-500', 'bg-indigo-50');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.remove('border-indigo-500', 'bg-indigo-50');
        });
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        updateFileList(files);
    });

    fileInput.addEventListener('change', (e) => {
        updateFileList(e.target.files);
    });

    function updateFileList(files) {
        if (files.length > 0) {
            fileList.classList.remove('hidden');
            fileList.innerHTML = '';
            
            Array.from(files).forEach(file => {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-3 p-2 bg-slate-50 rounded-lg';
                div.innerHTML = `
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="flex-1 text-sm text-slate-600 truncate">${file.name}</span>
                    <span class="text-xs text-slate-400">${(file.size / 1024).toFixed(1)} KB</span>
                `;
                fileList.appendChild(div);
            });

            uploadBtn.disabled = false;
        } else {
            fileList.classList.add('hidden');
            uploadBtn.disabled = true;
        }
    }
</script>
@endpush

{{-- Modal de selector de medios para usar en otros formularios --}}
<div class="p-4">
    <div class="flex items-center gap-4 mb-4">
        <input type="text" name="search" placeholder="Buscar..." class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
        <button type="button" onclick="loadMedia()" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200 text-sm">Buscar</button>
    </div>

    @if($media->isEmpty())
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="mt-2 text-slate-500 text-sm">No hay imágenes disponibles</p>
        </div>
    @else
        <div class="grid grid-cols-4 gap-3 max-h-96 overflow-y-auto">
            @foreach($media as $item)
                <button type="button" onclick="selectMedia({{ json_encode($item) }})" class="aspect-square bg-slate-100 rounded-lg overflow-hidden hover:ring-2 hover:ring-indigo-500 focus:ring-2 focus:ring-indigo-500 transition-all">
                    <img src="{{ $item->url }}" alt="{{ $item->filename }}" class="w-full h-full object-cover">
                </button>
            @endforeach
        </div>

        @if($media->hasPages())
            <div class="mt-4 flex justify-center">
                {{ $media->links() }}
            </div>
        @endif
    @endif
</div>

<!-- ================= RESTORANIM.NET 3-COLUMN HERO PHOTO GRID ================= -->
<div class="h-[280px] sm:h-[340px] md:h-[360px] w-full rounded-2xl overflow-hidden mt-1">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-2 sm:gap-2.5 h-full w-full">

        <!-- Column 1: Left Big Photo (md:col-span-5) -->
        <div @click="openGallery(0)"
             class="md:col-span-5 h-full rounded-xl overflow-hidden bg-stone-200 group cursor-pointer relative">
            <img src="{{ $allPhotos[0] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
        </div>

        <!-- Column 2: Middle 2 Stacked Photos (md:col-span-3) -->
        <div class="hidden md:grid md:col-span-3 grid-rows-2 gap-2 sm:gap-2.5 h-full min-h-0">
            <div @click="openGallery(1)" class="rounded-xl overflow-hidden bg-stone-200 group cursor-pointer relative h-full min-h-0">
                <img src="{{ $allPhotos[1] ?? $allPhotos[0] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
            </div>
            <div @click="openGallery(2)" class="rounded-xl overflow-hidden bg-stone-200 group cursor-pointer relative h-full min-h-0">
                <img src="{{ $allPhotos[2] ?? $allPhotos[0] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
            </div>
        </div>

        <!-- Column 3: Right 2 Stacked Photos with +X Overlay (md:col-span-4) -->
        <div class="hidden md:grid md:col-span-4 grid-rows-2 gap-2 sm:gap-2.5 h-full min-h-0">
            <div @click="openGallery(3)" class="rounded-xl overflow-hidden bg-stone-200 group cursor-pointer relative h-full min-h-0">
                <img src="{{ $allPhotos[3] ?? $allPhotos[0] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
            </div>
            <div @click="openGallery(4)" class="rounded-xl overflow-hidden bg-stone-200 group cursor-pointer relative h-full min-h-0">
                <img src="{{ $allPhotos[4] ?? $allPhotos[0] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">

                @if($totalPhotos > 4)
                    <div class="absolute inset-0 bg-ink/65 group-hover:bg-ink/75 transition-colors flex flex-col items-center justify-center text-white text-center p-2">
                        <x-ico name="camera" class="w-5 h-5 mb-1 text-white" />
                        <span class="text-xs sm:text-sm font-bold">+{{ $totalPhotos - 4 }}</span>
                        <span class="text-[10px] text-stone-200 font-medium">fotoğraf daha</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

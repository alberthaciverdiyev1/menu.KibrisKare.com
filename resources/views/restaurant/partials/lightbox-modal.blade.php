<!-- ================= FULLSCREEN LIGHTBOX MODAL ================= -->
<template x-teleport="body">
    <div x-show="galleryOpen"
         x-cloak
         @keydown.escape.window="closeGallery()"
         @keydown.arrow-right.window="nextPhoto()"
         @keydown.arrow-left.window="prevPhoto()"
         class="fixed inset-0 z-[9999] bg-stone-950/95 backdrop-blur-md flex flex-col justify-between p-4 select-none overflow-hidden h-screen w-screen">

        <!-- Top Bar -->
        <div class="flex items-center justify-between text-white pb-3 border-b border-white/10 w-full shrink-0">
            <div class="flex items-center gap-3">
                <span class="font-bold text-sm text-white">{{ $restaurant->name }}</span>
                <span class="text-xs text-stone-400 bg-white/10 px-2.5 py-0.5 rounded-full font-mono" x-text="(galleryIndex + 1) + ' / ' + photos.length"></span>
            </div>
            <button type="button" @click="closeGallery()" class="text-white hover:text-stone-300 font-bold text-xs flex items-center gap-1 bg-white/10 hover:bg-white/20 px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">
                <x-ico name="close" class="w-4 h-4" />
                <span>Kapat</span>
            </button>
        </div>

        <!-- Image Stage -->
        <div class="relative flex-1 w-full flex items-center justify-center min-h-0 py-2" @click.self="closeGallery()">
            <button type="button" x-show="photos.length > 1" @click.stop="prevPhoto()" aria-label="Önceki"
                    class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 z-20 p-3 rounded-full bg-stone-900/80 hover:bg-stone-900 text-white border border-white/20 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </button>

            <div class="h-full w-full flex items-center justify-center p-2">
                <img :src="photos[galleryIndex]" alt="{{ $restaurant->name }}" class="max-h-[72vh] max-w-[90vw] w-auto h-auto rounded-xl object-contain shadow-2xl">
            </div>

            <button type="button" x-show="photos.length > 1" @click.stop="nextPhoto()" aria-label="Sonraki"
                    class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 z-20 p-3 rounded-full bg-stone-900/80 hover:bg-stone-900 text-white border border-white/20 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>

        <!-- Bottom Thumbnails -->
        <div x-show="photos.length > 1" class="w-full shrink-0 flex items-center justify-center gap-2 overflow-x-auto py-2 hide-scrollbar">
            <template x-for="(p, i) in photos" :key="i">
                <button type="button" @click.stop="galleryIndex = i"
                        :class="galleryIndex === i ? 'ring-2 ring-terracotta scale-105 opacity-100' : 'opacity-40 hover:opacity-80'"
                        class="h-12 aspect-square rounded-lg overflow-hidden shrink-0 transition-all focus:outline-none cursor-pointer bg-stone-800">
                    <img :src="p" class="w-full h-full object-cover">
                </button>
            </template>
        </div>
    </div>
</template>

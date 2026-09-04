@props(['category'])

<a href="{{ route('restaurants.index', ['category' => $category->slug]) }}" 
   class="group bg-surface rounded-2xl p-5 border border-warm hover:border-terracotta shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between">
    <span class="text-xs font-bold text-muted group-hover:text-terracotta transition-colors">
        {{ $category->restaurants_count }} Mekan
    </span>
    <h3 class="font-extrabold text-base text-ink group-hover:text-terracotta transition-colors mt-4">
        {{ $category->name }}
    </h3>
</a>

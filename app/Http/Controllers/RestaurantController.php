<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    /**
     * Ana Sayfa (Home) - ASCII Wireframe'e tam uyumlu
     */
    public function index(Request $request)
    {
        $selectedCitySlug = $request->query('city', 'girne');
        $selectedCategorySlug = $request->query('category');
        $searchQuery = $request->query('q');

        $cities = City::orderBy('name')->get();
        $categories = Category::orderBy('order')->get();

        $selectedCity = City::where('slug', $selectedCitySlug)->first() ?? $cities->first();

        $restaurantsQuery = Restaurant::with(['city', 'categories', 'menuCategories.items']);

        if ($selectedCity) {
            $restaurantsQuery->where('city_id', $selectedCity->id);
        }

        if ($selectedCategorySlug) {
            $restaurantsQuery->whereHas('categories', function ($q) use ($selectedCategorySlug) {
                $q->where('slug', $selectedCategorySlug);
            });
        }

        if ($searchQuery) {
            $restaurantsQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'ilike', "%{$searchQuery}%")
                  ->orWhere('cuisine', 'ilike', "%{$searchQuery}%")
                  ->orWhere('description', 'ilike', "%{$searchQuery}%")
                  ->orWhereHas('menuItems', function ($itemQ) use ($searchQuery) {
                      $itemQ->where('name', 'ilike', "%{$searchQuery}%")
                            ->orWhere('description', 'ilike', "%{$searchQuery}%");
                  });
            });
        }

        $allRestaurants = $restaurantsQuery->get();

        // Sections for the home page
        $nearbyRestaurants = $allRestaurants->take(6);
        $popularRestaurants = Restaurant::with(['city', 'categories', 'menuCategories.items'])
            ->where('is_popular', true)
            ->when($selectedCity, fn($q) => $q->where('city_id', $selectedCity->id))
            ->get();

        if ($popularRestaurants->isEmpty()) {
            $popularRestaurants = Restaurant::with(['city', 'categories', 'menuCategories.items'])
                ->where('is_popular', true)
                ->take(6)
                ->get();
        }

        $newRestaurants = Restaurant::with(['city', 'categories', 'menuCategories.items'])
            ->where('is_new', true)
            ->get();

        if ($newRestaurants->isEmpty()) {
            $newRestaurants = $allRestaurants->take(4);
        }

        // Map pins
        $mapData = $this->formatMapData(Restaurant::with(['city', 'categories'])->get());

        return view('home', compact(
            'cities',
            'categories',
            'selectedCity',
            'selectedCategorySlug',
            'searchQuery',
            'allRestaurants',
            'nearbyRestaurants',
            'popularRestaurants',
            'newRestaurants',
            'mapData'
        ));
    }

    /**
     * Tüm Restoranlar Listesi Sayfası (/restaurants)
     */
    public function list(Request $request)
    {
        $citySlug = $request->query('city');
        $categorySlug = $request->query('category');
        $search = $request->query('q');
        $sort = $request->query('sort', 'rating_desc');

        $cities = City::withCount('restaurants')->orderBy('name')->get();
        $categories = Category::withCount('restaurants')->orderBy('order')->get();

        $query = Restaurant::with(['city', 'categories', 'menuCategories.items']);

        if ($citySlug && $citySlug !== 'all') {
            $query->whereHas('city', fn($q) => $q->where('slug', $citySlug));
        }

        if ($categorySlug) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $categorySlug));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('cuisine', 'ilike', "%{$search}%")
                  ->orWhere('address', 'ilike', "%{$search}%");
            });
        }

        // Sıralama
        switch ($sort) {
            case 'rating_desc':
                $query->orderBy('rating', 'desc');
                break;
            case 'reviews_desc':
                $query->orderBy('reviews_count', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('rating', 'desc');
        }

        $restaurants = $query->paginate(12)->withQueryString();
        $mapData = $this->formatMapData($query->get());

        $currentCity = $citySlug ? City::where('slug', $citySlug)->first() : null;
        $currentCategory = $categorySlug ? Category::where('slug', $categorySlug)->first() : null;

        return view('restaurant.index', compact(
            'restaurants',
            'cities',
            'categories',
            'citySlug',
            'categorySlug',
            'search',
            'sort',
            'currentCity',
            'currentCategory',
            'mapData'
        ));
    }

    /**
     * Restoran Detay Sayfası (/restaurant/{slug})
     */
    public function show(Restaurant $restaurant)
    {
        $restaurant->load(['city', 'categories', 'menuCategories.items', 'branches.city']);

        $featuredItems = $restaurant->menuItems()
            ->where(function ($q) {
                $q->where('is_popular', true)->orWhere('is_chef_special', true);
            })
            ->take(4)
            ->get();

        if ($featuredItems->isEmpty()) {
            $featuredItems = $restaurant->menuItems()->take(4)->get();
        }

        $relatedRestaurants = Restaurant::where('city_id', $restaurant->city_id)
            ->where('id', '!=', $restaurant->id)
            ->take(3)
            ->get();

        return view('restaurant.show', compact('restaurant', 'featuredItems', 'relatedRestaurants'));
    }

    /**
     * Ayrı Restoran Menü Sayfası (/restaurant/{slug}/menu)
     */
    public function menu(Restaurant $restaurant)
    {
        $restaurant->load(['city', 'categories', 'menuCategories.items']);

        return view('restaurant.menu', compact('restaurant'));
    }

    /**
     * Harita Sayfası (/harita)
     */
    public function mapView(Request $request)
    {
        $citySlug = $request->query('city');
        $cities = City::orderBy('name')->get();
        $categories = Category::orderBy('order')->get();

        $query = Restaurant::with(['city', 'categories']);
        if ($citySlug) {
            $query->whereHas('city', fn($q) => $q->where('slug', $citySlug));
        }

        $restaurants = $query->get();
        $mapData = $this->formatMapData($restaurants);
        $selectedCity = $citySlug ? City::where('slug', $citySlug)->first() : null;

        return view('map', compact('restaurants', 'mapData', 'cities', 'categories', 'selectedCity', 'citySlug'));
    }

    /**
     * Kategoriler Sayfası (/kategoriler)
     */
    public function categoriesView()
    {
        $categories = Category::withCount('restaurants')
            ->with(['restaurants' => fn($q) => $q->with('city')->take(3)])
            ->orderBy('order')
            ->get();

        $cities = City::orderBy('name')->get();

        return view('categories', compact('categories', 'cities'));
    }

    /**
     * Leaflet için JSON veri formatlayıcı
     */
    private function formatMapData($restaurants)
    {
        return $restaurants->map(function ($r) {
            return [
                'id' => $r->id,
                'name' => $r->name,
                'slug' => $r->slug,
                'cuisine' => $r->cuisine,
                'city' => $r->city->name ?? '',
                'city_slug' => $r->city->slug ?? '',
                'rating' => (float)$r->rating,
                'reviews_count' => $r->reviews_count,
                'price_range' => $r->price_range,
                'distance' => $r->distance,
                'address' => $r->address,
                'phone' => $r->phone,
                'image' => $r->image,
                'lat' => (float)$r->latitude,
                'lng' => (float)$r->longitude,
                'is_open' => $r->is_open,
                'opening_hours' => $r->opening_hours,
                'detail_url' => route('restaurant.show', $r->slug),
                'menu_url' => route('restaurant.menu', $r->slug),
            ];
        });
    }
}

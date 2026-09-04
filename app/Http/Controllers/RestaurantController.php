<?php

namespace App\Http\Controllers;

use App\Models\Branch;
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
        $restaurant->load(['city', 'categories', 'menuCategories.items', 'branches.city', 'branches.reviews']);

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
    public function menu(Request $request, Restaurant $restaurant)
    {
        $branchId = $request->query('branch');
        $currentBranch = null;

        if ($branchId) {
            $currentBranch = $restaurant->branches()->where('id', $branchId)->first();
        }

        $restaurant->load([
            'city',
            'categories',
            'branches',
            'menuCategories.items' => function ($query) use ($currentBranch) {
                if ($currentBranch) {
                    $query->where(function ($q) use ($currentBranch) {
                        $q->whereDoesntHave('branches')
                          ->orWhereHas('branches', fn($b) => $b->where('branches.id', $currentBranch->id));
                    });
                }
            }
        ]);

        return view('restaurant.menu', compact('restaurant', 'currentBranch'));
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
                'city' => $r->display_city->name ?? $r->city->name ?? '',
                'city_slug' => $r->display_city->slug ?? $r->city->slug ?? '',
                'rating' => (float)$r->rating,
                'reviews_count' => $r->reviews_count,
                'price_range' => '',
                'distance' => $r->distance,
                'address' => $r->display_address,
                'phone' => $r->phone,
                'image' => $r->image,
                'lat' => (float)$r->display_latitude,
                'lng' => (float)$r->display_longitude,
                'is_open' => $r->isOpenNow(),
                'opening_hours' => $r->getTodayHours(),
                'detail_url' => route('restaurant.show', $r->slug),
                'menu_url' => route('restaurant.menu', $r->slug),
            ];
        });
    }

    /**
     * Şube için anonim puan ve yorum kaydetme
     */
    public function storeBranchReview(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'author_name' => 'nullable|string|max:100',
        ]);

        $authorName = !empty(trim($validated['author_name'] ?? '')) 
            ? trim($validated['author_name']) 
            : 'Anonim Misafir';

        $branch->reviews()->create([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'author_name' => $authorName,
            'ip_address' => $request->ip(),
            'is_approved' => true,
        ]);

        // Restoran genel puan ve yorum sayısını güncelle
        $restaurant = $branch->restaurant;
        if ($restaurant) {
            $allBranchIds = $restaurant->branches()->pluck('id');
            $allReviews = \App\Models\BranchReview::whereIn('branch_id', $allBranchIds)->where('is_approved', true);
            $avgRating = $allReviews->avg('rating');
            $reviewsCount = $allReviews->count();

            if ($avgRating) {
                $restaurant->update([
                    'rating' => round($avgRating, 1),
                    'reviews_count' => $reviewsCount,
                ]);
            }
        }

        return back()->with('success', 'Değerlendirmeniz ve yorumunuz başarıyla kaydedildi! Teşekkür ederiz.');
    }
}

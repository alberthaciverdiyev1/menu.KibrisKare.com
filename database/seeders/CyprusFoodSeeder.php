<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CyprusFoodSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user for Filament
        User::firstOrCreate(
            ['email' => 'admin@menu.cy'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        // Cities
        $citiesData = [
            ['name' => 'Girne', 'slug' => 'girne', 'latitude' => 35.3403, 'longitude' => 33.3190],
            ['name' => 'Lefkoşa', 'slug' => 'lefkosa', 'latitude' => 35.1856, 'longitude' => 33.3823],
            ['name' => 'Gazimağusa', 'slug' => 'gazimagusa', 'latitude' => 35.1250, 'longitude' => 33.9400],
            ['name' => 'İskele', 'slug' => 'iskele', 'latitude' => 35.2892, 'longitude' => 33.8897],
            ['name' => 'Güzelyurt', 'slug' => 'guzelyurt', 'latitude' => 35.1989, 'longitude' => 32.9928],
            ['name' => 'Lefke', 'slug' => 'lefke', 'latitude' => 35.1111, 'longitude' => 32.8489],
        ];

        $cities = [];
        foreach ($citiesData as $data) {
            $cities[$data['slug']] = City::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // Categories (from ASCII wireframe: 🍕 Pizza 🍔 Burger ☕ Cafe 🍣 Sushi 🥩 Steak + extras)
        $categoriesData = [
            ['name' => 'Pizza', 'slug' => 'pizza', 'icon' => '🍕', 'order' => 1],
            ['name' => 'Burger', 'slug' => 'burger', 'icon' => '🍔', 'order' => 2],
            ['name' => 'Cafe', 'slug' => 'cafe', 'icon' => '☕', 'order' => 3],
            ['name' => 'Sushi', 'slug' => 'sushi', 'icon' => '🍣', 'order' => 4],
            ['name' => 'Steak', 'slug' => 'steak', 'icon' => '🥩', 'order' => 5],
            ['name' => 'Kebap & Izgara', 'slug' => 'kebap', 'icon' => '🥙', 'order' => 6],
            ['name' => 'Deniz Ürünleri', 'slug' => 'deniz-urunleri', 'icon' => '🐟', 'order' => 7],
            ['name' => 'Tatlı & Pastane', 'slug' => 'tatli', 'icon' => '🍰', 'order' => 8],
            ['name' => 'Bar & Lounge', 'slug' => 'bar', 'icon' => '🍹', 'order' => 9],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // Restaurants
        $restaurants = [
            [
                'city' => 'girne',
                'name' => "Niazi's Restaurant Girne",
                'slug' => 'niazis-restaurant-girne',
                'cuisine' => 'Kıbrıs Mutfağı, Kebap & Steak',
                'description' => "1949'dan beri Kıbrıs'ın efsanevi 'Full Kebab' lezzeti, kömür ateşinde pişen taze etler ve zengin geleneksel mezeler.",
                'address' => 'Kordonboyu Caddesi No:12, Girne Liman',
                'latitude' => 35.3415,
                'longitude' => 33.3210,
                'phone' => '+90 392 815 21 63',
                'rating' => 4.9,
                'reviews_count' => 342,
                'price_range' => '₺₺₺',
                'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=800&q=80',
                'distance' => '0.8 km',
                'opening_hours' => '11:30 - 23:30',
                'is_popular' => true,
                'is_new' => false,
                'is_open' => true,
                'categories' => ['steak', 'kebap'],
                'menu' => [
                    'Özel Başlangıçlar & Mezeler' => [
                        ['name' => 'Kıbrıs Şeftali Kebabı Mezesi', 'desc' => 'Kuzu gömleğine sarılı geleneksel baharatlı köfte, sumaklı soğan ile', 'price' => 280, 'popular' => true, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Hellim Tava & Bal Sosu', 'desc' => 'Kızartılmış taze Kıbrıs hellimi, susam ve yabani kekik balı ile', 'price' => 220, 'popular' => true, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Geleneksel Humus & Pastırma', 'desc' => 'Sıcak tereyağlı pastırma dilimleri ile taş fırın pidesi eşliğinde', 'price' => 240, 'popular' => false, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1577906096429-f73c2c312435?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'Kömürde Ana Yemekler' => [
                        ['name' => "Niazi's Efsane Full Kebab", 'desc' => 'Karışık kuzu pirzola, şiş kebap, şeftali kebabı, fırın patates ve 12 çeşit meze ile', 'price' => 750, 'popular' => true, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Kuzu Külbastı', 'desc' => 'Marine edilmiş yumuşacık kuzu külbastı, köz biber ve domates ile', 'price' => 620, 'popular' => false, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1600891964599-f61ba0e24092?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Antrikot Izgara (300g)', 'desc' => '28 gün dinlendirilmiş yerli dana antrikot, mantar sos ve tereyağlı patates püresi', 'price' => 690, 'popular' => true, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1558030006-450675393462?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'Tatlı & İçecekler' => [
                        ['name' => 'Ceviz Macunu & Çifte Kavrulmuş Kahve', 'desc' => 'Geleneksel Kıbrıs yeşil ceviz macunu ve köpüklü Kıbrıs kahvesi', 'price' => 120, 'popular' => true, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Fırın Sütlaç', 'desc' => 'Fırınlanmış karamelize sütlaç, dövülmüş Antep fıstığı ile', 'price' => 160, 'popular' => false, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?auto=format&fit=crop&w=400&q=80'],
                    ],
                ],
            ],
            [
                'city' => 'girne',
                'name' => 'Eziç Peanuts Girne',
                'slug' => 'ezic-peanuts-girne',
                'cuisine' => 'Burger, Dünya Mutfağı & Cafe',
                'description' => "Deniz kıyısında muhteşem Akdeniz manzarası, meşhur tavuk spesiyalleri ve zengin burger menüsü.",
                'address' => 'Reyhan Sokak, Karakum Sahili, Girne',
                'latitude' => 35.3375,
                'longitude' => 33.3450,
                'phone' => '+90 392 444 39 42',
                'rating' => 4.8,
                'reviews_count' => 520,
                'price_range' => '₺₺',
                'image' => 'https://images.unsplash.com/photo-1550547660-d9450f859349?auto=format&fit=crop&w=800&q=80',
                'distance' => '1.2 km',
                'opening_hours' => '10:00 - 00:00',
                'is_popular' => true,
                'is_new' => false,
                'is_open' => true,
                'categories' => ['burger', 'cafe'],
                'menu' => [
                    'Burgerler & Sandviçler' => [
                        ['name' => 'Eziç Special Burger', 'desc' => '200g dana köfte, karamelize soğan, cheddar peyniri, füme et ve özel fıstık sosu', 'price' => 340, 'popular' => true, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Crispy Truffle Burger', 'desc' => 'Çıtır tavuk fileto, trüflü mayonez, avokado püresi ve çıtır patates', 'price' => 310, 'popular' => true, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1625813506062-0aeb1d7a094b?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Hellimli Smash Burger', 'desc' => 'İkili smash köfte, ızgara hellim, turşu ve acı tatlı sos', 'price' => 330, 'popular' => false, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'Tavuk & Spesiyaller' => [
                        ['name' => 'Peanuts Tavuk Kebabı', 'desc' => 'Özel marineli çıtır tavuk parçaları, fıstık soslu basmati pilav ile', 'price' => 360, 'popular' => true, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1562967914-608f82629710?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Fajita Tavuk', 'desc' => 'Döküm tavada sıcak sebzeli tavuk, salsa, ekşi krema ve guacamole ile', 'price' => 390, 'popular' => false, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1534790566855-4cb788d389ec?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'Tatlılar' => [
                        ['name' => 'Lotus Cheesecake', 'desc' => 'Biscoff lotus kreması ve çıtır bisküvi tabanı', 'price' => 190, 'popular' => true, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Sıcak Çikolatalı Sufle', 'desc' => 'Akışkan Belçika çikolatası ve vanilyalı Maraş dondurması', 'price' => 210, 'popular' => false, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&w=400&q=80'],
                    ],
                ],
            ],
            [
                'city' => 'girne',
                'name' => 'Girne Liman Balıkçısı',
                'slug' => 'girne-liman-balikcisi',
                'cuisine' => 'Deniz Ürünleri, Balık & Meze',
                'description' => "Tarihi Girne Kalesi gölgesinde, günlük taze Akdeniz balıkları, kalamar tava, ahtapot ızgara ve deniz mahsullü mezeler.",
                'address' => 'Tarihi Antik Liman No:8, Girne',
                'latitude' => 35.3428,
                'longitude' => 33.3225,
                'phone' => '+90 392 815 33 44',
                'rating' => 4.9,
                'reviews_count' => 289,
                'price_range' => '₺₺₺',
                'image' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&w=800&q=80',
                'distance' => '0.5 km',
                'opening_hours' => '12:00 - 00:30',
                'is_popular' => true,
                'is_new' => false,
                'is_open' => true,
                'categories' => ['deniz-urunleri'],
                'menu' => [
                    'Deniz Mezeleri' => [
                        ['name' => 'Izgara Ahtapot Bacağı', 'desc' => 'Zeytinyağı, kekik ve fava yatağında servis edilen Ege ahtapotu', 'price' => 420, 'popular' => true, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Tereyağlı Karides Güveç', 'desc' => 'Sarımsak, pul biber ve taze domates sosuyla pişmiş jumbo karides', 'price' => 380, 'popular' => true, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1559742811-822873691df8?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Tava Kalamar & Tarator', 'desc' => 'Çıtır Akdeniz kalamarı, ev yapımı cevizli tarator sos eşliğinde', 'price' => 360, 'popular' => false, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'Taze Balıklar' => [
                        ['name' => 'Kömürde Lagos Balığı', 'desc' => 'Akdeniz lagos ızgara, roka ve kırmızı soğan salatası ile', 'price' => 680, 'popular' => true, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Deniz Levreği Izgara', 'desc' => 'Limon ve zeytinyağlı marinasyon, fırınlanmış bebek patatesler ile', 'price' => 520, 'popular' => false, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&w=400&q=80'],
                    ],
                ],
            ],
            [
                'city' => 'girne',
                'name' => 'Bella Marin Lounge & Steak',
                'slug' => 'bella-marin-lounge-steak',
                'cuisine' => 'Steakhouse, İtalyan & Lounge',
                'description' => "Lord's Palace Hotel bünyesinde deniz üstü platformda premium dry-aged etler, şarap seçkisi ve DJ performansları.",
                'address' => 'Lord’s Palace Hotel Sahili, Girne',
                'latitude' => 35.3400,
                'longitude' => 33.3320,
                'phone' => '+90 392 650 35 00',
                'rating' => 4.9,
                'reviews_count' => 195,
                'price_range' => '₺₺₺',
                'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=800&q=80',
                'distance' => '2.1 km',
                'opening_hours' => '16:00 - 02:00',
                'is_popular' => true,
                'is_new' => true,
                'is_open' => true,
                'categories' => ['steak', 'bar'],
                'menu' => [
                    'Dry Aged Steakler' => [
                        ['name' => 'Tomahawk Steak (800g)', 'desc' => 'Kömür ızgarasında özel baharatlarla mühürlenmiş kemikli tomahawk, 2 kişilik', 'price' => 1400, 'popular' => true, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Dallas Steak (450g)', 'desc' => '35 gün dinlendirilmiş dana pirzola, fırın sarımsak ve trüflü patates ile', 'price' => 850, 'popular' => true, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1600891964599-f61ba0e24092?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'İmza Kokteyller' => [
                        ['name' => 'Cyprus Sunset Spritz', 'desc' => 'Kıbrıs turunç likörü, prosecco, taze nane ve nar taneleri', 'price' => 290, 'popular' => true, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Smoked Bourbon Old Fashioned', 'desc' => 'Kiraz ağacı dumanı ile islendirilmiş bourbon ve bitters', 'price' => 320, 'popular' => false, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&w=400&q=80'],
                    ],
                ],
            ],
            [
                'city' => 'girne',
                'name' => 'Cafe George Bellapais',
                'slug' => 'cafe-george-bellapais',
                'cuisine' => 'Pizza, İtalyan & Kahve',
                'description' => "Taş fırında çıtır Napoli pizzaları, el açması taze makarnalar ve artisanal kahveler.",
                'address' => 'Atatürk Meydanı No:4, Girne Merkez',
                'latitude' => 35.3380,
                'longitude' => 33.3180,
                'phone' => '+90 392 815 15 22',
                'rating' => 4.6,
                'reviews_count' => 410,
                'price_range' => '₺₺',
                'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=800&q=80',
                'distance' => '0.9 km',
                'opening_hours' => '09:00 - 23:00',
                'is_popular' => false,
                'is_new' => false,
                'is_open' => true,
                'categories' => ['pizza', 'cafe'],
                'menu' => [
                    'Taş Fırın Pizzalar' => [
                        ['name' => 'Pizza Margherita Di Bufala', 'desc' => 'San Marzano domates sosu, manda mozzarellası ve taze fesleğen', 'price' => 290, 'popular' => true, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Pizza Quattro Formaggi & Hellim', 'desc' => 'Gorgonzola, parmesan, mozzarella ve çıtır Kıbrıs hellim küpleri', 'price' => 340, 'popular' => true, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Diavola Acılı Pepperoni', 'desc' => 'İtalyan baharatlı dana sucuk, jalapeño ve acı zeytinyağı', 'price' => 330, 'popular' => false, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1628840042765-356cda07504e?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'Kahveler & İçecekler' => [
                        ['name' => 'Iced Salted Caramel Latte', 'desc' => 'Çifte espresso shot, deniz tuzu karamel sosu ve yulaf sütü', 'price' => 140, 'popular' => true, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Cold Brew Reserve', 'desc' => '16 saat damıtılmış tek kökenli Etiyopya kahvesi', 'price' => 130, 'popular' => false, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=400&q=80'],
                    ],
                ],
            ],
            [
                'city' => 'girne',
                'name' => 'Kyrenia Sushi & Wok Co.',
                'slug' => 'kyrenia-sushi-wok',
                'cuisine' => 'Sushi, Japon & Asya Mutfağı',
                'description' => "Taze somon ve ton balıklı rollar, bao burgerler, çıtır tempuralar ve wok lezzetleri.",
                'address' => 'Semih Sancar Caddesi No:45, Girne',
                'latitude' => 35.3340,
                'longitude' => 33.3280,
                'phone' => '+90 392 815 88 99',
                'rating' => 4.8,
                'reviews_count' => 210,
                'price_range' => '₺₺₺',
                'image' => 'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?auto=format&fit=crop&w=800&q=80',
                'distance' => '1.5 km',
                'opening_hours' => '12:30 - 23:00',
                'is_popular' => false,
                'is_new' => true,
                'is_open' => true,
                'categories' => ['sushi'],
                'menu' => [
                    'Special Rollar (8 Parça)' => [
                        ['name' => 'Dragon Volcano Roll', 'desc' => 'Tempura karides, avokado üzeri fırınlanmış somon tartarı ve teriyaki glaze', 'price' => 430, 'popular' => true, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1579871494447-9811cf80d66c?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Philadelphia Salmon Roll', 'desc' => 'Norveç somonu, taze krem peynir, salatalık ve çıtır panko', 'price' => 390, 'popular' => true, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1611143669185-af224c5e3252?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Crunchy Tiger Roll', 'desc' => 'Çıtır karides tempura, baharatlı ton balığı ve sriracha mayonez', 'price' => 410, 'popular' => false, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1617196034796-73dfa7b1fd56?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'Wok & Sıcak Başlangıçlar' => [
                        ['name' => 'Tavuklu Pad Thai', 'desc' => 'Pirinç eriştesi, fıstık, soya filizi, yumurta ve tamarind sos', 'price' => 340, 'popular' => true, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Edamame Deniz Tuzlu', 'desc' => 'Buharda pişmiş soya fasulyesi ve pul deniz tuzu', 'price' => 160, 'popular' => false, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=400&q=80'],
                    ],
                ],
            ],
            [
                'city' => 'lefkosa',
                'name' => 'Anibal Kebap Lefkoşa',
                'slug' => 'anibal-kebap-lefkosa',
                'cuisine' => 'Geleneksel Kebap, Şeftali & Kıbrıs Mutfağı',
                'description' => "Lefkoşa'nın en köklü kebapçılarından, gerçek Kıbrıs şeftali kebabı ve fırın lezzetleri.",
                'address' => 'Surlariçi, Zahra Sokak No:19, Lefkoşa',
                'latitude' => 35.1780,
                'longitude' => 33.3610,
                'phone' => '+90 392 227 12 50',
                'rating' => 4.7,
                'reviews_count' => 480,
                'price_range' => '₺₺',
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=800&q=80',
                'distance' => '3.4 km',
                'opening_hours' => '11:00 - 22:30',
                'is_popular' => true,
                'is_new' => false,
                'is_open' => true,
                'categories' => ['kebap', 'steak'],
                'menu' => [
                    'Kıbrıs Kebapları' => [
                        ['name' => 'Özel Şeftali Kebabı Porsiyon', 'desc' => 'Tırnak pide üzerinde 4 adet kömürde pişmiş şeftali köfte, maydanoz ve soğan ile', 'price' => 360, 'popular' => true, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Karışık Lefkoşa Izgara', 'desc' => 'Şeftali kebabı, kuzu şiş, tavuk pirzola ve hellim ızgara', 'price' => 490, 'popular' => true, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=400&q=80'],
                    ],
                ],
            ],
            [
                'city' => 'gazimagusa',
                'name' => 'Petek Pastanesi Mağusa',
                'slug' => 'petek-pastanesi-magusa',
                'cuisine' => 'Tatlı, Kahvaltı & Cafe',
                'description' => "1976'dan bugüne Gazimağusa Limanı girişinde Kıbrıs tatlıları, dondurma ve zengin brunch seçenekleri.",
                'address' => 'İsmet İnönü Bulvarı No:1, Gazimağusa',
                'latitude' => 35.1240,
                'longitude' => 33.9420,
                'phone' => '+90 392 366 71 04',
                'rating' => 4.9,
                'reviews_count' => 670,
                'price_range' => '₺₺',
                'image' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=800&q=80',
                'distance' => '4.1 km',
                'opening_hours' => '08:00 - 23:30',
                'is_popular' => true,
                'is_new' => false,
                'is_open' => true,
                'categories' => ['tatli', 'cafe'],
                'menu' => [
                    'Geleneksel Tatlılar' => [
                        ['name' => 'Gül Suyu & Damla Sakızlı Su Muhallebisi', 'desc' => 'Kıbrıs usulü gülsuyu şerbeti ve dondurma ile servis edilen su muhallebisi', 'price' => 150, 'popular' => true, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Petek Havuç Dilim Baklava', 'desc' => 'Bol Antep fıstıklı sıcak baklava, kaymaklı dondurma eşliğinde', 'price' => 220, 'popular' => true, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?auto=format&fit=crop&w=400&q=80'],
                    ],
                ],
            ],
            [
                'city' => 'iskele',
                'name' => 'The Long Beach Pizza & Grill',
                'slug' => 'the-long-beach-pizza-grill',
                'cuisine' => 'Pizza, Burger & Kokteyl',
                'description' => "İskele sahil şeridinde deniz esintisiyle taş fırın pizzalar, gurme burgerler ve ferahlatıcı kokteyller.",
                'address' => 'Long Beach Promenade No:15, İskele',
                'latitude' => 35.2910,
                'longitude' => 33.8920,
                'phone' => '+90 392 371 44 22',
                'rating' => 4.7,
                'reviews_count' => 175,
                'price_range' => '₺₺',
                'image' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=800&q=80',
                'distance' => '5.2 km',
                'opening_hours' => '11:00 - 00:00',
                'is_popular' => false,
                'is_new' => true,
                'is_open' => true,
                'categories' => ['pizza', 'burger', 'bar'],
                'menu' => [
                    'Odun Ateşinde Pizzalar' => [
                        ['name' => 'Long Beach Supreme Pizza', 'desc' => 'Sucuk, mantar, köz biber, mısır, zeytin ve çift kat mozzarella', 'price' => 330, 'popular' => true, 'chef' => true, 'image' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=400&q=80'],
                        ['name' => 'Akdeniz Hellimli Pizza', 'desc' => 'Domates sos, roka, kurutulmuş domates ve ızgara hellim küpleri', 'price' => 310, 'popular' => false, 'chef' => false, 'image' => 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002?auto=format&fit=crop&w=400&q=80'],
                    ],
                ],
            ],
        ];

        foreach ($restaurants as $item) {
            $city = $cities[$item['city']] ?? $cities['girne'];

            $restaurant = Restaurant::firstOrCreate(
                ['slug' => $item['slug']],
                [
                    'city_id' => $city->id,
                    'name' => $item['name'],
                    'cuisine' => $item['cuisine'],
                    'description' => $item['description'],
                    'address' => $item['address'],
                    'latitude' => $item['latitude'],
                    'longitude' => $item['longitude'],
                    'phone' => $item['phone'],
                    'rating' => $item['rating'],
                    'reviews_count' => $item['reviews_count'],
                    'price_range' => $item['price_range'],
                    'image' => $item['image'],
                    'distance' => $item['distance'],
                    'opening_hours' => $item['opening_hours'],
                    'is_popular' => $item['is_popular'],
                    'is_new' => $item['is_new'],
                    'is_open' => $item['is_open'],
                ]
            );

            // Attach categories
            $categoryIds = [];
            foreach ($item['categories'] as $catSlug) {
                if (isset($categories[$catSlug])) {
                    $categoryIds[] = $categories[$catSlug]->id;
                }
            }
            $restaurant->categories()->sync($categoryIds);

            // Add menus
            $orderCat = 1;
            foreach ($item['menu'] as $menuCatName => $menuItems) {
                $menuCat = MenuCategory::firstOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'name' => $menuCatName,
                    ],
                    [
                        'order' => $orderCat++,
                    ]
                );

                $orderItem = 1;
                foreach ($menuItems as $mItem) {
                    MenuItem::updateOrCreate(
                        [
                            'restaurant_id' => $restaurant->id,
                            'name' => $mItem['name'],
                        ],
                        [
                            'menu_category_id' => $menuCat->id,
                            'description' => $mItem['desc'],
                            'price' => $mItem['price'],
                            'currency' => '₺',
                            'image' => $mItem['image'] ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80',
                            'is_popular' => $mItem['popular'] ?? false,
                            'is_chef_special' => $mItem['chef'] ?? false,
                            'order' => $orderItem++,
                        ]
                    );
                }
            }
        }
    }
}

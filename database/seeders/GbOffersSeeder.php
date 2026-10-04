<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Gang;
use App\Models\GangMember;
use App\Models\Merchant;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GbOffersSeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['name' => 'Food', 'slug' => 'food', 'accent_color' => '#b45309', 'icon' => 'utensils'],
            ['name' => 'Electronics', 'slug' => 'electronics', 'accent_color' => '#1d4ed8', 'icon' => 'tv'],
            ['name' => 'Fashion', 'slug' => 'fashion', 'accent_color' => '#831843', 'icon' => 'shirt'],
            ['name' => 'Home', 'slug' => 'home', 'accent_color' => '#166534', 'icon' => 'sofa'],
            ['name' => 'Beauty', 'slug' => 'beauty', 'accent_color' => '#9d174d', 'icon' => 'sparkles'],
            ['name' => 'Services', 'slug' => 'services', 'accent_color' => '#0f766e', 'icon' => 'wrench'],
            ['name' => 'Experiences', 'slug' => 'experiences', 'accent_color' => '#6d28d9', 'icon' => 'ticket'],
            ['name' => 'Travel', 'slug' => 'travel', 'accent_color' => '#0369a1', 'icon' => 'map'],
        ];
        foreach ($cats as $i => $c) {
            Category::firstOrCreate(['slug' => $c['slug']], $c + ['sort' => $i]);
        }
        $owner = User::firstOrCreate(['email' => 'merchant@demo.ug'], ['name' => 'Demo Merchant', 'password' => Hash::make('password'), 'role' => 'merchant_owner', 'buyer_trust' => 'trusted']);
        $owner->profile()->firstOrCreate([], ['display_name' => 'Demo Merchant', 'trust_level' => 'trusted', 'completed_count' => 8]);
        $merchants = [
            ['business_name' => 'Kampala Kitchen', 'trading_name' => 'Kampala Kitchen', 'slug' => 'kampala-kitchen', 'category' => 'food', 'phone' => '+256772111111', 'location' => 'Kampala, Kisementi', 'payment_details' => ['network' => 'MTN', 'account_name' => 'Kampala Kitchen', 'account_number' => '0772111111']],
            ['business_name' => 'Pearl Electronics', 'trading_name' => 'Pearl Electronics', 'slug' => 'pearl-electronics', 'category' => 'electronics', 'phone' => '+256772222222', 'location' => 'Kampala, Kampala Road', 'payment_details' => ['network' => 'Airtel', 'account_name' => 'Pearl Electronics', 'account_number' => '0752222222']],
            ['business_name' => 'City Style', 'trading_name' => 'City Style', 'slug' => 'city-style', 'category' => 'fashion', 'phone' => '+256772333333', 'location' => 'Kampala, Owino', 'payment_details' => ['network' => 'MTN', 'account_name' => 'City Style', 'account_number' => '0772333333']],
            ['business_name' => 'HomeHub Uganda', 'trading_name' => 'HomeHub Uganda', 'slug' => 'homehub-uganda', 'category' => 'home', 'phone' => '+256772444444', 'location' => 'Kampala, Ntinda', 'payment_details' => ['network' => 'MTN', 'account_name' => 'HomeHub Uganda', 'account_number' => '0772444444']],
        ];
        foreach ($merchants as $m) {
            $cat = Category::where('slug', $m['category'])->first();
            Merchant::firstOrCreate(['slug' => $m['slug']], ['user_id' => $owner->id, 'category_id' => $cat?->id, 'business_name' => $m['business_name'], 'trading_name' => $m['trading_name'], 'description' => 'Verified demo merchant for GBOffers.', 'phone' => $m['phone'], 'location' => $m['location'], 'payment_details' => $m['payment_details'], 'verification_status' => 'approved', 'verified_at' => now(), 'completed_count' => 1248, 'fulfillment_rate' => 98.7, 'return_policy' => '7-day return for unopened items.', 'delivery_options' => ['pickup' => true, 'delivery' => true], 'is_active' => true]);
        }
        $offers = [
            ['merchant' => 'kampala-kitchen', 'category' => 'food', 'title' => '12-Piece Chicken Bucket', 'slug' => '12-piece-chicken-bucket', 'normal_price' => 85000, 'tiers' => [[1, 85000], [5, 75000], [10, 68000]], 'target' => 8, 'cta' => 'JOIN THE FOOD GANG'],
            ['merchant' => 'pearl-electronics', 'category' => 'electronics', 'title' => 'Samsung 55\" Smart TV', 'slug' => 'samsung-55-inch-smart-tv', 'normal_price' => 1850000, 'tiers' => [[1, 1800000], [5, 1650000], [10, 1550000]], 'target' => 10, 'cta' => 'JOIN THE GANG'],
            ['merchant' => 'city-style', 'category' => 'fashion', 'title' => 'Ankara Style Set', 'slug' => 'ankara-style-set', 'normal_price' => 120000, 'tiers' => [[1, 120000], [4, 105000], [8, 95000]], 'target' => 6, 'cta' => 'STYLE GANG'],
            ['merchant' => 'homehub-uganda', 'category' => 'home', 'title' => '3-Seater Fabric Sofa', 'slug' => '3-seater-fabric-sofa', 'normal_price' => 950000, 'tiers' => [[1, 950000], [3, 880000], [6, 820000]], 'target' => 6, 'cta' => 'HOUSEHOLDS'],
        ];
        foreach ($offers as $o) {
            $merchant = Merchant::where('slug', $o['merchant'])->first();
            $cat = Category::where('slug', $o['category'])->first();
            $offer = Offer::firstOrCreate(['slug' => $o['slug']], ['merchant_id' => $merchant->id, 'category_id' => $cat?->id, 'title' => $o['title'], 'description' => 'Demo offer for '.$o['title'].'. Group terms apply.', 'normal_price' => $o['normal_price'], 'quantity_total' => 50, 'min_buyers' => 2, 'max_buyers' => 100, 'gang_target' => $o['target'], 'starts_at' => now()->subDay(), 'ends_at' => now()->addDays(13), 'payment_deadline' => now()->addDays(14), 'redemption_deadline' => now()->addDays(30), 'fulfillment_method' => 'pickup', 'pickup_location' => $merchant->location, 'delivery_available' => true, 'terms' => 'Group price unlocks when the target is reached.', 'cancellation_policy' => 'Cancel before payment confirmation for a full release of your spot.', 'status' => 'active', 'featured' => true]);
            $offer->priceTiers()->delete();
            foreach ($o['tiers'] as $i => [$min,$price]) {
                $offer->priceTiers()->create(['min_qty' => $min, 'price' => $price, 'sort' => $i]);
            }
            Gang::firstOrCreate(['offer_id' => $offer->id, 'status' => 'forming'], ['code' => 'GANG-'.strtoupper(substr(md5($offer->slug), 0, 6)), 'target' => $o['target'], 'expires_at' => $offer->ends_at]);
        }
        // demo customers
        for ($i = 1; $i <= 6; $i++) {
            $u = User::firstOrCreate(['email' => "customer{$i}@demo.ug"], ['name' => "Customer {$i}", 'password' => Hash::make('password'), 'role' => 'customer']);
            $u->profile()->firstOrCreate([], ['display_name' => "Customer {$i}", 'phone' => '+25670000000'.$i]);
        }
        $this->seedCatalog($owner);
        $this->command->info('GBOffers demo data seeded.');
    }

    private function seedCatalog(User $owner): void
    {
        $extraMerchants = [
            ['business_name' => 'Glow Beauty', 'trading_name' => 'Glow Beauty', 'slug' => 'glow-beauty', 'category' => 'beauty', 'phone' => '+256772555555', 'location' => 'Kampala, Acacia Mall'],
            ['business_name' => 'FixIt Services', 'trading_name' => 'FixIt Services', 'slug' => 'fixit-services', 'category' => 'services', 'phone' => '+256772666666', 'location' => 'Kampala, Bugolobi'],
            ['business_name' => 'Safari Nights', 'trading_name' => 'Safari Nights', 'slug' => 'safari-nights', 'category' => 'experiences', 'phone' => '+256772777777', 'location' => 'Kampala, Kololo'],
            ['business_name' => 'Nile Travel', 'trading_name' => 'Nile Travel', 'slug' => 'nile-travel', 'category' => 'travel', 'phone' => '+256772888888', 'location' => 'Kampala, Entebbe Road'],
        ];
        foreach ($extraMerchants as $m) {
            $cat = Category::where('slug', $m['category'])->first();
            Merchant::firstOrCreate(['slug' => $m['slug']], ['user_id' => $owner->id, 'category_id' => $cat?->id, 'business_name' => $m['business_name'], 'trading_name' => $m['trading_name'], 'description' => 'Verified demo merchant for GBOffers.', 'phone' => $m['phone'], 'location' => $m['location'], 'payment_details' => ['network' => 'MTN', 'account_name' => $m['business_name'], 'account_number' => '0772000000'], 'verification_status' => 'approved', 'verified_at' => now(), 'fulfillment_rate' => 97.5, 'return_policy' => '7-day return for unopened items.', 'delivery_options' => ['pickup' => true, 'delivery' => true], 'is_active' => true]);
        }
        // [merchant_slug, category_slug, title, normal_price]
        $catalog = [
            ['kampala-kitchen', 'food', 'Rolex Combo Platter', 25000], ['kampala-kitchen', 'food', 'Nsenene Season Pack', 45000],
            ['kampala-kitchen', 'food', 'Matooke & Groundnut Bundle', 38000], ['kampala-kitchen', 'food', 'Grilled Tilapia Feast', 65000],
            ['kampala-kitchen', 'food', 'Luwombo Party Pack', 55000], ['kampala-kitchen', 'food', 'Chapati & Beans Family Box', 30000],
            ['kampala-kitchen', 'food', 'Pineapple Juice Crate', 28000], ['kampala-kitchen', 'food', 'Coffee Beans 1kg', 48000],
            ['kampala-kitchen', 'food', 'Honey 500ml', 35000], ['kampala-kitchen', 'food', 'Ghee 1L', 42000],
            ['kampala-kitchen', 'food', 'Cassava Flour Sack', 33000], ['kampala-kitchen', 'food', 'Samosa Party Tray', 27000],
            ['kampala-kitchen', 'food', 'Pork Joint Platter', 58000],
            ['pearl-electronics', 'electronics', 'Hisense 43 inch TV', 1250000], ['pearl-electronics', 'electronics', 'Solar Home Kit', 680000],
            ['pearl-electronics', 'electronics', 'Bluetooth Speaker', 185000], ['pearl-electronics', 'electronics', 'Power Bank 20000mAh', 95000],
            ['pearl-electronics', 'electronics', 'LED Bulb Box 10pc', 120000], ['pearl-electronics', 'electronics', 'Standing Fan', 240000],
            ['pearl-electronics', 'electronics', 'Rice Cooker', 195000], ['pearl-electronics', 'electronics', 'Electric Kettle', 145000],
            ['pearl-electronics', 'electronics', 'Extension Reel', 85000], ['pearl-electronics', 'electronics', 'Wiring Set Bundle', 160000],
            ['pearl-electronics', 'electronics', 'USB-C Cable 3-Pack', 45000], ['pearl-electronics', 'electronics', 'Table Lamp', 90000],
            ['pearl-electronics', 'electronics', 'Air Fryer', 320000],
            ['city-style', 'fashion', 'Gomesi Dress', 95000], ['city-style', 'fashion', 'Kitenge Fabric 6 Yards', 78000],
            ['city-style', 'fashion', 'Leather Sandals', 65000], ['city-style', 'fashion', 'Bridesmaid Package', 350000],
            ['city-style', 'fashion', 'Mens Kaunda Suit', 280000], ['city-style', 'fashion', 'Handbag', 135000],
            ['city-style', 'fashion', 'Sneakers', 175000], ['city-style', 'fashion', 'Hijab Bundle 5pc', 110000],
            ['city-style', 'fashion', 'Kids Party Outfits', 88000], ['city-style', 'fashion', 'Headwraps 3pc', 45000],
            ['city-style', 'fashion', 'Belt and Wallet Set', 70000], ['city-style', 'fashion', 'Denim Jacket', 155000],
            ['city-style', 'fashion', 'Second-hand Bale Share', 90000],
            ['homehub-uganda', 'home', '5x7 Mattress', 620000], ['homehub-uganda', 'home', 'Plastic Chairs Set of 6', 390000],
            ['homehub-uganda', 'home', 'Curtain Set', 150000], ['homehub-uganda', 'home', 'Water Tank 500L', 480000],
            ['homehub-uganda', 'home', 'Charcoal Stove Jiko', 85000], ['homehub-uganda', 'home', 'Heavy Duty Blanket', 120000],
            ['homehub-uganda', 'home', 'Melamine Plates Dozen', 95000], ['homehub-uganda', 'home', 'Office Desk', 340000],
            ['homehub-uganda', 'home', 'Bookshelf', 260000], ['homehub-uganda', 'home', 'Treated Mosquito Net', 55000],
            ['homehub-uganda', 'home', 'Laundry Basket Duo', 65000], ['homehub-uganda', 'home', 'Wall Clock', 75000],
            ['homehub-uganda', 'home', 'Gas Cylinder Refill 13kg', 185000],
            ['glow-beauty', 'beauty', 'Shea Butter Jar', 40000], ['glow-beauty', 'beauty', 'Braiding Hair Pack', 60000],
            ['glow-beauty', 'beauty', 'Human Blend Wig', 250000], ['glow-beauty', 'beauty', 'Makeup Starter Kit', 180000],
            ['glow-beauty', 'beauty', 'Perfume 100ml', 140000], ['glow-beauty', 'beauty', 'Facial Treatment Voucher', 90000],
            ['glow-beauty', 'beauty', 'Manicure and Pedicure Deal', 75000], ['glow-beauty', 'beauty', 'Skin Glow Oil', 55000],
            ['glow-beauty', 'beauty', 'Hair Dryer', 130000], ['glow-beauty', 'beauty', 'Beard Grooming Kit', 95000],
            ['glow-beauty', 'beauty', 'Lipstick Set 5pc', 65000], ['glow-beauty', 'beauty', 'Spa Day Pass', 200000],
            ['fixit-services', 'services', 'House Deep Cleaning', 150000], ['fixit-services', 'services', 'AC Repair Visit', 120000],
            ['fixit-services', 'services', 'Plumbing Fix Bundle', 100000], ['fixit-services', 'services', 'Salon Home Service', 80000],
            ['fixit-services', 'services', 'Car Wash Monthly Plan', 180000], ['fixit-services', 'services', 'Tailoring 3 Outfits', 140000],
            ['fixit-services', 'services', 'Phone Screen Replacement', 110000], ['fixit-services', 'services', 'DSTV Installation', 130000],
            ['fixit-services', 'services', 'Fumigation 2BR', 160000], ['fixit-services', 'services', 'Local Movers Package', 450000],
            ['fixit-services', 'services', 'Tutoring 10 Sessions', 200000], ['fixit-services', 'services', 'Generator Service', 170000],
            ['safari-nights', 'experiences', 'Sipi Falls Day Trip', 220000], ['safari-nights', 'experiences', 'Jinja Rafting Slot', 350000],
            ['safari-nights', 'experiences', 'Kampala Food Tour', 120000], ['safari-nights', 'experiences', 'Cinema Private Hall', 500000],
            ['safari-nights', 'experiences', 'Paint and Sip Evening', 100000], ['safari-nights', 'experiences', 'Go-Kart 10 Laps', 90000],
            ['safari-nights', 'experiences', 'Lake Victoria Cruise', 280000], ['safari-nights', 'experiences', 'Comedy Night Table', 160000],
            ['safari-nights', 'experiences', 'Yoga Retreat Day', 140000], ['safari-nights', 'experiences', 'Photography Session', 180000],
            ['safari-nights', 'experiences', 'Bowling Night 4 Pax', 130000], ['safari-nights', 'experiences', 'Museum Guided Tour', 60000],
            ['nile-travel', 'travel', 'Entebbe Airport Transfers Return', 150000], ['nile-travel', 'travel', 'Sesse Islands Ferry and Stay', 480000],
            ['nile-travel', 'travel', 'Murchison 2-Day Safari', 950000], ['nile-travel', 'travel', 'Kigali Weekend Bus', 260000],
            ['nile-travel', 'travel', 'Hotel Night Kololo', 320000], ['nile-travel', 'travel', 'Car Hire with Driver Day', 250000],
            ['nile-travel', 'travel', 'Gorilla Trek Package', 2800000], ['nile-travel', 'travel', 'Zanzibar Flight Deal', 1100000],
        ];
        $groups = ['Students', 'Staff SACCO', 'Church Group', 'Campus Hostel', 'Corporate Team'];
        foreach ($catalog as $j => [$mslug, $cslug, $title, $price]) {
            $merchant = Merchant::where('slug', $mslug)->first();
            $cat = Category::where('slug', $cslug)->first();
            $slug = Str::slug($title);
            $target = 4 + (($j * 7) % 9);
            $t1 = max(2, (int) floor($target * 0.6));
            $round = fn ($v) => (int) (round($v / 100) * 100);
            $offer = Offer::firstOrCreate(['slug' => $slug], [
                'merchant_id' => $merchant->id, 'category_id' => $cat?->id, 'title' => $title,
                'description' => 'Group deal on '.$title.'. Group terms apply.', 'normal_price' => $price,
                'quantity_total' => 20 + (($j * 13) % 60), 'min_buyers' => 2, 'gang_target' => $target,
                'starts_at' => now()->subDay(), 'ends_at' => now()->addDays(3 + (($j * 5) % 18)),
                'payment_deadline' => now()->addDays(21), 'redemption_deadline' => now()->addDays(35),
                'fulfillment_method' => 'pickup', 'pickup_location' => $merchant->location, 'delivery_available' => true,
                'terms' => 'Group price unlocks when the target is reached.', 'status' => 'active', 'featured' => ($j % 4 === 0),
                'interested_count' => ($j * 3) % 9,
            ]);
            if ($offer->priceTiers()->count() === 0) {
                $offer->priceTiers()->createMany([
                    ['min_qty' => 1, 'price' => $price, 'sort' => 0],
                    ['min_qty' => $t1, 'price' => $round($price * 0.93), 'sort' => 1],
                    ['min_qty' => $target, 'price' => $round($price * 0.86), 'sort' => 2],
                ]);
            }
            if (($j * 11) % 10 < 7) {
                $gang = Gang::firstOrCreate(['offer_id' => $offer->id, 'status' => 'forming'], [
                    'code' => 'GANG-'.strtoupper(substr(md5($slug), 0, 6)), 'target' => $target, 'expires_at' => $offer->ends_at,
                ]);
                if ($gang->wasRecentlyCreated && ($j * 5) % 2 === 0) {
                    $confirmed = 1 + (($j * 3) % max(1, $target - 1));
                    $gang->update(['confirmed_count' => $confirmed]);
                    $offer->update(['confirmed_count' => $confirmed]);
                }
            }
            if (($j * 13) % 3 === 0 && $offer->audiences()->count() === 0) {
                $offer->audiences()->create([
                    'label' => $groups[$j % count($groups)], 'min_buyers' => 5 + ($j % 6),
                    'price' => $round($price * 0.9), 'sort' => 0,
                ]);
            }
        }
        // real joiners so avatar stacks show people, not just counts
        $customers = User::where('role', 'customer')->orderBy('id')->get();
        if ($customers->isNotEmpty()) {
            $gangs = Gang::where('status', 'forming')->orderBy('id')->get();
            foreach ($gangs as $gi => $gang) {
                $n = 1 + (($gi * 5) % 4);
                for ($k = 0; $k < $n; $k++) {
                    $u = $customers[($gi + $k * 2) % $customers->count()];
                    GangMember::firstOrCreate(
                        ['gang_id' => $gang->id, 'user_id' => $u->id],
                        ['offer_id' => $gang->offer_id, 'status' => $k % 2 ? 'reserved' : 'interested', 'joined_at' => now()->subHours($gi + $k)]
                    );
                }
            }
        }
    }
}

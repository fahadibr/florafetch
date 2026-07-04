<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $indoor     = Category::where('name', 'Indoor')->first();
        $outdoor    = Category::where('name', 'Outdoor')->first();
        $succulents = Category::where('name', 'Succulents')->first();
        $flowering  = Category::where('name', 'Flowering')->first();
        $medicinal  = Category::where('name', 'Medicinal')->first();
        $essentials = Category::where('name', 'Gardening Essentials')->first();

        $products = [
            // Indoor
            [
                'category_id'          => $indoor->id,
                'common_name'          => 'Peace Lily',
                'botanical_name'       => 'Spathiphyllum wallisii',
                'description'          => 'A graceful indoor plant known for its white blooms and air-purifying qualities.',
                'size'                 => 'medium',
                'price'                => 850.00,
                'stock_quantity'       => 25,
                'sunlight_requirement' => 'Low to Medium Indirect Light',
                'watering_frequency'   => 'Once a week',
                'soil_recommendation'  => 'Well-draining potting mix',
                'temperature_min_c'    => 18,
                'temperature_max_c'    => 30,
                'is_low_maintenance'   => true,
                'is_pet_friendly'      => false,
                'growth_rate'          => 'Moderate',
            ],
            [
                'category_id'          => $indoor->id,
                'common_name'          => 'Snake Plant',
                'botanical_name'       => 'Sansevieria trifasciata',
                'description'          => 'Nearly indestructible and perfect for beginners. Tolerates low light and infrequent watering.',
                'size'                 => 'large',
                'price'                => 1200.00,
                'stock_quantity'       => 30,
                'sunlight_requirement' => 'Low to Bright Indirect Light',
                'watering_frequency'   => 'Every 2–3 weeks',
                'soil_recommendation'  => 'Sandy, well-draining soil',
                'temperature_min_c'    => 15,
                'temperature_max_c'    => 35,
                'is_low_maintenance'   => true,
                'is_pet_friendly'      => false,
                'growth_rate'          => 'Slow',
            ],
            [
                'category_id'          => $indoor->id,
                'common_name'          => 'Pothos',
                'botanical_name'       => 'Epipremnum aureum',
                'description'          => 'A trailing vine with heart-shaped leaves, ideal for shelves and hanging baskets.',
                'size'                 => 'small',
                'price'                => 450.00,
                'stock_quantity'       => 50,
                'sunlight_requirement' => 'Low to Medium Indirect Light',
                'watering_frequency'   => 'Every 1–2 weeks',
                'soil_recommendation'  => 'Standard potting mix',
                'temperature_min_c'    => 15,
                'temperature_max_c'    => 30,
                'is_low_maintenance'   => true,
                'is_pet_friendly'      => false,
                'growth_rate'          => 'Fast',
            ],
            [
                'category_id'          => $indoor->id,
                'common_name'          => 'ZZ Plant',
                'botanical_name'       => 'Zamioculcas zamiifolia',
                'description'          => 'Glossy, dark green leaves and extreme drought tolerance make this a top choice for busy plant parents.',
                'size'                 => 'medium',
                'price'                => 950.00,
                'stock_quantity'       => 20,
                'sunlight_requirement' => 'Low to Bright Indirect Light',
                'watering_frequency'   => 'Every 2–3 weeks',
                'soil_recommendation'  => 'Well-draining potting mix',
                'temperature_min_c'    => 15,
                'temperature_max_c'    => 35,
                'is_low_maintenance'   => true,
                'is_pet_friendly'      => false,
                'growth_rate'          => 'Slow',
            ],
            // Outdoor
            [
                'category_id'          => $outdoor->id,
                'common_name'          => 'Bougainvillea',
                'botanical_name'       => 'Bougainvillea spectabilis',
                'description'          => 'A vibrant climbing shrub with stunning magenta bracts, perfect for walls and trellises.',
                'size'                 => 'large',
                'price'                => 1500.00,
                'stock_quantity'       => 15,
                'sunlight_requirement' => 'Full Sun (6+ hours)',
                'watering_frequency'   => 'Twice a week',
                'soil_recommendation'  => 'Well-draining, slightly acidic soil',
                'temperature_min_c'    => 10,
                'temperature_max_c'    => 40,
                'is_low_maintenance'   => false,
                'is_pet_friendly'      => false,
                'growth_rate'          => 'Fast',
            ],
            [
                'category_id'          => $outdoor->id,
                'common_name'          => 'Jasmine',
                'botanical_name'       => 'Jasminum sambac',
                'description'          => 'Fragrant white flowers that bloom throughout the year. A classic in Pakistani gardens.',
                'size'                 => 'medium',
                'price'                => 700.00,
                'stock_quantity'       => 35,
                'sunlight_requirement' => 'Full Sun to Partial Shade',
                'watering_frequency'   => 'Every 2–3 days',
                'soil_recommendation'  => 'Loamy, well-draining soil',
                'temperature_min_c'    => 15,
                'temperature_max_c'    => 38,
                'is_low_maintenance'   => false,
                'is_pet_friendly'      => true,
                'growth_rate'          => 'Moderate',
            ],
            // Succulents
            [
                'category_id'          => $succulents->id,
                'common_name'          => 'Aloe Vera',
                'botanical_name'       => 'Aloe barbadensis miller',
                'description'          => 'A multipurpose succulent with thick, fleshy leaves filled with soothing gel.',
                'size'                 => 'small',
                'price'                => 350.00,
                'stock_quantity'       => 60,
                'sunlight_requirement' => 'Bright Direct or Indirect Light',
                'watering_frequency'   => 'Every 2–3 weeks',
                'soil_recommendation'  => 'Cactus/succulent mix',
                'temperature_min_c'    => 13,
                'temperature_max_c'    => 40,
                'is_low_maintenance'   => true,
                'is_pet_friendly'      => false,
                'growth_rate'          => 'Slow',
            ],
            [
                'category_id'          => $succulents->id,
                'common_name'          => 'Echeveria',
                'botanical_name'       => 'Echeveria elegans',
                'description'          => 'A rosette-forming succulent with powdery blue-green leaves. Great for windowsills.',
                'size'                 => 'small',
                'price'                => 280.00,
                'stock_quantity'       => 45,
                'sunlight_requirement' => 'Bright Direct Light',
                'watering_frequency'   => 'Every 2 weeks',
                'soil_recommendation'  => 'Cactus/succulent mix',
                'temperature_min_c'    => 10,
                'temperature_max_c'    => 35,
                'is_low_maintenance'   => true,
                'is_pet_friendly'      => true,
                'growth_rate'          => 'Slow',
            ],
            // Flowering
            [
                'category_id'          => $flowering->id,
                'common_name'          => 'Anthurium',
                'botanical_name'       => 'Anthurium andraeanum',
                'description'          => 'Striking waxy red spathes that last for months. A showstopper for any room.',
                'size'                 => 'medium',
                'price'                => 1100.00,
                'stock_quantity'       => 18,
                'sunlight_requirement' => 'Bright Indirect Light',
                'watering_frequency'   => 'Once a week',
                'soil_recommendation'  => 'Orchid bark mix or chunky potting mix',
                'temperature_min_c'    => 18,
                'temperature_max_c'    => 32,
                'is_low_maintenance'   => false,
                'is_pet_friendly'      => false,
                'growth_rate'          => 'Moderate',
            ],
            [
                'category_id'          => $flowering->id,
                'common_name'          => 'Hibiscus',
                'botanical_name'       => 'Hibiscus rosa-sinensis',
                'description'          => 'Large, trumpet-shaped flowers in red, pink, yellow, and orange. A tropical beauty.',
                'size'                 => 'large',
                'price'                => 900.00,
                'stock_quantity'       => 22,
                'sunlight_requirement' => 'Full Sun',
                'watering_frequency'   => 'Daily in summer',
                'soil_recommendation'  => 'Rich, well-draining soil',
                'temperature_min_c'    => 15,
                'temperature_max_c'    => 40,
                'is_low_maintenance'   => false,
                'is_pet_friendly'      => true,
                'growth_rate'          => 'Fast',
            ],
            // Medicinal
            [
                'category_id'          => $medicinal->id,
                'common_name'          => 'Tulsi (Holy Basil)',
                'botanical_name'       => 'Ocimum tenuiflorum',
                'description'          => 'Sacred and medicinal herb used in teas and Ayurvedic remedies. Easy to grow.',
                'size'                 => 'small',
                'price'                => 250.00,
                'stock_quantity'       => 70,
                'sunlight_requirement' => 'Full Sun',
                'watering_frequency'   => 'Every 2 days',
                'soil_recommendation'  => 'Loamy, well-draining soil',
                'temperature_min_c'    => 20,
                'temperature_max_c'    => 38,
                'is_low_maintenance'   => true,
                'is_pet_friendly'      => true,
                'growth_rate'          => 'Fast',
            ],
            [
                'category_id'          => $medicinal->id,
                'common_name'          => 'Mint',
                'botanical_name'       => 'Mentha spicata',
                'description'          => 'Refreshing and aromatic herb perfect for teas, cooking, and natural remedies.',
                'size'                 => 'small',
                'price'                => 200.00,
                'stock_quantity'       => 80,
                'sunlight_requirement' => 'Partial to Full Sun',
                'watering_frequency'   => 'Every 2 days',
                'soil_recommendation'  => 'Moist, rich soil',
                'temperature_min_c'    => 10,
                'temperature_max_c'    => 35,
                'is_low_maintenance'   => true,
                'is_pet_friendly'      => false,
                'growth_rate'          => 'Fast',
            ],
            // Gardening Essentials
            [
                'category_id'          => $essentials->id,
                'common_name'          => 'Premium Potting Mix',
                'botanical_name'       => 'N/A',
                'description'          => 'A rich, well-draining potting mix suitable for most indoor and outdoor plants. 5kg bag.',
                'size'                 => 'large',
                'price'                => 550.00,
                'stock_quantity'       => 100,
                'sunlight_requirement' => 'N/A',
                'watering_frequency'   => 'N/A',
                'soil_recommendation'  => 'N/A',
                'temperature_min_c'    => null,
                'temperature_max_c'    => null,
                'is_low_maintenance'   => true,
                'is_pet_friendly'      => true,
                'growth_rate'          => null,
            ],
            [
                'category_id'          => $essentials->id,
                'common_name'          => 'Terracotta Pot (6 inch)',
                'botanical_name'       => 'N/A',
                'description'          => 'Classic terracotta pot with drainage hole. Ideal for succulents and herbs.',
                'size'                 => 'small',
                'price'                => 180.00,
                'stock_quantity'       => 150,
                'sunlight_requirement' => 'N/A',
                'watering_frequency'   => 'N/A',
                'soil_recommendation'  => 'N/A',
                'temperature_min_c'    => null,
                'temperature_max_c'    => null,
                'is_low_maintenance'   => true,
                'is_pet_friendly'      => true,
                'growth_rate'          => null,
            ],
            [
                'category_id'          => $essentials->id,
                'common_name'          => 'Organic Fertilizer',
                'botanical_name'       => 'N/A',
                'description'          => 'Slow-release organic fertilizer pellets. Feeds plants for up to 3 months. 1kg pack.',
                'size'                 => 'medium',
                'price'                => 420.00,
                'stock_quantity'       => 75,
                'sunlight_requirement' => 'N/A',
                'watering_frequency'   => 'N/A',
                'soil_recommendation'  => 'N/A',
                'temperature_min_c'    => null,
                'temperature_max_c'    => null,
                'is_low_maintenance'   => true,
                'is_pet_friendly'      => true,
                'growth_rate'          => null,
            ],
        ];

        foreach ($products as $data) {
            Product::firstOrCreate(
                ['common_name' => $data['common_name'], 'botanical_name' => $data['botanical_name']],
                $data
            );
        }

        // Set up some "Frequently Bought With" relationships
        $aloe      = Product::where('common_name', 'Aloe Vera')->first();
        $pot       = Product::where('common_name', 'Terracotta Pot (6 inch)')->first();
        $fertilizer = Product::where('common_name', 'Organic Fertilizer')->first();
        $soil      = Product::where('common_name', 'Premium Potting Mix')->first();
        $echeveria = Product::where('common_name', 'Echeveria')->first();
        $snakePlant = Product::where('common_name', 'Snake Plant')->first();

        if ($aloe && $pot)        $aloe->relatedProducts()->syncWithoutDetaching([$pot->id]);
        if ($aloe && $soil)       $aloe->relatedProducts()->syncWithoutDetaching([$soil->id]);
        if ($echeveria && $pot)   $echeveria->relatedProducts()->syncWithoutDetaching([$pot->id]);
        if ($snakePlant && $fertilizer) $snakePlant->relatedProducts()->syncWithoutDetaching([$fertilizer->id]);
        if ($snakePlant && $soil) $snakePlant->relatedProducts()->syncWithoutDetaching([$soil->id]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SettingsSeeder::class);

        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@foodlay.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Categories
        $categories = [
            ['name' => 'Set Menu', 'name_de' => 'Menüs', 'icon' => '🍱', 'sort_order' => 1],
            ['name' => 'Burgers', 'name_de' => 'Burger', 'icon' => '🍔', 'sort_order' => 2],
            ['name' => 'Pizza', 'name_de' => 'Pizza', 'icon' => '🍕', 'sort_order' => 3],
            ['name' => 'Kebab', 'name_de' => 'Kebab', 'icon' => '🥙', 'sort_order' => 4],
            ['name' => 'Chicken', 'name_de' => 'Hähnchen', 'icon' => '🍗', 'sort_order' => 5],
            ['name' => 'Pasta', 'name_de' => 'Pasta', 'icon' => '🍝', 'sort_order' => 6],
            ['name' => 'Salads', 'name_de' => 'Salate', 'icon' => '🥗', 'sort_order' => 7],
            ['name' => 'Drinks', 'name_de' => 'Getränke', 'icon' => '🥤', 'sort_order' => 8],
            ['name' => 'Desserts', 'name_de' => 'Desserts', 'icon' => '🍰', 'sort_order' => 9],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Get category IDs
        $burgerCategory = Category::where('name', 'Burgers')->first();
        $pizzaCategory = Category::where('name', 'Pizza')->first();
        $chickenCategory = Category::where('name', 'Chicken')->first();
        $drinksCategory = Category::where('name', 'Drinks')->first();
        $setMenuCategory = Category::where('name', 'Set Menu')->first();

        // Create Products - Burgers
        $this->createBurger($burgerCategory, 'Double Steak Cheese Burger', 'Doppel-Steak-Käse-Burger',
            'Juicy double beef patty with melted cheese, lettuce, tomato, pickles, and our special sauce.',
            'Saftiges Doppel-Rindfleisch mit geschmolzenem Käse, Salat, Tomate, Gurken und unserer Spezialsauce.',
            149.99, 189.99, true, true, ['popular', 'new']);

        $this->createBurger($burgerCategory, 'Classic Beef Burger', 'Klassischer Beef-Burger',
            'Classic beef burger with fresh vegetables and special sauce.',
            'Klassischer Beef-Burger mit frischem Gemüse und Spezialsauce.',
            89.99, null, true, false, ['popular']);

        $this->createBurger($burgerCategory, 'Crispy Chicken Burger', 'Knuspriger Chicken-Burger',
            'Crispy fried chicken breast with coleslaw and mayo.',
            'Knuspriges Hähnchenbrustfilet mit Krautsalat und Mayo.',
            79.99, null, true, false, []);

        $this->createBurger($burgerCategory, 'Mushroom Swiss Burger', 'Champignon-Swiss-Burger',
            'Beef patty topped with sautéed mushrooms and Swiss cheese.',
            'Rindfleisch mit gebratenen Champignons und Schweizer Käse.',
            109.99, null, true, false, ['new']);

        // Create Products - Pizza
        $this->createPizza($pizzaCategory, 'Margherita Pizza', 'Pizza Margherita',
            'Classic tomato sauce, fresh mozzarella, and basil.',
            'Klassische Tomatensauce, frische Mozzarella und Basilikum.',
            119.99, null, true, false, []);

        $this->createPizza($pizzaCategory, 'Pepperoni Supreme', 'Pepperoni Supreme',
            'Loaded with pepperoni, extra cheese, and Italian herbs.',
            'Reichlich Pepperoni, extra Käse und italienische Kräuter.',
            149.99, 179.99, true, true, ['popular', 'new']);

        $this->createPizza($pizzaCategory, 'BBQ Chicken Pizza', 'BBQ-Chicken-Pizza',
            'Grilled chicken, BBQ sauce, onions, and cilantro.',
            'Gegrilltes Hähnchen, BBQ-Sauce, Zwiebeln und Koriander.',
            139.99, null, true, false, []);

        // Create Products - Chicken
        $this->createChicken($chickenCategory, 'Fried Chicken Bucket', 'Knuspriger Hähnchen-Bucket',
            '8 pieces of crispy fried chicken with fries and coleslaw.',
            '8 Stück knuspriges Brathähnchen mit Pommes und Krautsalat.',
            199.99, 249.99, true, true, ['popular']);

        $this->createChicken($chickenCategory, 'Grilled Chicken Platter', 'Gegrilltes Hähnchen',
            'Grilled chicken with rice, grilled vegetables, and garlic sauce.',
            'Gegrilltes Hähnchen mit Reis, Grillgemüse und Knoblauchsauce.',
            159.99, null, true, false, []);

        // Create Products - Drinks
        $this->createDrink($drinksCategory, 'Fresh Orange Juice', 'Frischer Orangensaft', 29.99, null);
        $this->createDrink($drinksCategory, 'Mango Smoothie', 'Mango-Smoothie', 39.99, null);
        $this->createDrink($drinksCategory, 'Coca Cola', 'Coca Cola', 15.99, null);
        $this->createDrink($drinksCategory, 'Mineral Water', 'Mineralwasser', 9.99, null);

        // Create Products - Set Menu
        $this->createSetMenu($setMenuCategory, 'Family Feast', 'Familienmenü',
            '2 large burgers, 1 large pizza, 8pc chicken, fries, and 4 drinks.',
            '2 große Burger, 1 große Pizza, 8 Stück Hähnchen, Pommes und 4 Getränke.',
            449.99, 599.99, true, true, ['popular', 'new']);

        $this->createSetMenu($setMenuCategory, 'Combo Meal', 'Combo-Menü',
            'Burger or chicken sandwich with fries and drink.',
            'Burger oder Chicken-Sandwich mit Pommes und Getränk.',
            129.99, null, true, false, []);
    }

    private function createBurger($category, $name, $nameDe, $desc, $descDe, $price, $oldPrice, $available, $featured, $tags)
    {
        $product = Product::create([
            'category_id' => $category->id,
            'name' => $name,
            'name_de' => $nameDe,
            'description' => $desc,
            'description_de' => $descDe,
            'base_price' => $price,
            'old_price' => $oldPrice,
            'is_available' => $available,
            'is_featured' => $featured,
            'tags' => $tags,
        ]);

        // Add sizes
        $product->sizes()->createMany([
            ['name' => 'Small', 'name_de' => 'Klein', 'price_modifier' => 0, 'sort_order' => 1],
            ['name' => 'Medium', 'name_de' => 'Mittel', 'price_modifier' => 20, 'sort_order' => 2],
            ['name' => 'Large', 'name_de' => 'Groß', 'price_modifier' => 40, 'sort_order' => 3],
        ]);

        // Add toppings
        $product->toppings()->createMany([
            ['name' => 'Extra Cheese', 'name_de' => 'Extra Käse', 'price' => 15, 'is_required' => false],
            ['name' => 'Bacon', 'name_de' => 'Speck', 'price' => 20, 'is_required' => false],
            ['name' => 'Jalapeños', 'name_de' => 'Jalapeños', 'price' => 10, 'is_required' => false],
            ['name' => 'Mushrooms', 'name_de' => 'Champignons', 'price' => 12, 'is_required' => false],
        ]);
    }

    private function createPizza($category, $name, $nameDe, $desc, $descDe, $price, $oldPrice, $available, $featured, $tags)
    {
        $product = Product::create([
            'category_id' => $category->id,
            'name' => $name,
            'name_de' => $nameDe,
            'description' => $desc,
            'description_de' => $descDe,
            'base_price' => $price,
            'old_price' => $oldPrice,
            'is_available' => $available,
            'is_featured' => $featured,
            'tags' => $tags,
        ]);

        // Add sizes
        $product->sizes()->createMany([
            ['name' => 'Small (6")', 'name_de' => 'Klein', 'price_modifier' => 0, 'sort_order' => 1],
            ['name' => 'Medium (9")', 'name_de' => 'Mittel', 'price_modifier' => 40, 'sort_order' => 2],
            ['name' => 'Large (12")', 'name_de' => 'Groß', 'price_modifier' => 70, 'sort_order' => 3],
            ['name' => 'Family (15")', 'name_de' => 'Familie', 'price_modifier' => 100, 'sort_order' => 4],
        ]);

        // Add toppings
        $product->toppings()->createMany([
            ['name' => 'Extra Cheese', 'name_de' => 'Extra Käse', 'price' => 20, 'is_required' => false],
            ['name' => 'Pepperoni', 'name_de' => 'Pepperoni', 'price' => 25, 'is_required' => false],
            ['name' => 'Olives', 'name_de' => 'Oliven', 'price' => 15, 'is_required' => false],
            ['name' => 'Bell Peppers', 'name_de' => 'Paprika', 'price' => 12, 'is_required' => false],
        ]);
    }

    private function createChicken($category, $name, $nameDe, $desc, $descDe, $price, $oldPrice, $available, $featured, $tags)
    {
        $product = Product::create([
            'category_id' => $category->id,
            'name' => $name,
            'name_de' => $nameDe,
            'description' => $desc,
            'description_de' => $descDe,
            'base_price' => $price,
            'old_price' => $oldPrice,
            'is_available' => $available,
            'is_featured' => $featured,
            'tags' => $tags,
        ]);

        // Add sizes for bucket
        if (str_contains($name, 'Bucket')) {
            $product->sizes()->createMany([
                ['name' => '4 Pieces', 'name_de' => '4 Stück', 'price_modifier' => -60, 'sort_order' => 1],
                ['name' => '8 Pieces', 'name_de' => '8 Stück', 'price_modifier' => 0, 'sort_order' => 2],
                ['name' => '12 Pieces', 'name_de' => '12 Stück', 'price_modifier' => 80, 'sort_order' => 3],
            ]);
        }
    }

    private function createDrink($category, $name, $nameDe, $price, $oldPrice)
    {
        $product = Product::create([
            'category_id' => $category->id,
            'name' => $name,
            'name_de' => $nameDe,
            'description' => null,
            'base_price' => $price,
            'old_price' => $oldPrice,
            'is_available' => true,
            'is_featured' => false,
            'tags' => [],
        ]);

        // Add sizes for drinks (except water)
        if (!str_contains($name, 'Water')) {
            $product->sizes()->createMany([
                ['name' => 'Regular', 'name_de' => 'Normal', 'price_modifier' => 0, 'sort_order' => 1],
                ['name' => 'Large', 'name_de' => 'Groß', 'price_modifier' => 10, 'sort_order' => 2],
            ]);
        }
    }

    private function createSetMenu($category, $name, $nameDe, $desc, $descDe, $price, $oldPrice, $available, $featured, $tags)
    {
        Product::create([
            'category_id' => $category->id,
            'name' => $name,
            'name_de' => $nameDe,
            'description' => $desc,
            'description_de' => $descDe,
            'base_price' => $price,
            'old_price' => $oldPrice,
            'is_available' => $available,
            'is_featured' => $featured,
            'tags' => $tags,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * A grocery catalogue the factory draws realistic products from.
     *
     * Items are grouped by store aisle. Each group knows the packaging
     * variants it sells and its typical best-before range in days, so
     * generated expiration dates match reality (yogurt expires in days,
     * rice in months, detergent in years).
     *
     * @var array<string, array{items: list<string>, packs: list<string>, shelfLife: array{0: int, 1: int}}>
     */
    private const CATALOGUE = [
        'Dairy & Eggs' => [
            'items' => ['Whole Milk', 'Greek Yogurt', 'Cheddar Block', 'Butter', 'Cottage Cheese', 'Sour Cream', 'Free Range Eggs', 'Brie'],
            'packs' => ['1 L', '500 ml', '400 g', '250 g', '6 pcs'],
            'shelfLife' => [4, 30],
        ],
        'Bakery' => [
            'items' => ['Rye Bread', 'Baguette', 'Cinnamon Buns', 'Bagels', 'Multigrain Loaf'],
            'packs' => ['1 pc', '6 pcs', '400 g', '700 g'],
            'shelfLife' => [2, 7],
        ],
        'Fruits & Vegetables' => [
            'items' => ['Tomatoes on the Vine', 'Avocados', 'Bananas', 'Baby Spinach', 'Bell Peppers', 'Cucumbers', 'Blueberries'],
            'packs' => ['1 kg', '500 g', '250 g', '3 pcs', '1 pc'],
            'shelfLife' => [3, 14],
        ],
        'Meat & Fish' => [
            'items' => ['Chicken Breast Fillets', 'Pork Loin Chops', 'Salmon Fillet', 'Beef Mince', 'Smoked Ham', 'Frozen Shrimp'],
            'packs' => ['500 g', '1 kg', '300 g', '200 g'],
            'shelfLife' => [1, 21],
        ],
        'Pantry' => [
            'items' => ['Basmati Rice', 'Spaghetti', 'Olive Oil', 'Tomato Passata', 'Chickpeas in Brine', 'Rolled Oats', 'Peanut Butter', 'Honey'],
            'packs' => ['1 kg', '500 g', '750 ml', '400 g', '1 L'],
            'shelfLife' => [180, 900],
        ],
        'Drinks' => [
            'items' => ['Sparkling Water', 'Cola', 'Orange Juice', 'Green Tea', 'Ground Coffee', 'Apple Juice'],
            'packs' => ['1.5 L', '500 ml', '330 ml', '250 g'],
            'shelfLife' => [45, 540],
        ],
        'Snacks & Sweets' => [
            'items' => ['Dark Chocolate Bar', 'Salted Pretzels', 'Trail Mix', 'Vanilla Cookies', 'Gummy Bears', 'Popcorn'],
            'packs' => ['100 g', '150 g', '200 g', '250 g'],
            'shelfLife' => [60, 365],
        ],
        'Household' => [
            'items' => ['Dish Soap', 'Laundry Detergent', 'Paper Towels', 'Trash Bags', 'Toothpaste'],
            'packs' => ['500 ml', '2 L', '4 rolls', '30 pcs'],
            'shelfLife' => [540, 1095],
        ],
    ];

    /**
     * Statuses weighted like a healthy catalogue: most items are on
     * sale, a small slice is inactive or discontinued.
     *
     * @var list<ProductStatus>
     */
    private const WEIGHTED_STATUSES = [
        ProductStatus::Active,
        ProductStatus::Active,
        ProductStatus::Active,
        ProductStatus::Active,
        ProductStatus::Active,
        ProductStatus::Active,
        ProductStatus::Inactive,
        ProductStatus::Inactive,
        ProductStatus::Discontinued,
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = fake()->unique()->randomElement(static::shelfProducts());

        [$minShelfLife, $maxShelfLife] = $product['shelfLife'];

        return [
            // Item + packaging reads like a real shelf price tag and
            // stays unique across a large seed run.
            'name' => $product['name'],
            'description' => static::description($product['name'], $product['aisle']),
            'expiration_date' => fake()->dateTimeBetween('today', sprintf('+%d days', fake()->numberBetween($minShelfLife, $maxShelfLife)))->format('Y-m-d'),
            'status' => fake()->randomElement(self::WEIGHTED_STATUSES),
            'quantity' => fake()->numberBetween(0, 250),
        ];
    }

    /**
     * Every shoppable shelf product in the catalogue, with the aisle
     * data needed to write realistic details about it.
     *
     * The list is memoized so the unique() pool stays stable between
     * factory calls, and it rates item times pack variants (~230
     * combinations), so uniqueness never runs dry on big seed runs.
     *
     * @return list<array{name: string, aisle: string, shelfLife: array{0: int, 1: int}}>
     */
    private static function shelfProducts(): array
    {
        static $products = null;

        if ($products === null) {
            $products = [];

            foreach (self::CATALOGUE as $aisle => $group) {
                foreach ($group['items'] as $item) {
                    foreach ($group['packs'] as $pack) {
                        $products[] = [
                            'name' => $item.' '.$pack,
                            'aisle' => $aisle,
                            'shelfLife' => $group['shelfLife'],
                        ];
                    }
                }
            }
        }

        return $products;
    }

    /**
     * Products that are currently listed for sale.
     */
    public function active(): static
    {
        return $this->state(fn (): array => ['status' => ProductStatus::Active]);
    }

    /**
     * Products temporarily delisted from the catalogue.
     */
    public function inactive(): static
    {
        return $this->state(fn (): array => ['status' => ProductStatus::Inactive]);
    }

    /**
     * Products withdrawn from sale.
     */
    public function discontinued(): static
    {
        return $this->state(fn (): array => ['status' => ProductStatus::Discontinued]);
    }

    /**
     * Products still on the shelf but about to pass their best-before date.
     */
    public function nearlyExpired(): static
    {
        return $this->state(fn (): array => [
            'expiration_date' => fake()->dateTimeBetween('today', '+5 days')->format('Y-m-d'),
        ]);
    }

    /**
     * Products already past their best-before date.
     */
    public function expired(): static
    {
        return $this->state(fn (): array => [
            'expiration_date' => fake()->dateTimeBetween('-10 days', '-1 day')->format('Y-m-d'),
        ]);
    }

    /**
     * Products with an empty stock level.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (): array => ['quantity' => 0]);
    }

    /**
     * Plausible catalogue copy for the given product.
     */
    private static function description(string $item, string $aisle): string
    {
        return fake()->randomElement([
            sprintf('%s from the %s aisle. %s', $item, $aisle, fake()->sentence()),
            sprintf('%s. Sold per shelf unit, best stored away from direct sunlight. %s', $item, fake()->sentence()),
            sprintf('%s. A customer favourite with a %s star rating. %s', $item, fake()->randomDigitNotNull(), fake()->sentence()),
            sprintf('%s. Applies to a limited batch promotion. %s', $item, fake()->sentence()),
        ]);
    }
}

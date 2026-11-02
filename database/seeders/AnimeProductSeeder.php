<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Database\Seeder;

class AnimeProductSeeder extends Seeder
{
    /**
     * Seed a small catalogue of stock-control demo products
     * referencing popular anime series and their characters.
     *
     * Tags are attached through the same model method the tag editor
     * uses, so the demo data exercises the very same code path.
     */
    public function run(): void
    {
        $this->create(
            'Genya Shinazugawa Action Figure',
            'Highly detailed figure of the Wind-breathing disciple. Comes with his Nichirin blade and removable shotgun. Windquarters-proof packaging.',
            quantity: 12,
            status: ProductStatus::Active,
            tags: ['demon slayer', 'genya shinazugawa', 'figures'],
        );

        $this->create(
            "Genya's Shotgun Charm Keychain",
            'Miniature Nichirin shotgun charm for keys or bags. Combine it with the brotherly Windhashira approved look.',
            quantity: 25,
            status: ProductStatus::Active,
            tags: ['demon slayer', 'genya shinazugawa', 'keychains'],
        );

        $this->create(
            'Tomura Shigaraki Limited Bust',
            'The Decay quirk incarnate: a hand-plastered bust of the League of Villains leader. Ten fingers, zero warranty. Caution: chipping hazard not detected.',
            quantity: 8,
            status: ProductStatus::Active,
            tags: ['my hero academia', 'tomura shigaraki', 'figures'],
        );

        $this->create(
            'League of Villains Trading Card Set',
            'Rare card game set featuring Shigaraki, Dabi and the rest of the League. Ships in a Hero Public Safety Commission sealed sleeve.',
            quantity: 0,
            status: ProductStatus::Inactive,
            tags: ['my hero academia', 'tomura shigaraki', 'trading cards'],
        );

        $this->create(
            "Deku's Hero Analysis Notebook Replica",
            'Volume 13 edition of the famous green notebook. Contains zero burn marks… probably.',
            quantity: 15,
            status: ProductStatus::Active,
            tags: ['my hero academia', 'stationery'],
        );

        $this->create(
            "Sanemi's Ohagi Sweet Box",
            'Traditional sweet red-bean rice cakes, the Wind Hashira weakness. Sharing them with Genya is strongly encouraged. Perishable: best within weeks.',
            quantity: 20,
            status: ProductStatus::Active,
            expirationInWeeks: 3,
            tags: ['demon slayer', 'sweets', 'perishable'],
        );

        $this->create(
            'All Might "I Am Here" Poster',
            'Silver Age All Might pointing straight at your dorm wall. Beyond this posters power is nothing but Plus Ultra vibes.',
            quantity: 40,
            status: ProductStatus::Active,
            tags: ['my hero academia', 'posters'],
        );
    }

    /**
     * Create one product and label it with the given tags.
     *
     * @param  list<string>  $tags
     */
    private function create(
        string $name,
        string $description,
        ?int $quantity = null,
        ?ProductStatus $status = null,
        ?int $expirationInWeeks = null,
        array $tags = [],
    ): Product {
        $product = Product::create([
            'name' => $name,
            'description' => $description,
            'quantity' => $quantity ?? 0,
            'status' => $status ?? ProductStatus::Active,
            'expiration_date' => $expirationInWeeks === null ? null : today()->addWeeks($expirationInWeeks),
        ]);

        $product->syncTags($tags);

        return $product;
    }
}

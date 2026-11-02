<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use InvalidArgumentException;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'expiration_date',
        'status',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expiration_date' => 'date',
        'status' => ProductStatus::class,
        'quantity' => 'integer',
    ];

    /**
     * The tags labelling this product.
     *
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags()->orderBy('name')->get();
    }

    /**
     * Attach exactly the named tags to the product.
     *
     * Unknown names are created on the fly, extra whitespace is
     * trimmed, duplicates are collapsed, and blank names are skipped.
     * Tags that were on the product before but are not in the list
     * are detached (the pivot mirrors the submitted list).
     *
     * @param  list<string|int>  $names
     */
    public function syncTags(array $names): static
    {
        $tagIds = collect($names)
            ->map(fn ($name) => trim((string) $name))
            ->filter(fn ($name) => $name !== '')
            ->unique()
            ->map(fn ($name) => Tag::firstOrCreate(['name' => $name])->id);

        $this->tags()->sync($tagIds->all());

        return $this;
    }

    /**
     * Register a delivery: the given amount arrives in the store.
     */
    public function increaseQuantity(int $amount = 1): static
    {
        return $this->changeQuantity($amount);
    }

    /**
     * Register a purchase: the given amount leaves the store.
     */
    public function decreaseQuantity(int $amount = 1): static
    {
        return $this->changeQuantity(-$amount);
    }

    /**
     * Adjust the stored quantity by the given difference and persist it.
     *
     * A positive difference models a delivery (stock arriving), a
     * negative one a purchase (stock leaving the store). The
     * intent-carrying methods above are the public API; this is the
     * single piece of arithmetic both of them share.
     *
     * @throws InvalidArgumentException when the change would drop the quantity below zero
     */
    public function changeQuantity(int $difference): static
    {
        $newQuantity = $this->quantity + $difference;

        if ($newQuantity < 0) {
            throw new InvalidArgumentException('Quantity cannot go below zero.');
        }

        $this->quantity = $newQuantity;
        $this->save();

        return $this;
    }
}

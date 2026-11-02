<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
     * Adjust the stored quantity by the given difference and persist it.
     *
     * A positive difference models a delivery (stock arriving), a
     * negative one a purchase (stock leaving the store).
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
}

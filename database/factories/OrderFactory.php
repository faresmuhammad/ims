<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference_code' => randomCode(),
            'user_id' => User::first()->id,
            'supplier_id' => null,
            'date' => now(),
            'total_amount' => 0,
            'discount' => 0,
            'completed' => false
        ];
    }

    public function withSupplier()
    {
        return $this->state(new Sequence(fn(Sequence $sequence) => [
            'supplier_id' => Supplier::all()->random()
        ]));
    }

    public function randomTotalAmount()
    {
        return $this->state(new Sequence(fn(Sequence $sequence) => [
            'total_amount' => rand(20, 2000)
        ]));
    }

    public function randomComplete()
    {
        return $this->state(fn(array $attributes) => [
            'completed' => $this->faker->boolean()
        ]);
    }

    public function completed()
    {
        return $this->state(fn(array $attributes) => [
            'completed' => true
        ]);
    }

    public function createdAt(Carbon $date)
    {
        return $this->state(fn(array $attributes) => [
            'created_at' => $date,
            'updated_at' => $date
        ]);
    }

    public function sequenceCreatedFrom(Carbon $date)
    {
        return $this->state(new Sequence(fn(Sequence $sequence) => [
            'created_at' => $date->addMinutes(30),
            'updated_at' => $date->addMinutes(30)
        ]));
    }




}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }


    public function scopeCompleted(Builder $query)
    {
        $query->where('completed', true);
    }

    protected static function boot()
    {
        parent::boot();
        static::updating(function (Order $order) {
            $order->total_amount = $order->items->sum('total_amount');
        });
    }
}

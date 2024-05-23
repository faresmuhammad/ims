<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getTypeOfValueAttribute()
    {
        $columns = ['value_integer', 'value_date', 'value_start_date'];

        foreach ($columns as $column) {
            if (!is_null($this->$column)) {
                return $column;
            }
        }
        return null;
    }
}

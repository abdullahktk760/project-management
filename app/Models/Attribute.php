<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['name', 'type'];

    // An attribute can have many AttributeValues (for various projects)
    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class);
    }

    protected static function booted()
    {
        static::deleting(function ($attribute) {
            $attribute->attributeValues()->delete();
        });
    }
}

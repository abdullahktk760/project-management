<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'status'];
    // Each project can have many users.
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    // A project has many timesheets.
    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }

    // EAV relationship: get dynamic attribute values.
    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class, 'entity_id');
    }


    public function attributes()
    {
        return $this->hasManyThrough(
            Attribute::class,
            AttributeValue::class,
            'entity_id',      // Foreign key on attribute_values that references projects.
            'id',             // Primary key on attributes (used to match with attribute_values.attribute_id).
            'id',             // Local key on projects.
            'attribute_id'    // Foreign key on attribute_values that references attributes.
        );
    }
}

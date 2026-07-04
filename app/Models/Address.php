<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['user_id', 'label', 'street', 'city', 'postal_code'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toSnapshot(): array
    {
        return [
            'label'       => $this->label,
            'street'      => $this->street,
            'city'        => $this->city,
            'postal_code' => $this->postal_code,
        ];
    }
}

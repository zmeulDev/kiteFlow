<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'contact_person',
        'contact_person_email',
        'contact_person_phone',
        'is_active',
        'contract_start_date',
        'contract_end_date',
        'main_contact_user_id',
        'parent_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'contract_start_date' => 'date',
            'contract_end_date' => 'date',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(Company::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Company::class, 'parent_id');
    }

    public function allChildrenIds(): array
    {
        return $this->children()->pluck('id')->toArray();
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(Visitor::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function mainContact()
    {
        return $this->belongsTo(User::class, 'main_contact_user_id');
    }
}
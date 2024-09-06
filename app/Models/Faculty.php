<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

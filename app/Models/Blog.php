<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'manager_id',
        'category_id'
    ];

    public function blogFiles()
    {
        return $this->hasMany(BlogFile::class);
    }
}

<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaPesan extends Model
{
    use HasFactory;
    // protected $fillable = ['user_id', 'pesan', 'judul'];
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function proses()
    {
        return $this->hasMany(Proses::class);
    }
}

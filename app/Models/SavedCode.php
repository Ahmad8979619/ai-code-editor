<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedCode extends Model
{

    protected $fillable = [

        'user_id',
        'code',
        'language'

    ];

    // ✅ ت علاقة مع المستخدL
    public function user(): BelongsTo
    {

        return $this->belongsTo(User::class);

    }

}

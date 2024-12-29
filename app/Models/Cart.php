<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{

    protected $fillable  = ["session_id", "user_id", "product_id", "price", "quantity"];

    public static function flush()
    {
        return self::where(["session_id" => session()->getId()])->delete();
    }


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}

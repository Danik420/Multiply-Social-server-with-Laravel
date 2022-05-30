<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    public static $rules =
        [
            'user_id',
            'user_name',
            'profile_image_url',
            'title' => 'required',
            'body' => 'required',
            'thumbnail',
            'created_at',
            'updated_at'
        ];

    protected $guarded = [];
//    protected $fillable = ['title', 'body', 'thumnail'];
//    둘 중에 하나만 써놓으면 됨 guarded는 이것만 금할거야(공백인 경우 전부 허용) fillable은 이것만 허용할 거야(공백일 경우 전부 금지)

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}

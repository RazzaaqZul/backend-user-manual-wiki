<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Mail\Mailables\Content;

class UserManualHistory extends Model
{
    use HasFactory;

    protected $table = "user_manual_histories";
    protected $primaryKey = 'user_manual_history_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'title',
        'img',
        'short_desc',
        'initial_editor',
        'latest_editor',
        'version',
        'content',
        'category',
        'size',
        'user_manual_id'
    ];

    /**
     * Relationships
     */

    public function UserManual() : BelongsTo
    {
        return $this->belongsTo(UserManual::class,'user_manual_id', 'user_manual_id');
    }
}

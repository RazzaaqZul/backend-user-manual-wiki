<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserManual extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_manuals';
    protected $primaryKey = 'user_manual_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $dates = ['deleted_at'];

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
        'user_id'
    ];

    /**
     * Relationships
     */

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class,'user_id', 'user_id');
    }

    public function userManualHistories() : HasMany 
    {
        return $this->hasMany(UserManualHistory::class);
    }

}
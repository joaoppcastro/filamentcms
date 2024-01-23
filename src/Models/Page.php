<?php

namespace JoaoCastro\FilamentCms\Models;

use App\Models\Channel;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasFactory;
    use HasTranslations;

    protected static function booted(): void
    {
        static::addGlobalScope('team', function (Builder $query) {
            if (auth()->check()) {
                if (auth()->guard('web')->check()) {
                    $query->where(config('filament-navigation.teamsId'), Filament::getTenant()->getAttribute('id'));
                } else {

                }
            }
        });
    }

    public static array $allowedFields = [
        'title',
    ];

    public static array $allowedSorts = [
        'title',
        'created_at',
    ];

    public static array $allowedFilters = [
        'title',
    ];

    protected $fillable = [
        'channel_id',
        'title',
        'content',
        'slug',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'is_active',
    ];

    public array $translatable = [
        'title',
        'content',
        'slug',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'is_active',
    ];

    protected $casts = [
        'title' => 'array',
        'content' => 'array',
        'slug' => 'array',
        'meta_title' => 'array',
        'meta_keywords' => 'array',
        'meta_description' => 'array',
        'is_active',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}

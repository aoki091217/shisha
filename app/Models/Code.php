<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Code extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const CODE_ID = 'code_id';
    public const SHOP_ID = 'shop_id';
    public const SITUATION_ID = 'situation_id';
    public const NAME = 'name';
    public const HASH = 'hash';
    public const PARAMETER = 'parameter';
    public const KIND = 'kind';
    public const SCRIPT = 'script';
    public const NOTES = 'notes';
    public const CREATED_AT = 'created_at';

    public const RELATION_SHOP = 'shop';
    public const RELATION_SITUATION = 'situation';

    public const KIND_ROUTE = 'route';
    public const KIND_CHECKIN = 'checkin';

    public const KIND_VALUES = [
        self::KIND_ROUTE => 1,
        self::KIND_CHECKIN => 2
    ];

    public const KIND_LABELS = [
        self::KIND_ROUTE => '流入経路計測',
        self::KIND_CHECKIN => 'チェックイン'
    ];

    protected $primaryKey = self::CODE_ID;

    protected $fillable = [
        self::SHOP_ID,
        self::SITUATION_ID,
        self::NAME,
        self::HASH,
        self::PARAMETER,
        self::KIND,
        self::SCRIPT,
        self::NOTES
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id')->withTrashed();
    }

    public function situation()
    {
        return $this->belongsTo(Situation::class, 'situation_id');
    }

    public function getCodeId(): int
    {
        return $this->{self::CODE_ID};
    }

    public function getShopId(): int
    {
        return $this->{self::SHOP_ID};
    }

    public function getSituationId(): int
    {
        return $this->{self::SITUATION_ID};
    }

    public function getName(): string
    {
        return $this->{self::NAME};
    }

    public function getHash(): string
    {
        return $this->{self::HASH};
    }

    public function getParameter(): string
    {
        return $this->{self::PARAMETER};
    }

    public function getKind(): int
    {
        return $this->{self::KIND};
    }

    public function getScript(): string
    {
        return $this->{self::SCRIPT};
    }

    public function getNotes(): string
    {
        return $this->{self::NOTES};
    }

    public function getCreatedAt(): string
    {
        return Carbon::parse($this->{self::CREATED_AT})->format('Y年m月d日 H時i分s秒');
    }

    public function scopeSearch(Builder $query, $keywords)
    {
        if (isset($keywords[self::NAME])) {
            $query->where(self::NAME, 'LIKE', "%{$keywords[self::NAME]}%");
        }

        if (isset($keywords[self::SHOP_ID])) {
            $query->whereHas(self::RELATION_SITUATION, function ($query) use ($keywords) {
                $query->where(Shop::SHOP_ID, $keywords[self::SHOP_ID]);
            });
        }
    }

    public static function getKindValue(string $kind): int
    {
        return self::KIND_VALUES[$kind];
    }

    public static function getKindLabels(): array
    {
        return self::KIND_LABELS;
    }
}

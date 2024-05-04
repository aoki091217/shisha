<?php

namespace App\Models\External;

use App\Models\CustomerShop;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @property-read string $line_id
 * @property-read string $line_name
 * @property-read string|null $picture
 * @property-read bool|null $is_friend
 * @property-read bool|null $is_liff_active
 */
class LineUser implements Arrayable
{
    private array $attributes;

    public function __construct(
        string $line_id,
        string $line_name,
        ?string $picture,
        ?bool $is_friend = null,
        ?bool $is_liff_active = null,
    ) {
        $this->attributes = [
            'line_id' => $line_id,
            'line_name' => $line_name,
            'picture' => $picture,
            'is_friend' => $is_friend,
            'is_liff_active' => $is_liff_active,
        ];
    }

    public function __get(string $name)
    {
        return $this->attributes[$name] ?? throw new \InvalidArgumentException("unknown attribute: {$name}");
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function getFriendStatus()
    {
        if (is_null($this->is_friend)) {
            return CustomerShop::LIFF_STATUS_UNKNOWN;
        }
        return $this->is_friend ? CustomerShop::FRIEND_STATUS_FOLLOWED : CustomerShop::FRIEND_STATUS_UNFOLLOWED;
    }

    /**
     * @return string
     */
    public function getLiffStatus()
    {
        if (is_null($this->is_liff_active)) {
            return CustomerShop::LIFF_STATUS_UNKNOWN;
        }
        return $this->is_liff_active ? CustomerShop::LIFF_STATUS_ACTIVE : CustomerShop::LIFF_STATUS_INACTIVE;
    }
}

<?php

namespace App\Services;

class SituationService
{
    public const TYPE_FOLLOW = 'follow';
    public const TYPE_REPLY = 'reply';
    public const TYPE_QUESTION = 'question';
    public const TYPE_BILL = 'bill';
    public const TYPE_PUSH = 'push';

    private $values = [
        self::TYPE_FOLLOW => 1,
        self::TYPE_REPLY => 2,
        self::TYPE_QUESTION => 3,
        self::TYPE_BILL => 4,
        self::TYPE_PUSH => 5,
    ];

    public function getSituationType(string $type): array
    {
        switch ($type) {
            case self::TYPE_REPLY:
                return [
                    $this->values[self::TYPE_REPLY],
                    $this->values[self::TYPE_BILL]
                ];
            default:
                return [$this->values[$type]];
        }
    }
}

?>

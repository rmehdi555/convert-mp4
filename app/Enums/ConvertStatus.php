<?php

namespace App\Enums;

enum ConvertStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::PENDING => 'bg-secondary',
            self::PROCESSING => 'bg-warning',
            self::COMPLETED => 'bg-success',
            self::FAILED => 'bg-danger',
        };
    }
}

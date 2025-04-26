<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SearchPerformed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $query,
        public string $type,
        public float $executionTime
    ) {}
} 
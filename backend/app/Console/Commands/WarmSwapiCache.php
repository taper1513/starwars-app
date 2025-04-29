<?php

namespace App\Console\Commands;

use App\Services\SwapiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WarmSwapiCache extends Command
{
    protected $signature = 'swapi:warm-cache';
    protected $description = 'Warm the cache with SWAPI people and movies';

    private $swapiService;

    public function __construct(SwapiService $swapiService)
    {
        parent::__construct();
        $this->swapiService = $swapiService;
    }

    public function handle()
    {
        $this->info('Starting SWAPI cache warming...');

        // First, get all movies and cache them with full data
        $this->info('Fetching and caching all movies...');
        for ($i = 1; $i <= 6; $i++) {
            $movie = $this->swapiService->getMovie((string)$i, false);
            if ($movie && $movie['title'] !== 'Unknown') {
                $this->info("Cached movie {$i}: {$movie['title']}");
            }
            usleep(100000); // Add delay to respect API rate limits
        }

        // Then get all people and cache them with full data
        $this->info('Fetching and caching all people...');
        for ($i = 1; $i <= 83; $i++) {
            $person = $this->swapiService->getPerson((string)$i, false);
            if ($person && $person['name'] !== 'Unknown') {
                $this->info("Cached person {$i}: {$person['name']}");
            }
            usleep(100000); // Add delay to respect API rate limits
        }

        $this->info('SWAPI cache warming complete!');
    }
} 
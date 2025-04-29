<?php

namespace Tests\Unit\Jobs;

use App\Jobs\UpdateSearchStats;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UpdateSearchStatsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_it_calculates_top_searches_with_percentages()
    {
        // Arrange
        Cache::put('search_counts', [
            'luke' => 50,
            'vader' => 30,
            'yoda' => 10,
            'leia' => 5,
            'han' => 3,
            'chewie' => 2,
        ], now()->addHour());

        // Act
        (new UpdateSearchStats())->handle();

        // Assert
        $stats = Cache::get('search_stats');
        $this->assertArrayHasKey('top_searches', $stats);
        $topSearches = $stats['top_searches'];

        $this->assertEquals(50, $topSearches['luke']['count']);
        $this->assertEquals(30, $topSearches['vader']['count']);
        $this->assertEquals(10, $topSearches['yoda']['count']);
        $this->assertEquals(5, $topSearches['leia']['count']);
        $this->assertEquals(3, $topSearches['han']['count']);

        // Verify percentages
        $this->assertEquals(50, $topSearches['luke']['percentage']);
        $this->assertEquals(30, $topSearches['vader']['percentage']);
        $this->assertEquals(10, $topSearches['yoda']['percentage']);
        $this->assertEquals(5, $topSearches['leia']['percentage']);
        $this->assertEquals(3, $topSearches['han']['percentage']);

        // Verify only top 5 are returned
        $this->assertCount(5, $topSearches);
        $this->assertArrayNotHasKey('chewie', $topSearches);
    }

    public function test_it_calculates_average_response_time()
    {
        // Arrange
        Cache::put('request_times', [100, 200, 300, 400, 500], now()->addHour());

        // Act
        (new UpdateSearchStats())->handle();

        // Assert
        $stats = Cache::get('search_stats');
        $this->assertArrayHasKey('avg_response_time', $stats);
        $this->assertEquals(300, $stats['avg_response_time']);
    }

    public function test_it_finds_busiest_hour()
    {
        // Arrange
        $hourCounts = array_fill(0, 24, 0);
        $hourCounts[14] = 100; // 2 PM is busiest
        Cache::put('hour_counts', $hourCounts, now()->addHour());

        // Act
        (new UpdateSearchStats())->handle();

        // Assert
        $stats = Cache::get('search_stats');
        $this->assertArrayHasKey('busiest_hour', $stats);
        $this->assertEquals(14, $stats['busiest_hour']);
    }

    public function test_it_handles_empty_data()
    {
        // Arrange
        Cache::put('search_counts', [], now()->addHour());
        Cache::put('request_times', [], now()->addHour());
        Cache::put('hour_counts', array_fill(0, 24, 0), now()->addHour());

        // Act
        (new UpdateSearchStats())->handle();

        // Assert
        $stats = Cache::get('search_stats');
        
        $this->assertEmpty($stats['top_searches']);
        $this->assertEquals(0, $stats['avg_response_time']);
        $this->assertEquals(0, $stats['busiest_hour']);
    }

    public function test_it_caches_results_for_one_hour()
    {
        // Arrange
        Cache::put('search_counts', ['test' => 1], now()->addHour());
        Cache::put('request_times', [100], now()->addHour());
        Cache::put('hour_counts', array_fill(0, 24, 0), now()->addHour());

        // Act
        (new UpdateSearchStats())->handle();

        // Assert
        $this->assertTrue(Cache::has('search_stats'));
        
        // Move time forward 59 minutes
        $this->travel(59)->minutes();
        $this->assertTrue(Cache::has('search_stats'));
        
        // Move time forward 2 more minutes
        $this->travel(2)->minutes();
        $this->assertFalse(Cache::has('search_stats'));
    }
} 
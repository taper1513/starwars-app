<?php

namespace Tests\Unit;

use App\Exceptions\SwapiException;
use App\Services\SwapiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SwapiServiceTest extends TestCase
{
    private SwapiService $swapiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->swapiService = new SwapiService();
    }

    public function test_search_people_returns_normalized_results()
    {
        Http::fake([
            'swapi.py4e.com/api/people?search=luke' => Http::response([
                'count' => 1,
                'results' => [
                    [
                        'name' => 'Luke Skywalker',
                        'url' => 'https://swapi.py4e.com/api/people/1/',
                        'birth_year' => '19BBY',
                        'films' => [
                            'https://swapi.py4e.com/api/films/1/',
                        ],
                    ],
                ],
            ], 200),
            'swapi.py4e.com/api/films/1' => Http::response([
                'title' => 'A New Hope',
                'url' => 'https://swapi.py4e.com/api/films/1/',
            ], 200),
        ]);

        $result = $this->swapiService->searchPeople('luke');

        $this->assertEquals(1, $result['total']);
        $this->assertCount(1, $result['results']);
        $this->assertEquals('Luke Skywalker', $result['results'][0]['name']);
        $this->assertEquals('1', $result['results'][0]['id']);
        $this->assertEquals('19BBY', $result['results'][0]['birth_year']);
    }

    public function test_search_movies_returns_normalized_results()
    {
        Http::fake([
            'swapi.py4e.com/api/films?search=hope' => Http::response([
                'count' => 1,
                'results' => [
                    [
                        'title' => 'A New Hope',
                        'url' => 'https://swapi.py4e.com/api/films/1/',
                        'episode_id' => 4,
                        'characters' => [
                            'https://swapi.py4e.com/api/people/1/',
                        ],
                    ],
                ],
            ], 200),
            'swapi.py4e.com/api/people/1' => Http::response([
                'name' => 'Luke Skywalker',
                'url' => 'https://swapi.py4e.com/api/people/1/',
            ], 200),
        ]);

        $result = $this->swapiService->searchMovies('hope');

        $this->assertEquals(1, $result['total']);
        $this->assertCount(1, $result['results']);
        $this->assertEquals('A New Hope', $result['results'][0]['title']);
        $this->assertEquals('1', $result['results'][0]['id']);
        $this->assertEquals(4, $result['results'][0]['episode_id']);
    }

    public function test_get_person_returns_cached_data()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('person:1')
            ->andReturn([
                'name' => 'Luke Skywalker',
                'url' => 'https://swapi.py4e.com/api/people/1/',
            ]);

        Http::shouldReceive('get')->never();

        $result = $this->swapiService->getPerson('1', true);

        $this->assertEquals('Luke Skywalker', $result['name']);
        $this->assertEquals('1', $result['id']);
    }

    public function test_get_movie_returns_cached_data()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('movie:1')
            ->andReturn([
                'title' => 'A New Hope',
                'url' => 'https://swapi.py4e.com/api/films/1/',
            ]);

        Http::shouldReceive('get')->never();

        $result = $this->swapiService->getMovie('1', true);

        $this->assertEquals('A New Hope', $result['title']);
        $this->assertEquals('1', $result['id']);
    }

    public function test_get_person_handles_not_found()
    {
        Http::fake([
            'swapi.py4e.com/api/people/999' => Http::response(null, 404),
        ]);

        $result = $this->swapiService->getPerson('999');

        $this->assertEquals('999', $result['id']);
        $this->assertEquals('Unknown', $result['name']);
    }

    public function test_get_movie_handles_not_found()
    {
        Http::fake([
            'swapi.py4e.com/api/films/999' => Http::response(null, 404),
        ]);

        $result = $this->swapiService->getMovie('999');

        $this->assertEquals('999', $result['id']);
        $this->assertEquals('Unknown', $result['title']);
    }

    public function test_search_people_handles_api_error()
    {
        Http::fake([
            'swapi.py4e.com/api/people?search=luke' => Http::response(null, 500),
        ]);

        $this->expectException(SwapiException::class);
        $this->expectExceptionMessage('SWAPI request failed');

        $this->swapiService->searchPeople('luke');
    }

    public function test_search_movies_handles_api_error()
    {
        Http::fake([
            'swapi.py4e.com/api/films?search=hope' => Http::response(null, 500),
        ]);

        $this->expectException(SwapiException::class);
        $this->expectExceptionMessage('SWAPI request failed');

        $this->swapiService->searchMovies('hope');
    }

    public function test_get_person_expands_films_when_not_lightweight()
    {
        Http::fake([
            'swapi.py4e.com/api/people/1' => Http::response([
                'name' => 'Luke Skywalker',
                'url' => 'https://swapi.py4e.com/api/people/1/',
                'films' => [
                    'https://swapi.py4e.com/api/films/1/',
                ],
            ], 200),
            'swapi.py4e.com/api/films/1' => Http::response([
                'title' => 'A New Hope',
                'url' => 'https://swapi.py4e.com/api/films/1/',
            ], 200),
        ]);

        $result = $this->swapiService->getPerson('1', false);

        $this->assertEquals('Luke Skywalker', $result['name']);
        $this->assertCount(1, $result['films']);
        $this->assertEquals('A New Hope', $result['films'][0]['title']);
    }

    public function test_get_movie_expands_characters_when_not_lightweight()
    {
        Http::fake([
            'swapi.py4e.com/api/films/1' => Http::response([
                'title' => 'A New Hope',
                'url' => 'https://swapi.py4e.com/api/films/1/',
                'characters' => [
                    'https://swapi.py4e.com/api/people/1/',
                ],
            ], 200),
            'swapi.py4e.com/api/people/1' => Http::response([
                'name' => 'Luke Skywalker',
                'url' => 'https://swapi.py4e.com/api/people/1/',
            ], 200),
        ]);

        $result = $this->swapiService->getMovie('1', false);

        $this->assertEquals('A New Hope', $result['title']);
        $this->assertCount(1, $result['characters']);
        $this->assertEquals('Luke Skywalker', $result['characters'][0]['name']);
    }
} 
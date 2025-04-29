<?php

namespace App\Services;

use App\Exceptions\SwapiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class SwapiService
{
    private const BASE_URL = 'https://swapi.py4e.com/api/';
    private const CACHE_TTL = 3600; // 1 hour

    public function searchPeople(string $query)
    {
        $url = self::BASE_URL . 'people';
        return $this->fetchWithCache(
            "people_search:{$query}",
            $url,
            ['search' => $query],
            fn ($data) => $this->normalizePeopleResults($data)
        );
    }

    public function searchMovies(string $query)
    {
        $url = self::BASE_URL . 'films';
        return $this->fetchWithCache(
            "movies_search:{$query}",
            $url,
            ['search' => $query],
            fn ($data) => $this->normalizeMovieResults($data)
        );
    }

    public function getPerson(string $id, bool $lightweight = false)
    {
        $url = self::BASE_URL . "people/{$id}";

        try {
            $personData = $this->fetchWithCache(
                "person:{$id}",
                $url,
                [],
                fn ($data) => $data
            );

            if (!$personData) {
                throw new SwapiException(
                    "Person not found",
                    ['id' => $id],
                    404
                );
            }

            return $this->normalizePerson($personData, $lightweight);
        } catch (SwapiException $e) {
            if ($e->getCode() === 404) {
                return [
                    'id' => $id,
                    'name' => 'Unknown',
                ];
            }
            throw $e;
        }
    }

    public function getMovie(string $id, bool $lightweight = false)
    {
        $url = self::BASE_URL . "films/{$id}";

        try {
            $movieData = $this->fetchWithCache(
                "movie:{$id}",
                $url,
                [],
                fn ($data) => $data
            );

            if (!$movieData) {
                throw new SwapiException(
                    "Movie not found",
                    ['id' => $id],
                    404
                );
            }

            return $this->normalizeMovie($movieData, $lightweight);
        } catch (SwapiException $e) {
            if ($e->getCode() === 404) {
                return [
                    'id' => $id,
                    'title' => 'Unknown',
                ];
            }
            throw $e;
        }
    }

    private function normalizePeopleResults(array $data)
    {
        return [
            'results' => collect($data['results'] ?? [])->map(fn ($person) => $this->normalizePerson($person)),
            'total' => $data['count'] ?? 0
        ];
    }

    private function normalizeMovieResults(array $data)
    {
        return [
            'results' => collect($data['results'] ?? [])->map(fn ($movie) => $this->normalizeMovie($movie)),
            'total' => $data['count'] ?? 0
        ];
    }

    private function normalizePerson(array $person, bool $lightweight = false)
    {
        try {
            $normalizedPerson = [
                'id' => $this->extractIdFromUrl($person['url'] ?? ''),
                'name' => $person['name'] ?? 'Unknown',
                'birth_year' => $person['birth_year'] ?? null,
                'gender' => $person['gender'] ?? null,
                'height' => $person['height'] ?? null,
                'films' => $person['films'] ?? [],
                'mass' => $person['mass'] ?? null,
                'hair_color' => $person['hair_color'] ?? null,
                'eye_color' => $person['eye_color'] ?? null,            
            ];

            if (!$lightweight) {
                $normalizedPerson['films'] = collect($person['films'] ?? [])
                    ->map(function ($url) {
                        try {
                            $id = $this->extractIdFromUrl($url);
                            $movie = $this->getMovie($id, true);
                            return [
                                'id' => $id,
                                'title' => $movie['title'] ?? 'Unknown',
                            ];
                        } catch (Throwable $e) {
                            Log::error('Error normalizing film for person', [
                                'url' => $url,
                                'error' => $e->getMessage()
                            ]);
                            return null;
                        }
                    })
                    ->filter()
                    ->values()
                    ->toArray();
            }

            return $normalizedPerson;
        } catch (Throwable $e) {
            throw new SwapiException(
                'Error normalizing person data',
                ['person' => $person],
                500,
                $e
            );
        }
    }

    private function normalizeMovie(array $movie, bool $lightweight = false)
    {
        try {
            $normalizedMovie = [
                'id' => $this->extractIdFromUrl($movie['url'] ?? ''),
                'title' => $movie['title'] ?? 'Unknown',
                'episode_id' => $movie['episode_id'] ?? null,
                'release_date' => $movie['release_date'] ?? null,
                'director' => $movie['director'] ?? null,
                'characters' => $movie['characters'] ?? [],
                'opening_crawl' => $movie['opening_crawl'] ?? null,
            ];

            if (!$lightweight) {
                $normalizedMovie['characters'] = collect($movie['characters'] ?? [])
                    ->map(function ($url) {
                        try {
                            $id = $this->extractIdFromUrl($url);
                            $person = $this->getPerson($id, true);
                            return [
                                'id' => $id,
                                'name' => $person['name'] ?? 'Unknown',
                            ];
                        } catch (Throwable $e) {
                            Log::error('Error normalizing character for movie', [
                                'url' => $url,
                                'error' => $e->getMessage()
                            ]);
                            return null;
                        }
                    })
                    ->filter()
                    ->values()
                    ->toArray();
            }
            
            return $normalizedMovie;
        } catch (Throwable $e) {
            throw new SwapiException(
                'Error normalizing movie data',
                ['movie' => $movie],
                500,
                $e
            );
        }
    }

    private function extractIdFromUrl(string $url)
    {
        if (empty($url)) {
            throw new SwapiException(
                'Invalid URL provided',
                ['url' => $url],
                400
            );
        }
        return basename(rtrim($url, '/'));
    }

    private function fetchWithCache(string $cacheKey, string $url, array $params, callable $normalizer)
    {
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        try {
            Log::info("Requesting from SWAPI: {$url}", ['params' => $params]);
            $response = Http::timeout(5)->get($url, $params);

            if ($response->failed()) {
                throw new SwapiException(
                    'SWAPI request failed',
                    [
                        'url' => $url,
                        'params' => $params,
                        'status' => $response->status(),
                        'body' => $response->body()
                    ],
                    $response->status()
                );
            }

            $data = $response->json();
            if (!$data) {
                throw new SwapiException(
                    'Invalid JSON response from SWAPI',
                    [
                        'url' => $url,
                        'params' => $params,
                        'body' => $response->body()
                    ],
                    500
                );
            }

            $result = $normalizer($data);
            
            if (
                $result !== null &&
                !(
                    (is_array($result) && empty($result)) ||
                    (is_object($result) && empty((array)$result))
                )
            ) {
                Cache::put($cacheKey, $result, self::CACHE_TTL);
            }

            return $result;
        } catch (SwapiException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new SwapiException(
                'Error fetching data from SWAPI',
                [
                    'url' => $url,
                    'params' => $params,
                    'error' => $e->getMessage()
                ],
                500,
                $e
            );
        }
    }
}

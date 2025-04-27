<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SwapiService
{
    private const BASE_URL = 'https://swapi.py4e.com/api/';
    private const CACHE_TTL = 3600; // 1 hour

    public function searchPeople(string $query)
    {
        $url = self::BASE_URL . '/people';
        return $this->fetchWithCache(
            "people_search:{$query}",
            $url,
            ['search' => $query],
            fn ($data) => $this->normalizePeopleResults($data)
        );
    }

    public function searchMovies(string $query)
    {
        $url = self::BASE_URL . '/films';
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

        return $this->fetchWithCache(
            "person:{$id}" . ($lightweight ? 'lightweight' : 'full'),
            $url,
            [],
            function ($data) use ($lightweight, $id) {
                    if (!$data || empty($data['name'])) {
                    Log::warning("Empty or invalid person data for ID {$id}", $data);
                    return ['id' => $id, 'name' => 'Unknown'];
                }

                $person = $this->normalizePerson($data);

                if (!$lightweight) {
                    $person['films'] = collect($data['films'] ?? [])
                        ->map(function ($url) {
                            $id = $this->extractIdFromUrl($url);
                            $movie = $this->getMovie($id, true);
                            return [
                                'id' => $id,
                                'title' => $movie['title'] ?? 'Unknown',
                            ];
                        })
                        ->toArray();
                }

                return $person;
            }
        );
    }

    public function getMovie(string $id, bool $lightweight = false)
    {
        $url = self::BASE_URL . "films/{$id}";

        return $this->fetchWithCache(
            "movie:{$id}" . ($lightweight ? 'lightweight' : 'full'),
            $url,
            [],
            function ($data) use ($lightweight, $id) {
                if (!$data || empty($data['title'])) {
                    Log::warning("Empty or invalid movie data for ID {$id}", $data);
                    return ['id' => $id, 'title' => 'Unknown'];
                }

                $movie = $this->normalizeMovie($data);

                if (!$lightweight) {
                    $movie['characters'] = collect($data['characters'] ?? [])
                        ->map(function ($url) {
                            $id = $this->extractIdFromUrl($url);
                            $person = $this->getPerson($id, true);
                            return [
                                'id' => $id,
                                'name' => $person['name'] ?? 'Unknown',
                            ];
                        })
                        ->toArray();
                }

                return $movie;
            }
        );
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

    private function normalizePerson(array $person)
    {
        return [
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
    }

    private function normalizeMovie(array $movie)
    {
        return [
            'id' => $this->extractIdFromUrl($movie['url'] ?? ''),
            'title' => $movie['title'] ?? 'Unknown',
            'episode_id' => $movie['episode_id'] ?? null,
            'release_date' => $movie['release_date'] ?? null,
            'director' => $movie['director'] ?? null,
            'characters' => $movie['characters'] ?? [],
            'opening_crawl' => $movie['opening_crawl'] ?? null,
        ];
    }

    private function extractIdFromUrl(string $url)
    {
        return basename(rtrim($url, '/'));
    }

    private function fetchWithCache(string $cacheKey, string $url, array $params, callable $normalizer)
    {
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($url, $params, $normalizer) {
            Log::info("Requesting from SWAPI: {$url}", ['params' => $params]);

            $response = Http::get($url, $params);

            if ($response->failed() || !$response->json()) {
                Log::error("Failed request to SWAPI", [
                    'url' => $url,
                    'params' => $params,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return null;
            }

            return $normalizer($response->json());
        });
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class LocationService
{
    private $apiKey;
    private $baseUrl = 'https://api.countrystatecity.in/v1';

    public function __construct()
    {
        $this->apiKey = config('services.location.api_key');
    }

    /**
     * Get all countries
     */
    public function getCountries()
    {
        return Cache::remember('countries', 86400, function () {
            try {
                $response = Http::withHeaders([
                    'X-CSCAPI-KEY' => $this->apiKey
                ])->get("{$this->baseUrl}/countries");

                if ($response->successful()) {
                    return $response->json();
                }
                return [];
            } catch (\Exception $e) {
                \Log::error('Error fetching countries: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get states by country code
     */
    public function getStates($countryCode)
    {
        return Cache::remember("states_{$countryCode}", 86400, function () use ($countryCode) {
            try {
                $response = Http::withHeaders([
                    'X-CSCAPI-KEY' => $this->apiKey
                ])->get("{$this->baseUrl}/countries/{$countryCode}/states");

                if ($response->successful()) {
                    return $response->json();
                }
                return [];
            } catch (\Exception $e) {
                \Log::error('Error fetching states: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Get cities by country and state code
     */
    public function getCities($countryCode, $stateCode)
    {
        return Cache::remember("cities_{$countryCode}_{$stateCode}", 86400, function () use ($countryCode, $stateCode) {
            try {
                $response = Http::withHeaders([
                    'X-CSCAPI-KEY' => $this->apiKey
                ])->get("{$this->baseUrl}/countries/{$countryCode}/states/{$stateCode}/cities");

                if ($response->successful()) {
                    return $response->json();
                }
                return [];
            } catch (\Exception $e) {
                \Log::error('Error fetching cities: ' . $e->getMessage());
                return [];
            }
        });
    }
}
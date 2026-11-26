<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NewsApiService
{
    public function search(string $keyword = 'kesehatan hewan'): array
    {
        $cleanKeyword = trim($keyword) ?: 'kesehatan hewan';
        $cacheKey = 'newsdata_' . md5(strtolower($cleanKeyword));

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $apiKey = config('services.newsdata.key');
        if (empty($apiKey)) {
            return [
                'success' => false,
                'message' => 'NEWSDATA_API_KEY belum dikonfigurasi pada file .env.',
                'data'    => [],
            ];
        }

        try {
            $http = Http::timeout(10);

            // Jika curl.cainfo di php.ini menunjuk ke file yang tidak ada (mis. pindah folder Laragon),
            // gunakan berkas cacert.pem valid yang tersedia agar verifikasi SSL tetap aman.
            $caInfo = ini_get('curl.cainfo');
            if (! $caInfo || ! is_file($caInfo)) {
                $candidates = array_filter([
                    config('services.newsdata.ca_bundle'),
                    dirname(base_path(), 2) . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'ssl' . DIRECTORY_SEPARATOR . 'cacert.pem',
                ]);
                foreach ($candidates as $path) {
                    if (is_file($path)) {
                        $http = $http->withOptions(['verify' => $path]);
                        break;
                    }
                }
            }

            $response = $http->get(config('services.newsdata.url'), [
                'apikey'   => $apiKey,
                'q'        => $cleanKeyword,
                'language' => 'id',
            ]);

            if ($response->failed() || $response->json('status') !== 'success') {
                return [
                    'success' => false,
                    'message' => $response->json('results.message') ?? 'Gagal mengambil data dari API berita.',
                    'data'    => [],
                ];
            }

            $items = collect($response->json('results', []))
                ->filter(fn ($item) => ! empty($item['title']) && ! empty($item['link']))
                ->map(fn ($item) => [
                    'judul'        => trim((string) $item['title']),
                    'link'         => html_entity_decode((string) $item['link'], ENT_QUOTES, 'UTF-8'),
                    'ringkasan'    => $item['description'] ?? 'Baca selengkapnya pada situs sumber.',
                    'gambar'       => ! empty($item['image_url'])
                        ? html_entity_decode((string) $item['image_url'], ENT_QUOTES, 'UTF-8')
                        : null,
                    'sumber'       => $item['source_name'] ?? $item['source_id'] ?? parse_url($item['link'], PHP_URL_HOST),
                    'published_at' => $item['pubDate'] ?? null,
                ])
                ->values()
                ->all();

            $result = [
                'success' => true,
                'message' => 'Berhasil mengambil artikel.',
                'data'    => $items,
            ];

            // Hanya simpan cache 15 menit jika permintaan API berhasil
            Cache::put($cacheKey, $result, now()->addMinutes(15));

            return $result;
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Koneksi ke layanan berita bermasalah. Periksa koneksi internet atau konfigurasi SSL PHP.',
                'data'    => [],
            ];
        }
    }
}

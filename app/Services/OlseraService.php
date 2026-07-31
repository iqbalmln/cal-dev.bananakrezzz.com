<?php

namespace App\Services;

use App\Models\Cabang;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use RuntimeException;

class OlseraService
{
    protected Client $client;
    protected string $baseUrl = 'https://api-open.olsera.co.id/api/open-api/v1/id/';
    protected ?Cabang $cabang = null;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'verify' => false,
        ]);
    }

    /**
     * Kembalikan salinan service yang memakai kredensial Olsera milik cabang ini.
     *
     * Sengaja meng-clone, bukan mengubah $this, supaya instance yang di-share
     * container tidak ikut berubah saat command melakukan loop antar cabang.
     */
    public function forCabang(Cabang $cabang): static
    {
        $salinan = clone $this;
        $salinan->cabang = $cabang;

        return $salinan;
    }

    public function getToken()
    {
        $cabang = $this->cabang();

        return Cache::remember($this->tokenCacheKey(), 55 * 60, function () use ($cabang) {
            try {
                $res = $this->client->post('token', [
                    'form_params' => [
                        'app_id' => $cabang->olsera_app_id,
                        'secret_key' => $cabang->olsera_secret_key,
                        'grant_type' => 'secret_key',
                    ],
                ]);

                $body = json_decode($res->getBody()->getContents(), true);
                $token = $body['access_token'] ?? null;
                Log::info("✅ Token Olsera cabang {$cabang->nama} berhasil diperbarui.");
                return $token;
            } catch (Exception $e) {
                Log::error("❌ Gagal mendapatkan token Olsera cabang {$cabang->nama}: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Buang token yang tersimpan, dipakai saat kredensial cabang diubah.
     */
    public function lupakanToken(): void
    {
        Cache::forget($this->tokenCacheKey());
    }

    public static function lupakanTokenCabang(Cabang $cabang): void
    {
        Cache::forget("olsera_token_cabang_{$cabang->id}");
    }

    public function getSalesItemsByDate(string $date)
    {
        $cabang = $this->cabang();
        $token = $this->getToken();

        if (!$token) {
            Log::warning("⚠️ Token Olsera cabang {$cabang->nama} kosong, hentikan proses.");
            return [];
        }

        $page = 1;
        $data = [];
        $maxRetries = 5;

        while (true) {
            $retryCount = 0;

            while ($retryCount <= $maxRetries) {
                try {
                    $res = $this->client->get('report/salesitemsbydate', [
                        'query' => [
                            'per_page' => 100,
                            'from' => $date,
                            'to' => $date,
                            'page' => $page,
                        ],
                        'headers' => [
                            'Authorization' => "Bearer {$token}",
                        ],
                    ]);

                    $body = json_decode($res->getBody()->getContents(), true);

                    if (empty($body['data'])) {
                        break 2; // keluar dari 2 loop
                    }

                    $data = array_merge($data, $body['data']);
                    Log::info("✅ [{$cabang->nama}] Halaman {$page} sukses, total sementara: " . count($data));

                    $page++;
                    usleep(400000); // delay 400ms antar halaman
                    break; // keluar dari retry loop

                } catch (ClientException $e) {
                    $response = $e->getResponse();

                    if ($response && $response->getStatusCode() === 429) {
                        $retryAfter = $response->getHeader('Retry-After')[0] ?? pow(2, $retryCount + 1);
                        Log::warning("⚠️ [{$cabang->nama}] Rate limit halaman {$page}. Retry setelah {$retryAfter}s (percobaan ke-{$retryCount})");
                        sleep($retryAfter);
                        $retryCount++;
                        continue;
                    }

                    Log::error("❌ [{$cabang->nama}] Error halaman {$page}: " . $e->getMessage());
                    break 2; // keluar total
                } catch (Exception $e) {
                    Log::error("❌ [{$cabang->nama}] Exception umum halaman {$page}: " . $e->getMessage());
                    break 2;
                }
            }

            if ($retryCount > $maxRetries) {
                Log::error("❌ [{$cabang->nama}] Gagal mengambil halaman {$page} setelah {$maxRetries} percobaan.");
                break;
            }
        }

        return collect($data)
            ->filter(fn($item) => !empty($item['customer_name']))
            ->map(fn($val) => [
                'name' => $val['customer_name'],
                'id_pos' => $val['sales_order_item_id'],
                'price' => explode('.', $val['comission_amount'] ?? '0')[0],
                'order_time' => explode(' ', $val['forder_date'])[1] ?? null,
                'order_date' => $val['order_date'],
            ])
            ->values()
            ->toArray();
    }

    protected function cabang(): Cabang
    {
        if (!$this->cabang) {
            throw new RuntimeException('Cabang belum ditentukan. Panggil forCabang() lebih dulu.');
        }

        return $this->cabang;
    }

    protected function tokenCacheKey(): string
    {
        return "olsera_token_cabang_{$this->cabang()->id}";
    }
}

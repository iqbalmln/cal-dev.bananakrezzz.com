<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class OlseraService
{
    protected $client;
    protected $baseUrl = 'https://api-open.olsera.co.id/api/open-api/v1/id/';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'verify' => false,
        ]);
    }

    public function getToken()
    {
        return Cache::remember('olsera_token', 55 * 60, function () {
            try {
                $res = $this->client->post('token', [
                    'form_params' => [
                            'app_id' => env('OLSERA_APP_ID', 'pjm6n8KIbywEf4jLRuRX'),
                            'secret_key' => env('OLSERA_SECRET_KEY', 'otAW7uDlLFt1cvAgyzgsON6ifh31xXJw'),
                            'grant_type' => 'secret_key',
                    ],
                ]);

                $body = json_decode($res->getBody()->getContents(), true);
                $token = $body['access_token'] ?? null;
                Log::info('✅ Token Olsera berhasil diperbarui.');
                return $token;
            } catch (Exception $e) {
                Log::error('❌ Gagal mendapatkan token dari Olsera: ' . $e->getMessage());
                return null;
            }
        });
    }

    public function getSalesItemsByDate(string $date)
    {
        $token = $this->getToken();
        if (!$token) {
            Log::warning('⚠️ Token Olsera kosong, hentikan proses.');
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
                    Log::info("✅ Halaman {$page} sukses, total sementara: " . count($data));

                    $page++;
                    usleep(400000); // delay 400ms antar halaman
                    break; // keluar dari retry loop

                } catch (ClientException $e) {
                    $response = $e->getResponse();

                    if ($response && $response->getStatusCode() === 429) {
                        $retryAfter = $response->getHeader('Retry-After')[0] ?? pow(2, $retryCount + 1);
                        Log::warning("⚠️ Rate limit halaman {$page}. Retry setelah {$retryAfter}s (percobaan ke-{$retryCount})");
                        sleep($retryAfter);
                        $retryCount++;
                        continue;
                    }

                    Log::error("❌ Error halaman {$page}: " . $e->getMessage());
                    break 2; // keluar total
                } catch (Exception $e) {
                    Log::error("❌ Exception umum halaman {$page}: " . $e->getMessage());
                    break 2;
                }
            }

            if ($retryCount > $maxRetries) {
                Log::error("❌ Gagal mengambil halaman {$page} setelah {$maxRetries} percobaan.");
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
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportService
{
    public function fetch(string $url): array
    {
        $parts = parse_url(trim($url));
        if (! $parts || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true) || empty($parts['host'])) {
            throw new \RuntimeException('Only public http(s) product links are supported.');
        }
        $host = strtolower($parts['host']);
        $ip = gethostbyname($host);
        if ($ip !== $host && ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            throw new \RuntimeException('That host is not publicly reachable.');
        }

        $response = Http::timeout(10)
            ->withHeaders(['User-Agent' => 'GBOffersBot/1.0 (+https://gboffers.com)'])
            ->get($url);
        if (! $response->successful()) {
            throw new \RuntimeException('Could not fetch that page (HTTP '.$response->status().').');
        }
        $html = Str::limit($response->body(), 1000000);

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();
        $xp = new \DOMXPath($dom);
        $meta = function (string $key) use ($xp) {
            $n = $xp->query("//meta[@property='$key' or @name='$key']")->item(0);

            return $n ? trim($n->getAttribute('content')) : '';
        };
        $abs = function (string $src) use ($parts) {
            $src = trim($src);
            if ($src === '' || str_starts_with($src, 'data:')) {
                return null;
            }
            if (preg_match('#^https?://#i', $src)) {
                return $src;
            }
            $base = $parts['scheme'].'://'.$parts['host'];

            return $base.'/'.ltrim($src, '/');
        };

        $title = $meta('og:title');
        if ($title === '') {
            $t = $xp->query('//title')->item(0);
            $title = $t ? trim($t->textContent) : '';
        }
        if ($title === '') {
            $h = $xp->query('//h1')->item(0);
            $title = $h ? trim($h->textContent) : '';
        }
        $description = $meta('og:description') ?: $meta('description');
        if ($description === '') {
            foreach ($xp->query('//p') as $p) {
                $text = trim(preg_replace('/\s+/', ' ', $p->textContent));
                if (mb_strlen($text) > 60) {
                    $description = mb_substr($text, 0, 500);
                    break;
                }
            }
        }
        $image = $abs($meta('og:image')) ?? '';
        $images = [];
        if ($image !== '') {
            $images[] = $image;
        }
        foreach ($xp->query('//img[@src]') as $img) {
            $u = $abs($img->getAttribute('src'));
            if ($u && ! in_array($u, $images, true)) {
                $images[] = $u;
            }
            if (count($images) >= 6) {
                break;
            }
        }
        preg_match_all('/(?:UGX|USh|Ush|UGX\.?)\s*([\d,]{4,})/i', $dom->textContent, $m);
        $amounts = array_map(fn ($v) => (int) str_replace(',', '', $v), $m[1] ?? []);

        return [
            'title' => mb_substr(trim(preg_replace('/\s+/', ' ', $title)), 0, 160),
            'description' => $description,
            'image' => $image,
            'images' => array_values($images),
            'price' => $amounts ? max($amounts) : 0,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Dictionary-first EN → VI translator for seeding CMS translation rows.
 */
class ViSeedTranslator
{
    private bool $useApi;

    /** @var array<string, string> */
    private array $exactMap;

    /** @var array<string, string> slug => field maps */
    private array $slugMaps;

    public function __construct(bool $useApi = true)
    {
        $this->useApi = $useApi;
        $this->exactMap = $this->buildExactMap();
        $this->slugMaps = $this->buildSlugMaps();
    }

    public static function isCorrupted(?string $text): bool
    {
        if ($text === null || $text === '') {
            return false;
        }

        if (preg_match('/\?{2,}/', $text)) {
            return true;
        }

        if (preg_match('/[A-Za-zÀ-ỹ]\?+[A-Za-zÀ-ỹ]/u', $text)) {
            return true;
        }

        return false;
    }

    /**
     * @param array<string, mixed> $context entity, field, slug, enTitle
     */
    public function translate(string $text, array $context = []): string
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return $text;
        }

        if (isset($this->exactMap[$trimmed])) {
            return $this->exactMap[$trimmed];
        }

        $slug = (string) ($context['slug'] ?? '');
        $field = (string) ($context['field'] ?? '');
        $entity = (string) ($context['entity'] ?? '');

        if ($slug !== '' && isset($this->slugMaps[$entity][$slug][$field])) {
            return $this->slugMaps[$entity][$slug][$field];
        }

        if ($this->useApi) {
            $api = $this->translateViaMyMemory($trimmed);
            if ($api !== null && $api !== '') {
                return $api;
            }
        }

        return $this->translateByPhrases($trimmed);
    }

    private function translateViaMyMemory(string $text): ?string
    {
        if (strlen($text) > 450) {
            return $this->translateLongText($text);
        }

        $url = 'https://api.mymemory.translated.net/get?' . http_build_query([
            'q' => $text,
            'langpair' => 'en|vi',
            'de' => 'admin@shinning.com',
        ]);

        $json = $this->httpGet($url);
        if ($json === null) {
            return null;
        }

        $data = json_decode($json, true);
        if (!is_array($data) || ($data['responseStatus'] ?? 0) !== 200) {
            return null;
        }

        $translated = trim((string) ($data['responseData']['translatedText'] ?? ''));
        if ($translated === '' || strtoupper($translated) === strtoupper($text)) {
            return null;
        }

        usleep(250_000);

        return $translated;
    }

    private function translateLongText(string $text): ?string
    {
        $chunks = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [$text];
        $parts = [];
        foreach ($chunks as $chunk) {
            if (strlen($chunk) > 450) {
                $sub = str_split($chunk, 400);
                foreach ($sub as $piece) {
                    $t = $this->translateViaMyMemory($piece);
                    $parts[] = $t ?? $this->translateByPhrases($piece);
                }
                continue;
            }
            $t = $this->translateViaMyMemory($chunk);
            $parts[] = $t ?? $this->translateByPhrases($chunk);
        }

        return implode(' ', $parts);
    }

    private function httpGet(string $url): ?string
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 15,
                'header' => "User-Agent: ShinningHotel-ViSeed/1.0\r\n",
            ],
        ]);

        $body = @file_get_contents($url, false, $context);

        return $body === false ? null : $body;
    }

    private function translateByPhrases(string $text): string
    {
        $phrases = $this->phraseMap();
        uksort($phrases, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));

        $out = $text;
        foreach ($phrases as $en => $vi) {
            $out = str_ireplace($en, $vi, $out);
        }

        return $out;
    }

    /** @return array<string, string> */
    private function buildExactMap(): array
    {
        return [
            'Home' => 'Trang chủ',
            'Rooms' => 'Phòng',
            'About' => 'Giới thiệu',
            'About Us' => 'Về chúng tôi',
            'News' => 'Tin tức',
            'Location' => 'Vị trí',
            'Contact' => 'Liên hệ',
            'Book Now' => 'Đặt phòng',
            'Welcome to Shinning' => 'Chào mừng đến Shinning',
            'Where Every Stay Shines' => 'Nơi mỗi kỳ nghỉ đều rực rỡ',
            'Standard' => 'Standard',
            'Deluxe' => 'Deluxe',
            'Suite' => 'Suite',
            'Shinning Standard King' => 'Phòng Standard King',
            'Deluxe Ocean View' => 'Deluxe Hướng biển',
            'Presidential Suite' => 'Presidential Suite',
            'Grand Opening Special' => 'Ưu đãi khai trương',
            'Seasonal Spa Packages' => 'Gói spa theo mùa',
            'Celebrate our opening with 20% off your first stay.' => 'Khai trương — giảm 20% cho kỳ nghỉ đầu tiên.',
            'Rejuvenate with our new wellness offerings.' => 'Tái tạo năng lượng với liệu trình wellness mới.',
            'Comfortable rooms for smart travelers' => 'Phòng tiện nghi cho khách thông thái',
            'Spacious rooms with premium amenities' => 'Phòng rộng với tiện ích cao cấp',
            'Ultimate luxury and panoramic views' => 'Sang trọng tối đa và tầm nhìn panorama',
            'About Shinning Hotel' => 'Giới thiệu Shinning Hotel',
            'Location & Directions' => 'Vị trí & Chỉ đường',
        ];
    }

    /** @return array<string, array<string, array<string, string>>> */
    private function buildSlugMaps(): array
    {
        return [
            'page' => [
                'about-us' => [
                    'title' => 'Về chúng tôi',
                    'seo_title' => 'Giới thiệu Shinning Hotel',
                    'content' => '<p>Chào mừng đến <strong>Shinning Hotel</strong> — nơi mỗi kỳ nghỉ đều rực rỡ. Chúng tôi kết hợp sự tinh tế vượt thời gian với tiện nghi hiện đại.</p><p>Đội ngũ tận tâm của chúng tôi mang đến trải nghiệm khó quên từ lúc nhận phòng đến khi tiễn khách.</p>',
                ],
                'location' => [
                    'title' => 'Vị trí',
                    'seo_title' => 'Vị trí & Chỉ đường',
                    'content' => '<p>Chúng tôi tọa lạc tại 123 Nguyễn Huệ, Quận 1. Cách các điểm tham quan, mua sắm và khu kinh doanh chỉ vài phút.</p><p>Dịch vụ đưa đón sân bay theo yêu cầu.</p>',
                ],
            ],
            'news' => [
                'grand-opening-special' => [
                    'title' => 'Ưu đãi khai trương',
                    'summary' => 'Khai trương — giảm 20% cho kỳ nghỉ đầu tiên.',
                ],
                'seasonal-spa-packages' => [
                    'title' => 'Gói spa theo mùa',
                    'summary' => 'Tái tạo năng lượng với liệu trình wellness mới.',
                ],
            ],
            'room' => [
                'standard-king' => ['name' => 'Phòng Standard King'],
                'deluxe-ocean-view' => ['name' => 'Deluxe Hướng biển'],
                'presidential-suite' => ['name' => 'Presidential Suite'],
            ],
            'room_category' => [
                'standard' => [
                    'name' => 'Standard',
                    'description' => 'Phòng tiện nghi cho khách thông thái',
                ],
                'deluxe' => [
                    'name' => 'Deluxe',
                    'description' => 'Phòng rộng với tiện ích cao cấp',
                ],
                'suite' => [
                    'name' => 'Suite',
                    'description' => 'Sang trọng tối đa và tầm nhìn panorama',
                ],
            ],
        ];
    }

    /** @return array<string, string> */
    private function phraseMap(): array
    {
        return [
            'Welcome to' => 'Chào mừng đến',
            'Where Every Stay Shines' => 'Nơi mỗi kỳ nghỉ đều rực rỡ',
            'Book Now' => 'Đặt phòng',
            'Book before the end of the month and enjoy exclusive rates on all room categories.' =>
                'Đặt trước cuối tháng để nhận mức giá ưu đãi cho mọi hạng phòng.',
            'Indulge in treatments inspired by local traditions and global luxury standards.' =>
                'Thưởng thức liệu trình lấy cảm hứng từ truyền thống địa phương và tiêu chuẩn sang trọng toàn cầu.',
            'king bed' => 'giường king',
            'city view' => 'view thành phố',
            'rain shower' => 'vòi sen',
            'floor-to-ceiling windows' => 'cửa sổ trần đến sàn',
            'lounge area' => 'khu vực tiếp khách',
            'private terrace' => 'sân thượng riêng',
            'butler service' => 'dịch vụ quản gia',
            'dining room' => 'phòng ăn',
        ];
    }
}

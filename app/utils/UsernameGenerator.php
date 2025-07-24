<?php 

namespace App\Utils;

class UsernameGenerator {
    public static function generate(): array
    {
        $faIcons = ['fa-cat', 'fa-dog', 'fa-ghost', 'fa-rocket', 'fa-mug-hot', 'fa-headphones', 'fa-music', 'fa-star', 'fa-heart'];
        $nouns = ['Mèo', 'Cáo', 'Chó', 'Ma', 'Cú', 'Gấu', 'Cà', 'Táo', 'Bí', 'Chuối'];
        $adjectives = ['Mơ', 'Hài', 'Ngầu', 'Lì', 'Dễ', 'Vui', 'Lặng', 'Chill', 'Lạ', 'Béo'];

        $icon = $faIcons[array_rand($faIcons)];
        $name = $nouns[array_rand($nouns)] . ' ' . $adjectives[array_rand($adjectives)];

        return [
            'icon' => $icon,
            'name' => $name
        ];
    }
}
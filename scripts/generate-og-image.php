<?php

/**
 * Generates public/images/og-default.png (1200×630) from the brand tokens.
 * Placeholder art — replace with a designed image before launch if desired.
 *
 * Rerun after changing colors:  php scripts/generate-og-image.php
 */
[$primary, $secondary, $accent] = readBrandColors(__DIR__ . '/../resources/css/tokens.css');

$image = imagecreatetruecolor(1200, 630);
imagefill($image, 0, 0, allocate($image, $secondary));
paintGlow($image, 1050, 100, 700, $primary);
paintGlow($image, 150, 560, 600, $accent);

writeLine($image, 'BARAA AL-DOUMANI', 90, 220, 5, '#ffffff');
writeLine($image, 'Freelance Software Developer - Riyadh', 90, 330, 3, '#e2e8f0');
writeLine($image, 'Laravel . PostgreSQL . APIs', 90, 400, 3, $accent);

@mkdir(__DIR__ . '/../public/images', 0775, true);
imagepng($image, __DIR__ . '/../public/images/og-default.png', 9);
echo "Wrote public/images/og-default.png\n";

function readBrandColors(string $tokensPath): array
{
    $css = file_get_contents($tokensPath);
    $read = function (string $name) use ($css): string {
        preg_match("/--brand-{$name}:\s*(#[0-9a-fA-F]{6})/", $css, $match);
        return $match[1];
    };

    return [$read('primary'), $read('secondary'), $read('accent')];
}

function allocate(GdImage $image, string $hex): int
{
    [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');

    return imagecolorallocate($image, $r, $g, $b);
}

function paintGlow(GdImage $image, int $cx, int $cy, int $size, string $hex): void
{
    [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
    for ($i = 10; $i >= 1; $i--) {
        $color = imagecolorallocatealpha($image, $r, $g, $b, 96 + $i * 3);
        imagefilledellipse($image, $cx, $cy, (int) ($size * $i / 10), (int) ($size * $i / 10), $color);
    }
}

function writeLine(GdImage $image, string $text, int $x, int $y, int $scale, string $hex): void
{
    $font = 5;
    $w = imagefontwidth($font) * strlen($text);
    $h = imagefontheight($font);
    [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');

    $stamp = imagecreatetruecolor($w, $h);
    imagealphablending($stamp, false);
    imagefill($stamp, 0, 0, imagecolorallocatealpha($stamp, 0, 0, 0, 127));
    imagestring($stamp, $font, 0, 0, $text, imagecolorallocate($stamp, $r, $g, $b));

    imagesavealpha($stamp, true);
    imagecopyresized($image, $stamp, $x, $y, 0, 0, $w * $scale, $h * $scale, $w, $h);
    imagedestroy($stamp);
}

<?php
// public/icons/generate.php — Run once to create placeholder icons
// Usage: php public/icons/generate.php
// Requires GD library (usually bundled with PHP)

$sizes = [72, 96, 128, 144, 152, 192, 384, 512];
$color = '#2563eb'; // Blue

foreach ($sizes as $size) {
    $img = imagecreatetruecolor($size, $size);
    
    // Parse hex color
    $r = hexdec(substr($color, 1, 2));
    $g = hexdec(substr($color, 3, 2));
    $b = hexdec(substr($color, 5, 2));
    
    $bg = imagecolorallocate($img, $r, $g, $b);
    $white = imagecolorallocate($img, 255, 255, 255);
    
    // Fill background
    imagefill($img, 0, 0, $bg);
    
    // Draw a simple cross symbol
    $thickness = max(4, $size / 20);
    $center = $size / 2;
    $armLength = $size / 4;
    
    // Horizontal
    imagefilledrectangle($img, $center - $armLength, $center - $thickness/2, 
                         $center + $armLength, $center + $thickness/2, $white);
    // Vertical
    imagefilledrectangle($img, $center - $thickness/2, $center - $armLength,
                         $center + $thickness/2, $center + $armLength, $white);
    
    // Save
    imagepng($img, __DIR__ . "/icon-{$size}x{$size}.png");
    imagedestroy($img);
    
    echo "Created icon-{$size}x{$size}.png\n";
}

echo "Done.\n";
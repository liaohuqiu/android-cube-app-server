<?php
class MApps_TestImage extends MApps_AppBase_BasePageApp
{
    protected function main()
    {
        $origin_url = $this->getRequest()->getInfo()['origin_url'];
        $path_list = explode('/', $origin_url);

        list($name_str, $ext) = explode('.', $path_list[count($path_list) - 1]);
        list($text, $name_str) = explode('-', $name_str);
        $wh = explode('x', $name_str);
        $text_size = 30;

        $size = max($wh[0], $wh[1]);
        $width = $size;
        $height = $size;

        $font = ROOT_DIR . "/data/droid_mono.ttf";
        // Set the content-type
        header('Content-Type: image/png');

        // Create the image
        $im = imagecreatetruecolor($width, $height);

        // Get Bounding Box Size
        $box = imagettfbbox($text_size, 0, $font, $text);

        // Get your Text Width and Height
        $text_width = abs($box[4] - $box[0]);
        $text_height = abs($box[5] - $box[1]);

        $x = ($width - $text_width) / 2;
        $y = ($height - $text_height) / 2;

        // Create some colors
        $back_ground = imagecolorallocate($im, 255, 100, 255);
        imagefilledrectangle($im, 0, 0, $width, $height, $back_ground);

        // Add the text
        $box = imagettftext($im, $text_size, 0, 0, $text_height, $black, $font, $text);

        // Using imagepng() results in clearer text compared with imagejpeg()
        imagepng($im);
        imagedestroy($im);
        exit;
    }

    protected function outputBody()
    {
    }
}

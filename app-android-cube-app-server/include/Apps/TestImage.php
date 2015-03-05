<?php
class MApps_TestImage extends MApps_AppBase_BasePageApp
{
    protected function main()
    {
        $origin_url = $this->getRequest()->getInfo()['origin_url'];
        $path_list = explode('/', $origin_url);

        $sg_list = explode('.', $path_list[count($path_list) - 1]);
        $sg_count = count($sg_list);
        if ($sg_count < 3)
        {
            header('Status: 404 Not Found');
            echo '<h1>:( 404 Not Found</h1>';
            exit;
        }
        if ($sg_count == 3)
        {
            $size = 110;
        }
        else
        {
            $xy = explode('_', $sg_list[2]);
            $size = max($xy[0], $xy[1]);
        }

        $text_size = 30;
        $text = $sg_list[0];
        $width = $size;
        $height = $size;

        $font = ROOT_DIR . "/data/droid_mono.ttf";

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
        $background = imagecolorallocate($im, 255, 100, 255);
        imagefilledrectangle($im, 0, 0, $width, $height, $background);

        // Add the text
        $box = imagettftext($im, $text_size, 0, 10, $text_height + 10, $black, $font, $text);

        // Using imagepng() results in clearer text compared with imagejpeg()
        header('Content-Type: image/png');
        imagepng($im);
        imagedestroy($im);
        exit;
    }

    protected function outputBody()
    {
    }
}

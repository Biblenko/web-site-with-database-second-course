<?php

class Font {
    protected $size = 10;
    protected $font = 5;
    protected $angle = 0;
    public function __construct() {
    }

    function getWidth($text) {
        if (trim($text) === "") return 0;
        if (is_int($this->font)) {
            return imagefontwidth($this->font) * strlen($text);
        } else {
            return 0;
        }
    }

    function getHeight($text) {
        if (trim($text) === "") return 0;
        if (is_int($this->font)) {
            return imagefontheight($this->font);
        } else {
            return 0;
        }
    }

    function draw($image, $x, $y, $text, $color) {
        if (trim($text) === "") return;
        if (is_int($this->font)) {
            imagestring($image, $this->font, $x, $y, $text, $color);
        }else {
            imagettftext($image, $this->size, $this->angle, $x, $y + $this->getHeight($text), $color, $this->font, $text);
        }

    }

}


class Diagram {

    protected $width = 300, $height = 200;
    protected $bgImage = null;
    protected $bgColor = [255, 255, 255];
    protected $axisColor = [0, 0, 0];
    protected $titleColor = [0, 0, 255];
    protected $data = [];
    protected $image = null;
    protected $title = "";
    protected $fontTitle;
    protected $fontAxis;
    protected $countAxis0Y = 10;
    protected $min = null;
    protected $max = null;
    protected $colors = [];

    public function __construct() {
        $this->fontTitle = new Font();
        $this->fontAxis = new Font();
    }

    public function setWidth($w) { $this->width = $w; }
    public function setHeight($h) { $this->height = $h; }
    public function setBgColor($r, $g, $b) { $this->bgColor = [$r, $g, $b]; }
    public function setAxisColor($r, $g, $b) { $this->axisColor = [$r, $g, $b]; }
    public function setColors($c) { $this->colors = $c; }
    public function setData($data) { $this->data = $data; }
    public function setTitle($title) { $this->title = $title; }
    public function setMin($m) { $this->min = $m; }
    public function setMax($m) { $this->max = $m; }
    public function setBgImage($i) { $this->bgImage = $i; }


    protected function addColor($a) {
        return imagecolorallocate($this->image, $a[0], $a[1], $a[2]);
    }

    // function fillgradient($c, $c) {
        
    // }

    public function draw($format = "png") {
        if (!$this->data || !is_array($this->data)) throw new Exception("Error diagr data");
        header("Content-type: image/" . $format);
        $min = $this->min;
        $max = $this->max;
        foreach($this->data as $v) {
            if ($min === null || $v < $min) $min = $v;
            if ($max === null || $v > $max) $max = $v;
        }

        if (!$this->colors) {
            foreach($this->data as $v) {
                $this->colors[] = [rand(0, 255), rand(0, 255), rand(0, 255)];
            }
        }

        $colors = [];
        if ($this->bgImage === null) {
            $this->image = imagecreate($this->width,$this->height);
        } else {
            if (strrchr($this->bgImage, '.') === ".png") {
                $this->image = imagecreatefrompng($this->bgImage);
            } else {
                $this->image = imagecreatefromjpeg($this->bgImage);
            }
            $this->width = imagesx($this->image);
            $this->height = imagesy($this->image);
        }

        $ry = $this->fontAxis->getWidth("9") / 2;

        $margins = [
            'top' => 0,
            'bottom' => $this->fontAxis->getHeight("9") * 1.5,
            'left' => $this->fontAxis->getWidth((string)$max) + $ry * 2  + $this->fontAxis->getWidth("9") ,
            'right' =>20
        ];
        if ($this->title) {
            $margins['top'] += $this->fontTitle->getHeight($this->title);
        }
        $indexBg = $this->addColor($this->bgColor);

        $axisColor = $this->addColor($this->axisColor);
        $titleColor = $this->addColor($this->titleColor);
        foreach($this->colors as $c) {
            $colors[] = $this->addColor($c);
        }


        /*
        $f = fopen("1.txt", "a");
        $str = var_export($this->colors, true);
        fputs($f, $str);
        fputs($f, "\n");
        $str = var_export($colors, true);
        fputs($f, $str);
        fclose($f);
        */


        if ($this->title) {
            $x = ($this->width - $this->fontTitle->getWidth($this->title)) / 2;
            if ($x < 0) $x = 0;
            $this->fontTitle->draw($this->image, $x,0, $this->title, $titleColor);
        }

        //0y axis
        imageline($this->image,
            $margins['left'], $margins['top'],
            $margins['left'], $this->height - $margins['bottom'], $axisColor
        );

        //0x axis
        imageline($this->image,
            $margins['left'], $this->height - $margins['bottom'],
            $this->width - $margins['right'], $this->height - $margins['bottom'], $axisColor
        );


        $h = ($this->height - $margins['top'] - $margins['bottom']) / $this->countAxis0Y;

        $cd = ($max - $min) / $this->countAxis0Y;
        $x = $margins['left'];
        for ($i=0; $i <= $this->countAxis0Y; $i++) {
            $y = $this->height - $margins['bottom'] - $i * $h;
            $td = $min + $i * $cd;
            $this->fontAxis->draw($this->image, $x - $this->fontAxis->getWidth($td) - $ry * 2, $y - $this->fontAxis->getHeight($td) / 2, $td, $axisColor);
            if ($i > 0) {
                imageline($this->image,
                    $x, $y, $x - $this->fontAxis->getWidth("9") / 2, $y, $axisColor
                );
            }
        }


        $hy = $this->height - $margins['top'] - $margins['bottom'];
        $w = ($this->width - $margins['right'] - $margins['left']) / count($this->data);
        $wr = $w /3 ;
        $y = $this->height - $margins['bottom'];
        //for ($i=0; $i<count($this->data); $i++) {
        $i=0;
        $ccount = count($colors);
        foreach ($this->data as $v) {
            $x = $margins['left'] + $i * $w + $w / 2;
            imageline($this->image,
                $x, $y, $x, $y + $this->fontAxis->getHeight("9") / 2, $axisColor
            );
            $hd = (($v - $min) / ($max - $min)) * $hy;
            imagefilledrectangle($this->image,
                $x -$wr/2, $y - $hd, $x+$wr/2, $y , $colors[$i % $ccount]
            );
            $i++;
        }


        if (strtolower($format) == 'png') {
            imagepng($this->image);
        } else {
            //imagejpg($this->image);
        }

        imagedestroy($this->image);
    }

}

$diagr = new Diagram();
$diagr->setTitle("Diagr prod");
$diagr->setWidth(600);
$diagr->setHeight(500);
$diagr->setMin(0);
$diagr->setBgColor(200,200,200);
$diagr->setAxisColor(0,0,0);
//$diagr->setBgImage('bg.jpg');
$diagr->setBgImage('background.png');
/*
$diagr->setColors([
    [255,0,0],
    [0,255,0],
]);
*/

$diagr->setData([
    "Cat A" => 50,
    "Cat B" => 200,
    "Cat C" => 75,
    "Ca E" => 30,
    "Cat D" => 20,
]);

$diagr->draw();

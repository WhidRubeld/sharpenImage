<?php
class app{
    public $imageDir = "./images/";
    public function getImage($name){
        return imagecreatefromjpeg($this->imageDir.$name);
    }
      public function aswlcFilter($image, $radius = 4, $n = 0.65)
    {
        // $image Название изображения с форматом
        // $radius Смещение по X и Y относительно текущего пикселя для получения окрестности
        // $n Степень возведения

        // H S V - 0 1 2
        $index = 2;

        // HSV формат где V - яркость
        $data = $this->image2array($this->getImage($image), 'hsv');

        //$m Среднее значение яркости по всему изображению
        //$d Дисперсия яркости по всему изображению
        //$s Среднеквадратичное отклонение яркости по всему изображению
        $aL = $this->getElementsInRow($data, $index);
        list($m, $d, $s) =$this->getDeviation($aL);

        $newData = $data;
        foreach ($data as $y=>$row){
            foreach ($row as $x=>$pixel){
                //$ms Среднее значение яркости в окрестности
                //$ds Дисперсия яркости  в окрестности
                //$ss Среднеквадратичное отклонение яркости  в окрестности
                list($ms, $ds, $ss) =$this->getDeviation($this->getElementsInRadius($data, $x, $y, $radius, $index));

                //Коэффициент усиления резкости на основе локальной контрастности
                $Cz = abs($pixel[$index] - $ms) / ($pixel[$index] + $ms);
                //$Cz = $kn * ($m / $ss);

                // Нелинейное усиление локальной контрастности
                $fCz = pow($Cz, $n);

                // Проверка соблюдения условий для нелинейной монотонной функции
                if($fCz > $Cz && $Cz >= 0 && $Cz <= 1 && $fCz >= 0 && $fCz <= 1) {
                  // Результирующая яркость центрального пикселя
                  if($pixel[$index] < $ms) {
                    $newData[$y][$x][$index] = $ms * ((1 - $fCz)/(1 + $fCz));
                  } else {
                    $newData[$y][$x][$index] = $ms * ((1 + $fCz)/(1 - $fCz));
                  }
                }
            }
        }

        return $this->array2image($newData, 'hsv');
    }
    public function getDeviation($mas){
        //Среднее значение
        $averageV = array_sum($mas)/count($mas);
        //Дисперсия
        $dispersion = 0;
        foreach ($mas as $v) $dispersion += pow($v-$averageV, 2);
        $dispersion /= count($mas);
        //Среднеквадратичное отклонение
        $deviation = sqrt($dispersion);
        return array($averageV, $dispersion, $deviation);
    }
    public function getElementsInRadius(&$data, $xs, $ys, $radius, $index=false){
        $result = $index!==false ? array() : array(array(),array(),array());
        for($x=$xs-$radius; $x<=$xs+$radius; $x++){
            for($y=$ys-$radius; $y<=$ys+$radius; $y++){
                if(isset($data[$y][$x])){
                    if($index!==false){
                        $result[] = $data[$y][$x][$index];
                    }else {
                        $result[0][] = $data[$y][$x][0];
                        $result[1][] = $data[$y][$x][1];
                        $result[2][] = $data[$y][$x][2];
                    }
                }
            }
        }
        return $result;
    }
    public function getElementsInRow(&$data, $index=2){
        $result = array();
        $size = array(count($data[0]), count($data));
        for($y=0; $y<$size[1]; $y++){
            for($x=0; $x<$size[0]; $x++){
                $result[] = $data[$y][$x][$index];
            }
        }
        return $result;
    }
    public function image2array($im, $type=false){
        $size = array( imagesx($im), imagesy($im));
        $data = array();
        for($y=0; $y<$size[1]; $y++) {
            $data[$y] = array();
            for ($x = 0; $x < $size[0]; $x++) {
                $data[$y][$x] = $this->imagecolorat($im, $x, $y);
                if($type){
                    $method = 'rgb2'.$type;
                    $data[$y][$x] = $this->$method($data[$y][$x]);
                }
            }
        }
        return $data;
    }
    public function array2image(&$data, $type=false){
        $size = array( count($data[0]), count($data) );
        $newim = imagecreatetruecolor($size[0], $size[1]);
        for($x=0; $x<$size[0]; $x++) {
            for ($y = 0; $y < $size[1]; $y++) {
                $pixel = $data[$y][$x];
                if($type){
                    $method = $type.'2rgb';
                    $pixel = $this->$method($pixel);
                }
                imagesetpixel($newim, $x, $y, imagecolorallocate($newim, $pixel[0], $pixel[1], $pixel[2]));
            }
        }
        return $newim;
    }
    public function imagecolorat(&$im, $x, $y){
        $im_color = imagecolorat($im, $x, $y);
        $r = ($im_color >> 16) & 0xFF;
        $g = ($im_color >> 8) & 0xFF;
        $b = $im_color & 0xFF;
        return array($r, $g, $b);
    }
    public function showImage(&$im){
        header('Content-Type: image/jpeg');
        imagejpeg($im, NULL, 100);
        imagedestroy($im);
        exit;
    }
    function rgb2hsv($rgb)
    {
        list($R, $G, $B) = $rgb;
        $var_R = ($R / 255);
        $var_G = ($G / 255);
        $var_B = ($B / 255);
        $var_Min = min($var_R, $var_G, $var_B);
        $var_Max = max($var_R, $var_G, $var_B);
        $del_Max = $var_Max - $var_Min;
        $V = $var_Max;
        if ($del_Max == 0)
        {
            $H = 0;
            $S = 0;
        }
        else
        {
            $S = $del_Max / $var_Max;
            $del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
            $del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
            $del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
            if      ($var_R == $var_Max) $H = $del_B - $del_G;
            else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B;
            else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R;
            if ($H<0) $H++;
            if ($H>1) $H--;
        }
        // тон, насыщенность, яркость
        return array($H*360, $S*100, $V*100);
    }
    function hsv2rgb($hsv) {
        //https://gist.github.com/vkbo/2323023
        list($iH, $iS, $iV) = $hsv;
        if($iH < 0)   $iH = 0;   // Hue:
        if($iH > 360) $iH = 360; //   0-360
        if($iS < 0)   $iS = 0;   // Saturation:
        if($iS > 100) $iS = 100; //   0-100
        if($iV < 0)   $iV = 0;   // Lightness:
        if($iV > 100) $iV = 100; //   0-100
        $dS = $iS/100.0; // Saturation: 0.0-1.0
        $dV = $iV/100.0; // Lightness:  0.0-1.0
        $dC = $dV*$dS;   // Chroma:     0.0-1.0
        $dH = $iH/60.0;  // H-Prime:    0.0-6.0
        $dT = $dH;       // Temp variable
        while($dT >= 2.0) $dT -= 2.0; // php modulus does not work with float
        $dX = $dC*(1-abs($dT-1));     // as used in the Wikipedia link
        switch(floor($dH)) {
            case 0:
                $dR = $dC; $dG = $dX; $dB = 0.0; break;
            case 1:
                $dR = $dX; $dG = $dC; $dB = 0.0; break;
            case 2:
                $dR = 0.0; $dG = $dC; $dB = $dX; break;
            case 3:
                $dR = 0.0; $dG = $dX; $dB = $dC; break;
            case 4:
                $dR = $dX; $dG = 0.0; $dB = $dC; break;
            case 5:
                $dR = $dC; $dG = 0.0; $dB = $dX; break;
            default:
                $dR = 0.0; $dG = 0.0; $dB = 0.0; break;
        }
        $dM  = $dV - $dC;
        $dR += $dM; $dG += $dM; $dB += $dM;
        $dR *= 255; $dG *= 255; $dB *= 255;
        return array((int)$dR, (int)$dG, (int)$dB);
    }
    function rgb2hsl($rgb) {
        list( $r, $g, $b ) = $rgb;
        $oldR = $r;
        $oldG = $g;
        $oldB = $b;
        $r /= 255;
        $g /= 255;
        $b /= 255;
        $max = max( $r, $g, $b );
        $min = min( $r, $g, $b );
        $h;
        $s;
        $l = ( $max + $min ) / 2;
        $d = $max - $min;
        if( $d == 0 ){
            $h = $s = 0; // achromatic
        } else {
            $s = $d / ( 1 - abs( 2 * $l - 1 ) );
            switch( $max ){
                case $r:
                    $h = 60 * fmod( ( ( $g - $b ) / $d ), 6 );
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;
                case $g:
                    $h = 60 * ( ( $b - $r ) / $d + 2 );
                    break;
                case $b:
                    $h = 60 * ( ( $r - $g ) / $d + 4 );
                    break;
            }
        }
        return array( round( $h, 2 ), round( $s, 2 ), round( $l, 2 ) );
    }
    function hsl2rgb( $hsl){
        list($h, $s, $l) = $hsl;
        $r=$g=$b =0;
        $c = ( 1 - abs( 2 * $l - 1 ) ) * $s;
        $x = $c * ( 1 - abs( fmod( ( $h / 60 ), 2 ) - 1 ) );
        $m = $l - ( $c / 2 );
        if ( $h < 60 ) {
            $r = $c;
            $g = $x;
            $b = 0;
        } else if ( $h < 120 ) {
            $r = $x;
            $g = $c;
            $b = 0;
        } else if ( $h < 180 ) {
            $r = 0;
            $g = $c;
            $b = $x;
        } else if ( $h < 240 ) {
            $r = 0;
            $g = $x;
            $b = $c;
        } else if ( $h < 300 ) {
            $r = $x;
            $g = 0;
            $b = $c;
        } else {
            $r = $c;
            $g = 0;
            $b = $x;
        }
        $r = ( $r + $m ) * 255;
        $g = ( $g + $m ) * 255;
        $b = ( $b + $m  ) * 255;
        return array( floor( $r ), floor( $g ), floor( $b ) );
    }
    public function showImageData(&$data){
        $this->showImage($this->array2image($data));
    }
}

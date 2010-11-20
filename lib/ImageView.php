<?php 
class ImageView extends View{

	public static function rgb($int) {
		$hex=str_pad(dechex($int),$int<4096?3:6,'0',STR_PAD_LEFT);
		$len=strlen($hex);
		if ($len>6) {
			trigger_error(self::TEXT_Color);
			return FALSE;
		}
		$color=str_split($hex,$len/3);
		foreach ($color as &$hue)
			$hue=hexdec(str_repeat($hue,6/$len));
		return $color;
	}
	
    public static function thumb($file,$dimx,$dimy) {
		preg_match('/\.(gif|jp[e]*g|png)$/',$file,$ext);
		$ext[1]=str_replace('jpg','jpeg',$ext[1]);
		$img=imagecreatefromstring(file_get_contents($file));
		// Get image dimensions
		$oldx=imagesx($img);
		$oldy=imagesy($img);
		// Adjust dimensions; retain aspect ratio
		$ratio=$oldx/$oldy;
		if ($dimx<=$oldx && $dimx/$ratio<=$dimy)
			// Adjust height
			$dimy=$dimx/$ratio;
		elseif ($dimy<=$oldy && $dimy*$ratio<=$dimx)
			// Adjust width
			$dimx=$dimy*$ratio;
		else {
			// Retain size if dimensions exceed original image
			$dimx=$oldx;
			$dimy=$oldy;
		}
		// Create blank image
		$tmp=imagecreatetruecolor($dimx,$dimy);
		list($r,$g,$b)=self::rgb(0xFFF);
		$bg=imagecolorallocate($tmp,$r,$g,$b);
		imagefill($tmp,0,0,$bg);
		// Resize
		imagecopyresampled($tmp,$img,0,0,0,0,$dimx,$dimy,$oldx,$oldy);
		// Make the background transparent
		imagecolortransparent($tmp,$bg);
		Slim::response()->header('Content-Type','image/'.$ext[1]);
		// Send output in same graphics format as original
		eval('image'.$ext[1].'($tmp);');
	}
}
?>

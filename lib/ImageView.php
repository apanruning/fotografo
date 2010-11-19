<?php 
class ImageView extends View{

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
		list($r,$g,$b)=self::rgb(self::$global['BGCOLOR']);
		$bg=imagecolorallocate($tmp,$r,$g,$b);
		imagefill($tmp,0,0,$bg);
		// Resize
		imagecopyresampled($tmp,$img,0,0,0,0,$dimx,$dimy,$oldx,$oldy);
		// Make the background transparent
		imagecolortransparent($tmp,$bg);
		if (PHP_SAPI!='cli')
			header('Content-Type: image/'.$ext[1]);
		// Send output in same graphics format as original
		eval('image'.$ext[1].'($tmp);');
	}

}
?>

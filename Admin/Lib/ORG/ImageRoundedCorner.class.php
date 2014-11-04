<?php
class ImageRoundedCorner {
	private $_r;
	private $_g;
	private $_b;
	private $_image_path;
	private $_radius;

	function __construct($image_path, $radius, $r = 255, $g = 255, $b = 255) {
		$this->_image_path = $image_path;
		$this->_radius = $radius;
		$this->_r = (int)$r;
		$this->_g = (int)$g;
		$this->_b = (int)$b;
	}

	private function _get_lt_rounder_corner() {
		$radius = $this->_radius;
		$img = imagecreatetruecolor($radius, $radius);
		$bgcolor = imagecolorallocate($img, $this->_r, $this->_g, $this->_b);
		$fgcolor = imagecolorallocate($img, 0, 0, 0);
		imagefill($img, 0, 0, $bgcolor);
		imagefilledarc($img, $radius, $radius, $radius*2, $radius*2, 180, 270, $fgcolor, IMG_ARC_PIE);
		imagecolortransparent($img, $fgcolor);
		return $img;
	}

	private function _load_source_image() {
		$ext = substr($this->_image_path, strrpos($this->_image_path, '.'));
		if (empty($ext)) {
			return false;
		}
		switch(strtolower($ext)) {
			case '.jpg':
				$img = @imagecreatefromjpeg($this->_image_path);
				break;
			case '.gif':
				$img = @imagecreatefromgif($this->_image_path);
				break;
			case '.png':
				$img = @imagecreatefrompng($this->_image_path);
				break;
			default:
				return false;
		}

		return $img;
	}

	public function round_it($p_dst_img='') {
		// load the source image
		$src_image = $this->_load_source_image();
		if ($src_image === false) {
			return false;
		}
		$image_width = imagesx($src_image);
		$image_height = imagesy($src_image);

		// create a new image, with src_width, src_height, and fill it with transparent color
		$image = imagecreatetruecolor($image_width, $image_height);
		// debug 
		$alphacolor = imagecolorallocatealpha($image, $this->_r, $this->_g, $this->_b, 127);
		imagealphablending($image, false);
		imagesavealpha($image, true);
		imagefilledrectangle($image, 0, 0, $image_width, $image_height, $alphacolor);
		
		imagefill($image, 0, 0, $alphacolor);
		imagecopyresampled($image, $src_image, 0, 0, 0, 0, $image_width, $image_height, $image_width, $image_height);
		
		$radius = $this->_radius;
		
		imagearc($image, $radius-1, $radius-1, $radius*2, $radius*2, 180, 270, $alphacolor);
		imagefilltoborder($image, 0, 0, $alphacolor, $alphacolor);
		imagearc($image, $image_width-$radius, $radius-1, $radius*2, $radius*2, 270, 0, $alphacolor);
		imagefilltoborder($image, $image_width-1, 0, $alphacolor, $alphacolor);
		imagearc($image, $radius-1, $image_height-$radius, $radius*2, $radius*2, 90, 180, $alphacolor);
		imagefilltoborder($image, 0, $image_height-1, $alphacolor, $alphacolor);
		imagearc($image, $image_width-$radius, $image_height-$radius, $radius*2, $radius*2, 0, 90, $alphacolor);
		imagefilltoborder($image, $image_width-1, $image_height-1, $alphacolor, $alphacolor);
		imagealphablending($image, true);
		imagecolortransparent($image, $alphacolor);
		
// 		imageantialias($image, true);
// 		$trans_color = imagecolorallocate($image, $this->_r, $this->_g, $this->_b);
// 		imagefill($image, 0, 0, $trans_color);

// 		// then overwirte the source image to the new created image
// 		#imagecopymerge($image, $src_image, 0, 0, 0, 0, $image_width, $image_height, 100);
// 		imagecopy($image, $src_image, 0, 0, 0, 0, $image_width, $image_height);
// 		#imagecolortransparent($image, $trans_color); 
// 		#header('Content-Type: image/png');imagepng($image);exit;

// 		// then just copy all the rounded corner images to the 4 corners
// 		$radius = $this->_radius;
// 		// lt
// 		$lt_corner = $this->_get_lt_rounder_corner();
// 		imagecopymerge($image, $lt_corner, 0, 0, 0, 0, $radius, $radius, 100);
// 		// lb
// 		$lb_corner = imagerotate($lt_corner, 90, $trans_color);
// 		imagecopymerge($image, $lb_corner, 0, $image_height - $radius, 0, 0, $radius, $radius, 100);
// 		// rb
// 		$rb_corner = imagerotate($lt_corner, 180, $trans_color);
// 		imagecopymerge($image, $rb_corner, $image_width - $radius, $image_height - $radius, 0, 0, $radius, $radius, 100);
// 		// rt
// 		$rt_corner = imagerotate($lt_corner, 270, $trans_color);
// 		imagecopymerge($image, $rt_corner, $image_width - $radius, 0, 0, 0, $radius, $radius, 100);

// 		// set the transparency
// 		imagecolortransparent($image, $trans_color);
		if($p_dst_img) {
			return imagepng($image, $p_dst_img);
		} else {
			header('Content-Type: image/png');
			imagepng($image);
		}

		imagedestroy($src_image);
		imagedestroy($image);
// 		imagedestroy($lt_corner);
// 		imagedestroy($lb_corner);
// 		imagedestroy($rb_corner);
// 		imagedestroy($rt_corner);
	}
}
?>
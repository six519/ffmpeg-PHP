<?php
/**
 * 
 * @author Ferdinand E. Silva (ferdinandsilva@ferdinandsilva.com)
 *
 */
class FfMpegFileNotExistsException extends Exception {}

class FfMpegInvalidFileException extends Exception {}

class FfMpeg {
	
	private $file = "";
	private $ffmpegMessage = array();
	//Accepted mime types in array
	private $validFiles = array('audio/mpeg','audio/mpeg3','audio/x-mpeg-3','video/mpeg','video/x-mpeg','video/x-msvideo','application/x-troff-msvideo','video/avi','video/msvideo','audio/wav','audio/x-wav','video/quicktime','audio/aiff','audio/x-aiff','audio/mpeg','audio/x-mpeg','video/mpeg','video/x-mpeg','video/x-mpeq2a'); //Array of valid files
	
	function __construct($file) {
		$this->file = $file;
		//Check if file is existing, if not, then raise an exception
		if(!$this->existed()) {
			throw new FfMpegFileNotExistsException("File Does Not Exists");
		} else {
			//Check if valid file
			if(!$this->validFile()) {
				throw new FfMpegInvalidFileException("Invalid File");
			} else {
				//Fetch infos
				$this->getInfos();	
			}
		}
	}
	
	private function existed() {
		//Check if file is existing
		if(file_exists($this->file)) {
			
			if(is_file($this->file)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	private function validFile() {
		
		return in_array(mime_content_type($this->file), $this->validFiles);
	}
	
	private function getInfos() {
		exec("ffmpeg -i " . preg_replace("/'/", "\'", preg_replace('/ /', '\ ', $this->file)) . " 2>&1", $this->ffmpegMessage);
	}
	
	function getDuration() {
		$duration = "00:00:00.00";
		for($x=0;$x<count($this->ffmpegMessage);$x++) {
			if(preg_match("/Duration: /", $this->ffmpegMessage[$x])) {
				$duration = preg_split('/,/', $this->ffmpegMessage[$x]);
				$duration = preg_replace('/Duration: /', '', $duration[0]);
				break;	
			}
		}
		
		return $duration;
	}
	
	function getHours() {
		//Get Hours (Integer)
		$hours = preg_split('/:/', $this->getDuration());
		return (int)$hours[0];
	}
	
	function getMinutes() {
		//Get Minutes (Integer)
		$minutes = preg_split('/:/', $this->getDuration());
		return (int)$minutes[1];
	}
	
	function getSeconds() {
		//Get Seconds (Integer)
		$seconds = preg_split('/:/', $this->getDuration());
		return (int)$seconds[2];	
	}
	
}


?>
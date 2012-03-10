<?php
define("HISTORY_OVERWRITE",1);
define("HISTORY_PREFERENCES",2);
define("HISTORY_ARRAY",4);

class History {
	public $id = '';
	private $history = array();	
	private $options = array();
	private $final = array();
	private $preferences = array();

	public function __construct($id,$opt=array(),$pref=array()) {
		$this->id = $id;
		$this->setOptions($opt);
		$this->setPreferences($pref);
	} 

	public function add($hist,$removeDuplicates=false) {
		if($removeDuplicates) {
			if($this->history[count($this->history)-1] != $this) {
				$this->history[] = $hist;
			}
		} else $this->history[] = $hist;

		$this->_mergeElement($hist);

		return $this->final;
	} 

	public function setOptions($opt) {
		$this->options = $opt;
		return $opt;
	}

	public function getHistory() {
		return $this->final;
	}

	public function setPreferences($pref) {
		$this->preferences = $pref;
		return $pref;
	}

	public function setHistory($hist) {
		$this->history = $hist;
		return $hist;
	}

	public function merge() {
		foreach($this->history as $hist) {
			$this->_mergeElement($hist);
		} 
		return $this->final;
	}

	private function _mergeElement($hist) {
		foreach($hist as $key=>$elem) {
			if($this->_checkPermission($key,HISTORY_OVERWRITE)) {
				if($this->_checkPermission($key,HISTORY_ARRAY)) {
						$this->final[$key][] = $elem;
				} else {
					if(array_key_exists($key,$this->final)) {
						$this->final[$key] = ($this->_checkPermission($key,HISTORY_PREFERENCES)) ? $this->_checkPreferences($key,$elem,$this->final[$key]) : $elem;
					} else $this->final[$key] = $elem;
				} 
			} else $this->final[$key][] = $elem;
		}
/*
		foreach($this->options as $key=>$elem) {
			if($this->_checkPermission($key,HISTORY_OVERWRITE|HISTORY_ARRAY)) {
				$size = count($this->final[$key]);

// double loop checks values for equality
				for($i = 0; $i < count($this->final[$key])-1; $i++) {
					for($j = $i+1; $j < count($this->final[$key]); $j++) {

						# wenn die 2 elemente identisch sind
						if($this->final[$key][$i] == $this->final[$key][$j]) {
							foreach($this->final as $k=>$e) {
								if(is_array($e)) {
									$this->final[$k][$i] = ($this->_checkPermission($k,HISTORY_PREFERENCES)) ? $this->_checkPreferences($k,$this->final[$k][$j],$this->final[$k][$i]) : $this->final[$k][$j];
									unset($this->final[$k][$j]);
								}
							}
						}
					}
				}
			}
		}
//*/
	}

	private function _checkPermission($key,$option) { 
		if(!array_key_exists($key,$this->options)) return false;
		return ($option & $this->options[$key]) ? true : false;
	}

	private function _checkPreferences($key,$e1,$e2) {
		if(array_key_exists($key,$this->preferences)) {
			foreach($this->preferences[$key] as $e) {
				if($e1 == $e or $e2 == $e) return $e;
			}
		} else return $e1;
	}
}  
?>

<?php
class Lead{
        //this is the class of every individual lead
        //every time an object is instantiated it represents a single lead
        //
	//class attributes
	public $BusinessName;
        public $BusinessUrl;
        public $BusinessLocation;

	//class construct
	public function __construct($name, $url, $location){
		$this->BusinessName=$name;
                $this->BusinessUrl=$url;
                $this->BusinessLocation=$location;
	}
}

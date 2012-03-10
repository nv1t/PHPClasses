<?php
/**
* Class for managing a socket
*/

class Socket {
    /**
     * handler for the socket
     *
     * @var resource
     */
    private $handler;


    /**
     * Constructor of the class...if host and port are set opens a socket
     *
     * @param    string    $host    host for the socket
     * @param    string    $port    port for the socket
     */
    function __construct( $host='',$port='' ) {
        if(!empty($host) AND !empty($port)) {
            return $this->connect($host,$port);
        }
    }


    /**
     * Creates a socket to a certain host with a certain port
     *
     * @param    string    $host        host for the socket
     * @param    string    $port        port for the socket
     * @param    int        $timeout    timeout for the socket
     * 
     * @return resource $this->handler handler for the socket or bool false
     */
    function connect( $host, $port,$timeout=30 ) {
        $this->handler = @fsockopen( $host, $port, $errno, $errstr, $timeout );

        if($this->handler) {
            return $this->handler;
        } else {
            return false;
        }
    }

    
    /**
     * Reads from the socket
     *
     * @param int $bytes Number of bytes to read from the socket...if not set it will read everything
     *
     * @return string $buffer The output of the socket
     *
     */
    function read($bytes='') {
        $buffer = '';
        if(empty($bytes)) {
            while (!feof($this->handler)) {
                $buffer .= @fgets($this->handler, 1024);
            }
        } else {
            $buffer = @fgets($this->handler,$bytes);
        }

        return $buffer;
    }

    
    /**
     * Writes on the socket
     *
     * @param string $str The string you want to write on the socket
     *
     * @return bool always true
     *
     */
    function send($str) {
        @fwrite($this->handler,$str);
        
        return true;
    }
    
    
    /**
     * Closes the socket handler
     */
    function close( ) {
        @fclose($this->handler);
    }
}
?>

<?php
class SurPHPException extends Exception {
    
    function __construct($message, $code){
        parent::__construct($message, $code);
    }
    function __toString(){
        $trace          = $this->getTrace();        
        $this->class    = $trace[0]['class'];
        $this->function = $trace[0]['function'];
        $this->file     = $trace[0]['file'];
        $this->line     = $trace[0]['line'];
        $traceInfo      = '';
        foreach($trace as $t) {
            if (isset($t['file'])){
                $traceInfo .= $t['file'].' ('.$t['line'].') ';
            }
            $traceInfo .= $t['class'].$t['type'].$t['function'];
            $traceInfo .= '('.implode(', ', $t['args']);
            $traceInfo .=")\n";
        }
        $error['message']   = '['.$this->code.']'.$this->message;
        $error['type']      = $this->type;
        $error['class']     = $this->class;
        $error['function']  = $this->function;
        $error['file']      = $this->file;
        $error['line']      = $this->line;
        $error['trace']     = $traceInfo;
        return $error;
    }
}
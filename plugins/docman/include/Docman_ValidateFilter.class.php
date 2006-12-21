<?php

class Docman_ValidateFilterFactory {
    function Docman_ValidateFilterFactory() {
        
    }

    function &getFromFilter(&$filter) {
        $f = null;
        if(is_a($filter, 'Docman_FilterDate')) {
            $f = new Docman_ValidateFilterDate($filter);
        }
        if(is_a($filter, 'Docman_FilterOrdering')) {
            $f = new Docman_ValidateFilterOrdering($filter);
        }
        return $f;
    }

}

class Docman_ValidateFilter {
    var $filter;
    var $message;
    var $isValid;
    
    function Docman_ValidateFilter(&$filter) {
        $this->filter =& $filter;
        $this->message = '';
        $this->isValid = null; 
    }

    function validate() {
        return $this->isValid;
    }

    function getMessage() {
        return $this->message;
    }
}

class Docman_ValidateFilterDate extends Docman_ValidateFilter {

    function validate() {
        if($this->isValid === null) {
            $this->isValid = false;
            if($this->filter->getValue() == '') {
                $this->isValid = true;
            }
            elseif(preg_match('/[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}/',
                              $this->filter->getValue())) {
                $this->isValid = true;                
            }
            else {
                $today = date("Y-n-j");
                $this->message = $GLOBALS['Language']->getText('plugin_docman', 'filters_date_message', array($this->filter->getTitle(), $today));
            }
        }
        return $this->isValid;
    }
}

class Docman_ValidateFilterOrdering extends Docman_ValidateFilter {

    function validate() {
        if($this->isValid === null) {
            // Default value
            if($this->filter->getValue() == '') {
                $this->isValid = true;
                $this->filter->setValue('0');
            }
            
            if(!is_numeric($this->filter->getValue())) {
                $this->isValid = false;                
                $this->message = $GLOBALS['Language']->getText('plugin_docman', 'filters_ordering_message', $this->filter->getTitle());
            }
            else {
                $this->isValid = true;
            }
        }
        return $this->isValid;
    }
}

?>

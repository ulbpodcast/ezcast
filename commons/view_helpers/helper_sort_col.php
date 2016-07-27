<?php

/**
 * 
 */
class Sort_colonne {
    
    
    private $currentSortCol;
    private $orderOfSort;
    
    
    public function __construct($currentSortCol= "", $orderOfSort = "ASC") {
        $this->currentSortCol = $currentSortCol;
        
        if($orderOfSort == "") {
            $orderOfSort = "ASC";
        } else if($orderOfSort != "ASC" && $orderOfSort != "DESC") {
            throw InvalidArgumentException("Order of sort can only is ASC or DESC");
        }
        
        $this->orderOfSort = $orderOfSort;
        
    }
    
    
    public function insertHiddenInput() {
        return 
            '<input type="hidden" name="col" 
                value="'.$this->currentSortCol.'" />' .
            '<input type="hidden" name="order"
                value="'.$this->orderOfSort.'" />';
    }
    
    
    /**
     * Return a th balise HTML code with an sort system
     * 
     * @param String $name of the colonne name
     * @param String $title of the colonne
     * @return string with the HTML code
     */
    public function insertThSort($name, $title) {
        $res = '<th data-col="'.$name.'"';
        if($this->currentSortCol == $name) {
            $res .= ' data-order="'.$this->orderOfSort.'" ';
        }
        $res .= ' style="cursor:pointer;">'.$title." ";
        
        if($this->currentSortCol == $name) {
            if($this->orderOfSort == 'ASC') {
                $res .= '<span class="glyphicon glyphicon-chevron-down"></span>';
            } else {
                $res .= '<span class="glyphicon glyphicon-chevron-up"></span>';
            }
        }
        $res .= '</th>';
        
        return $res;
    }
    
    /**
     * Get the col who will be use for sort
     * 
     * @return String name of the col
     */
    public function getCurrentSortCol() {
        return $this->currentSortCol;
    }
    
    /**
     * Get the order of the sort (DESC or ASC)
     * 
     * @return String who specific the order
     */
    public function getOrderSort() {
        return $this->orderOfSort;
    }
    
    
}


<?php

/**
 * Class who help you to create pagination system
 * 
 * - Initialise (in controller) with the constructor
 * - Adapt you SQL Request with the getLimit() function and add `SQL_CALC_FOUND_ROWS`
 *   to the selected items
 * - Add the number of total row
 * - Insert the HTML code (in the view) with the function insert on the object 
 *   that you have initialise
 * - The javascript code is automaticly loaded when Pagination object exist
 * 
 * In you DataBase Request you must add: LIMIT 
 * 
 */
class Pagination {
    
    private $maxPage;
    private $currentPage;
    private $elemPerPage;
    
    
    /**
     * Init the pagination system
     * 
     * @param int $nbrItem nbr of all the item
     * @param int $currentPage nbr of the current page
     * @param int $itemPerPage nbr of item per page
     */
    public function __construct($currentPage = 1, $itemPerPage = 20) {
        $this->elemPerPage = intval($itemPerPage);
        $this->currentPage = intval($currentPage);
    }
    
    /**
     * Define the number of all items
     * 
     * @param type $allItem
     */
    public function setTotalItem($allItem) {
        $this->maxPage = ceil(intval($allItem) / $this->elemPerPage);
    }
    
    
    /**
     * Insert the HTML to view the page system
     */
    public function insert() {
        
        if($this->maxPage > 0) {
            echo '
        <div class="text-center">
            <ul class="pagination">
                <li';
                if($this->currentPage == 1) { 
                    echo ' class="disabled"';   
                } 
                echo '>
                    <a href="#" data-page="'.($this->currentPage-1).'">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li';
                if($this->currentPage == 1) {
                    echo ' class="active"';
                }
                echo '><a href="#" data-page="1">1</a></li>';
        
        if($this->currentPage > 5) {
           echo '<li><a href="#" data-page="0">...</a></li>';
        }
           
        $start = ($this->currentPage > 4) ? ($this->currentPage-3) : 2;
           
        for($i = $start; $i < $this->maxPage && $i < $start+7; ++$i) {
            echo '<li';
            if($this->currentPage == $i) {
                echo ' class="active"'; 
            }
            echo '>'
                . '<a href="#" data-page="'.$i.'">'.$i.'</a>'
            . '</li>';
        }
        
        if(($this->currentPage+7) < $this->maxPage) {
           echo '<li><a href="#" data-page="0">...</a></li>';
        }
           
        if($this->maxPage != 1) {
            echo '<li';
            if($this->currentPage == $this->maxPage) { 
                echo ' class="active"'; 
            } 
            echo '>
                <a href="#" data-page="'.$this->maxPage.'">'.$this->maxPage.'</a>
            </li>';
        }
        echo '<li>
            <a href="#" data-page="'.($this->currentPage+1).'">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
      </ul>
    </div>';
        
            echo '<script>
                var maxPage = '.$this->maxPage.'
            </script>';
        

        }
    
    
    
    }
    
    /**
     * Return the SQL code to limit the request
     * 
     * @return String who contains SQL request
     */
    public function getLimit() {
        return " LIMIT " . ($this->currentPage-1) * $this->elemPerPage . "," . $this->elemPerPage;
    }
    
    
}



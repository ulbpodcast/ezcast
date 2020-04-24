<?php

require_once 'helper_url.php';

/**
 * Helper for sort the result by column (adapt for Bootstrap 3)
 *
 * How to use:
 * - Initialise (in controller) with the constructor
 * - Adapt you SQL Request with functions getCurrentSortCol and getOrderSort
 * - In HTML Table, use insertThSort() to add an specific column order
 *
 * This system use GET variable with attributs `order` and `col` to send informations
 * (like: $_GET['order'] and $_GET['col'])
 *
 */
class Sort_colonne
{
    private $currentSortCol;
    private $orderOfSort;
    
    
    public function __construct($currentSortCol= "", $orderOfSort = "DESC")
    {
        $this->currentSortCol = $currentSortCol;
        
        if ($orderOfSort == "") {
            $orderOfSort = "ASC";
        } elseif ($orderOfSort != "ASC" && $orderOfSort != "DESC") {
            throw InvalidArgumentException("Order of sort can only is ASC or DESC");
        }
        
        $this->orderOfSort = $orderOfSort;
    }
    
    /**
     * Return the specific icon according to the already defined
     */
    private function getIcon()
    {
        if ($this->orderOfSort == 'ASC') {
            return '<span class="glyphicon glyphicon-chevron-down"></span>';
        }
        return '<span class="glyphicon glyphicon-chevron-up"></span>';
    }
    
    /**
     * Get the reverse order of the sort
     */
    private function getReverseOrder()
    {
        if ($this->orderOfSort == 'ASC') {
            return 'DESC';
        }
        return 'ASC';
    }
    
    
    /**
     * Return a th balise HTML code with an sort system
     *
     * @param String $name of the colonne name
     * @param String $title of the colonne
     * @return string with the HTML code
     */
    public function insertThSort($name, $title, $class = null, $style = null)
    {
        $res = '<th';
        if ($class != null) {
            $res .= ' class="'.$class.'"';
        }
        if ($style != null) {
            $res .= ' style="'.$style.'"';
        }
        $res .= '>';
        
        $res .= '<a style="color:black" href="';
        $res .= url_post_replace_multiple(
                    array(
                        'col' => $name,
                        'order' => $this->getReverseOrder())
                    );
        $res .= '"';
        $res .= ' style="cursor:pointer;">'.$title." ";
        
        if ($this->currentSortCol == $name) {
            $res .= $this->getIcon();
        }
        $res .= "</a>";
        $res .= '</th>';
        
        return $res;
    }
    
    /**
     * Get the col who will be use for sort
     *
     * @return String name of the col
     */
    public function getCurrentSortCol()
    {
        return $this->currentSortCol;
    }
    
    /**
     * Get the order of the sort (DESC or ASC)
     *
     * @return String who specific the order
     */
    public function getOrderSort()
    {
        return $this->orderOfSort;
    }
}

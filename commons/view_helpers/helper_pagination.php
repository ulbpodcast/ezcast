<?php

require_once 'helper_url.php';

/**
 * Class who help you to create pagination system (adapt for Bootstrap 3)
 *
 * How to use:
 * - Initialise (in controller) with the constructor
 * - Adapt you SQL Request with functions getStartElem, getElemPerPage and
 *   add `SQL_CALC_FOUND_ROWS` to the selected items
 * - Add the number of total row (with setTotalItem) with the result of your request
 * - Insert the HTML code (in the view) with the function insert() on the object
 *   that you have initialise
 *
 * This system use GET variable with the attribut `page` to send informations
 * (like: $_GET['page'])
 *
 */
class Pagination
{
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
    public function __construct($currentPage = 1, $itemPerPage = 20)
    {
        $this->elemPerPage = intval($itemPerPage);
        $this->currentPage = intval($currentPage);
    }
    
    /**
     * Define the number of all items
     *
     * @param type $allItem
     */
    public function setTotalItem($allItem)
    {
        $this->maxPage = ceil(intval($allItem) / $this->elemPerPage);
    }
    
    
    /**
     * Insert the HTML to view the page system
     *
     * @param String $type Type of request (POST or GET)
     */
    public function insert($type = 'GET')
    {
        if ($this->maxPage > 0) {
            if (strtoupper($type) == 'POST') {
                echo '<input type="hidden" name="page" class="nbr_page" id="nbr_page" value="1">';
            }
            
            
            echo '
            <div class="text-center">
              <ul class="pagination">';
            if ($this->currentPage == 1) {
                echo
                        '<li class="disabled">
                            <span aria-hidden="true">&laquo;</span>
                        </li>';
            } else {
                echo '<li>';
                echo $this->getLink($this->currentPage-1, '<span aria-hidden="true">&laquo;</span>', $type);
                echo '</li>';
            }

            echo '<li';
            if ($this->currentPage == 1) {
                echo ' class="active"';
            }
            echo '>';
            echo $this->getLink(1, '1', $type);
            echo '</li>';
        
            if ($this->currentPage > 5) {
                echo '<li><span>...</span></li>';
            }
           
            $start = ($this->currentPage > 4) ? ($this->currentPage-3) : 2;

            for ($i = $start; $i < $this->maxPage && $i < $start+7; ++$i) {
                echo '<li';
                if ($this->currentPage == $i) {
                    echo ' class="active"';
                }
                echo '>';
                echo $this->getLink($i, $i, $type);
                echo '</li>';
            }
        
            if (($this->currentPage+7) < $this->maxPage) {
                echo '<li><span>...</span></li>';
            }
           
            if ($this->maxPage != 1) {
                echo '<li';
                if ($this->currentPage == $this->maxPage) {
                    echo ' class="active"';
                }
                echo '>';
                echo $this->getLink($this->maxPage, $this->maxPage, $type);
                echo '</li>';
            }
                
            if ($this->currentPage == $this->maxPage) {
                echo
                        '<li class="disabled">
                            <span aria-hidden="true">&raquo;</span>
                        </li>';
            } else {
                echo '<li>';
                echo $this->getLink($this->currentPage+1, '<span aria-hidden="true">&raquo;</span>', $type);
                echo '</li>';
            }
                
            echo '
              </ul>
            </div>';
        }
    }
    
    private function getLink($pageNumber, $text, $type = 'GET')
    {
        if (strtoupper($type) == 'POST') {
            return '<span onClick="$(\'.nbr_page#nbr_page\').val(\''.$pageNumber.'\');$(\'form\').submit()"'
                    . 'style="cursor: pointer;">'.
                    $text.'</span>';
        } elseif (strtoupper($type) == 'GET') {
            return '<a href="'.url_post_replace('page', $pageNumber).'">
                        '.$text.'
                    </a>';
        } else {
            throw new InvalidArgumentException('Type of page send is not good. Allow: GET or POST (actually: '.$type.')');
        }
    }
    
    
    
    /**
     * Get the first element to be recoverd by SQL
     *
     * @return int the number of the first element
     */
    public function getStartElem()
    {
        return ($this->currentPage-1)*$this->elemPerPage;
    }
    
    /**
     * Get the number of item in one page
     *
     * @return int nbr elem
     */
    public function getElemPerPage()
    {
        return $this->elemPerPage;
    }
}

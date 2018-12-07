<?php

namespace Blaze\Pagination;

/**
* whiteGold - mini PHP Framework
*
* @package whiteGold
* @author Farawe iLyas <faraweilyas@gmail.com>
* @link http://faraweilyas.me
*
* Paginate Class
*/
class Paginate
{
    public $currentPage;
    public $perPage;
    public $adjacent;
    public $totalCount;

    /**
    * Constructor sets pagination properties
    * @param int $page
    * @param int $perPage
    * @param int $adjacent
    * @param int $totalCount
    */
    public function __construct (int $currentPage=1, int $perPage=20, int $adjacent=1, int $totalCount=0)
    {
        $this->setPagination($currentPage, $perPage, $adjacent, $totalCount);
    }

    /**
    * Sets pagination properties
    * @param int $page
    * @param int $perPage
    * @param int $adjacent
    * @param int $totalCount
    */
    public function setPagination (int $currentPage=1, int $perPage=20, int $adjacent=1, int $totalCount=0)
    {
        $this->currentPage     	= $currentPage;
        $this->perPage         	= $perPage;
        $this->adjacent 		= $adjacent;
        $this->totalCount      	= $totalCount;
    }

    /**
    * Sets Total Count
    * @param int $totalCount
    */
    public function setTotalCount (int $totalCount=0)
    {
        $this->totalCount = $totalCount;
    }

    /**
    * Sets Adjacent
    * @param int $adjacent
    */
    public function setAdjacent (int $adjacent=0)
    {
        $this->adjacent = $adjacent;
    }

    /**
    * Calculates the Offset.
    * Assuming 20 items per page:
    * page 1 has an offset of 0    (1-1) * 20
    * page 2 has an offset of 20   (2-1) * 20
    * in other words, page 2 starts with item 21
    * @return int
    */
    final public function offset () : int 
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    /**
    * Calculate Total Pages
    * @return int
    */
    final public function totalPages () : int
    {
        return (int) ceil($this->totalCount / $this->perPage);
    }

    /**
    * Get Last Page
    * @return int
    */
    final public function lastPage () : int
    {
        return (int) $this->totalPages();
    }

    /**
    * Calculate page before last page
    * @return int
    */
    final public function pageBeforeLastPage () : int
    {
        return (int) $this->lastPage() - 1;
    }

    /**
    * Calculate Previous Page.
    * @return int
    */
    final public function previousPage () : int
    {
        return (int) $this->currentPage - 1;
    }

    /**
    * Calculate Next Page.
    * @return int
    */
    final public function nextPage () : int
    {
        return (int) $this->currentPage + 1;
    }

    /**
    * Check if there's Previous Page.
    * @return bool
    */
    final public function hasPreviousPage () : bool
    {
        return ($this->previousPage() >= 1) ? TRUE : FALSE;
    }

    /**
    * Check if there's Next Page.
    * @return bool
    */
    final public function hasNextPage () : bool
    {
        return ($this->nextPage() <= $this->totalPages()) ? TRUE : FALSE;
    }

    /**
    * Check if there are enough pages, then do not bother hiding some
    * @return bool
    */
    final public function notEnoughPages () : bool
    {
        return ($this->lastPage() < 7 + ($this->adjacent * 2)) ? TRUE : FALSE;
    }

    /**
    * Check if there are enough pages, then hide some
    * @return bool
    */
    final public function hasEnoughPages () : bool
    {
        return ($this->lastPage() >= 7 + ($this->adjacent * 2)) ? TRUE : FALSE;
    }

    /**
    * Check if close to beginning, only hide later pages
    * @return bool
    */
    final public function closeToBeginning () : bool
    {
        return ($this->currentPage < 1 + ($this->adjacent * 3)) ? TRUE : FALSE;
    }

    /**
    * Check if in middle, then hide some front and some back
    * @return bool
    */
    final public function inMiddle () : bool
    {
        return ($this->lastPage() - ($this->adjacent * 2) > $this->currentPage && $this->currentPage > ($this->adjacent * 2)) ? TRUE : FALSE;
    }

    /**
    * Check if close to end, then only hide early pages
    * @return bool
    */
    final public function closeToEnd () : bool
    {
        return (!$this->closeToBeginning() && !$this->inMiddle()) ? TRUE : FALSE;
    }
}

<?php

namespace Blaze\Pagination;

/**
 * whiteGold - mini PHP Framework
 *
 * @package whiteGold
 * @author Farawe iLyas <faraweilyas@gmail.com>
 * @link https://faraweilyas.com
 *
 * Paginate Class
 */
class Paginate
{
    public $currentPage;
    public $perPage;
    public $adjacent;
    public $totalCount;
    public $template;
    public $queryLink;

    /**
    * Constructor sets pagination properties
    * @param int $currentPage
    * @param int $perPage
    * @param int $adjacent
    * @param int $totalCount
    */
    public function __construct(int $currentPage=1, int $perPage=20, int $adjacent=1, int $totalCount=0)
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
    public function setPagination(int $currentPage=1, int $perPage=20, int $adjacent=1, int $totalCount=0)
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
    public function setTotalCount(int $totalCount=0)
    {
        $this->totalCount = $totalCount;
    }

    /**
    * Sets Adjacent
    * @param int $adjacent
    */
    public function setAdjacent(int $adjacent=0)
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
    final public function offset() : int 
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    /**
    * Calculate Total Pages
    * @return int
    */
    final public function totalPages() : int
    {
        return (int) ceil($this->totalCount / $this->perPage);
    }

    /**
    * Get Last Page
    * @return int
    */
    final public function lastPage() : int
    {
        return (int) $this->totalPages();
    }

    /**
    * Calculate page before last page
    * @return int
    */
    final public function pageBeforeLastPage() : int
    {
        return (int) $this->lastPage() - 1;
    }

    /**
    * Calculate Previous Page.
    * @return int
    */
    final public function previousPage() : int
    {
        return (int) $this->currentPage - 1;
    }

    /**
    * Calculate Next Page.
    * @return int
    */
    final public function nextPage() : int
    {
        return (int) $this->currentPage + 1;
    }

    /**
    * Check if there's Previous Page.
    * @return bool
    */
    final public function hasPreviousPage() : bool
    {
        return ($this->previousPage() >= 1) ? TRUE : FALSE;
    }

    /**
    * Check if there's Next Page.
    * @return bool
    */
    final public function hasNextPage() : bool
    {
        return ($this->nextPage() <= $this->totalPages()) ? TRUE : FALSE;
    }

    /**
    * Check if there are enough pages, then do not bother hiding some
    * @return bool
    */
    final public function notEnoughPages() : bool
    {
        return ($this->lastPage() < 7 + ($this->adjacent * 2)) ? TRUE : FALSE;
    }

    /**
    * Check if there are enough pages, then hide some
    * @return bool
    */
    final public function hasEnoughPages() : bool
    {
        return ($this->lastPage() >= 7 + ($this->adjacent * 2)) ? TRUE : FALSE;
    }

    /**
    * Check if close to beginning, only hide later pages
    * @return bool
    */
    final public function closeToBeginning() : bool
    {
        return ($this->currentPage < 1 + ($this->adjacent * 3)) ? TRUE : FALSE;
    }

    /**
    * Check if in middle, then hide some front and some back
    * @return bool
    */
    final public function inMiddle() : bool
    {
        return ($this->lastPage() - ($this->adjacent * 2) > $this->currentPage && $this->currentPage > ($this->adjacent * 2)) ? TRUE : FALSE;
    }

    /**
     * Check if close to end, then only hide early pages
     * @return bool
     */
    final public function closeToEnd() : bool
    {
        return (!$this->closeToBeginning() && !$this->inMiddle()) ? TRUE : FALSE;
    }

	/**
	 * Get SQL limit query
	 * @return string
	 */
	final public function getSQLLimitQuery() : string
	{
		return "LIMIT {$this->perPage} OFFSET ".$this->offset();
	}

	/**
	 * To display links from an object to act as a string
	 * Magic __toString().
	 * @return string 
	 */
	public function __toString()
	{
		return $this->displayLinks();
	}

	/**
	 * Display links
	 * @return string
	 */
	final public function displayLinks() : string
	{
		return $this->getTemplate()->refineLink($this->generateLinks());
	}

	/**
	 * Set template
	 * @param BaseTemplate $template
	 * @return Paginate $this
	 */
	final public function setTemplate($template)
	{
		$this->template = $template;
		return $this;
	}

	/**
	 * Get template
	 * @return BaseTemplate object
	 */
	final public function getTemplate()
	{
		return $this->template = is_null($this->template) ? new BaseTemplate : $this->template;
	}

	/**
	 * Set query link
	 * @param string $queryLink
	 * @return Paginate $this
	 */
	final public function setQueryLink(string $queryLink="")
	{
		$this->queryLink = !empty($queryLink) ? $queryLink : $this->queryLink;
		return $this;
	}

	/**
	 * Get query link
	 * @return string
	 */
	final public function getQueryLink() : string
	{
		return empty($this->queryLink) ? "" : $this->queryLink;
	}

	/**
	 * Generate links with already set template
	 * @return string
	 */
	final public function generateLinks() : string
	{
		if ($this->totalPages() <= 1) return "";
		$queryLink 			= $this->getQueryLink();
	    $lastPageM1 		= $this->pageBeforeLastPage();
	    $lastPage   		= $totalPages = $this->lastPage();
		$template			= $this->getTemplate();
		$generatedLinks		= $template->prefix;
        if ($this->hasPreviousPage()):
            $generatedLinks	.= sprintf($template->previousLink, "?page=".$this->previousPage().$queryLink);
        endif;
        if ($this->notEnoughPages()):
            for ($i = 1; $i <= $totalPages; $i++):
                if ($i == $this->currentPage)
		            $generatedLinks	.= sprintf($template->activeLink, $i);
                else
                    $generatedLinks	.= sprintf($template->link, "?page={$i}{$queryLink}", $i);
            endfor;
        elseif ($this->hasEnoughPages()):
            if ($this->closeToBeginning()):
                for ($i = 1; $i < 4 + ($this->adjacent * 2); $i++):
                    if ($i == $this->currentPage)
	                    $generatedLinks	.= sprintf($template->activeLink, $i);
                    else
	                    $generatedLinks	.= sprintf($template->link, "?page={$i}{$queryLink}", $i);
                endfor;
                $generatedLinks	.= $template->elipsesLink;
                $generatedLinks	.= sprintf($template->link, "?page={$lastPageM1}{$queryLink}", $lastPageM1);
                $generatedLinks	.= sprintf($template->link, "?page={$lastPage}{$queryLink}", $lastPage);
            elseif ($this->inMiddle()):
                $generatedLinks	.= sprintf($template->link, "?page=1{$queryLink}", 1);;
                $generatedLinks	.= sprintf($template->link, "?page=2{$queryLink}", 2);
                $generatedLinks	.= $template->elipsesLink;
                for ($i = $this->currentPage - $this->adjacent; $i <= $this->currentPage + $this->adjacent; $i++):
                    if ($i == $this->currentPage)
	                    $generatedLinks	.= sprintf($template->activeLink, $i);
                    else
	                    $generatedLinks	.= sprintf($template->link, "?page={$i}{$queryLink}", $i);
                endfor;
                $generatedLinks	.= $template->elipsesLink;
                $generatedLinks	.= sprintf($template->link, "?page={$lastPageM1}{$queryLink}", $lastPageM1);
                $generatedLinks	.= sprintf($template->link, "?page={$lastPage}{$queryLink}", $lastPage);
            else:
                $generatedLinks	.= sprintf($template->link, "?page=1{$queryLink}", 1);;
                $generatedLinks	.= sprintf($template->link, "?page=2{$queryLink}", 2);
                $generatedLinks	.= $template->elipsesLink;
                for ($i = $totalPages - (1 + ($this->adjacent * 3)); $i <= $totalPages; $i++):
                    if ($i == $this->currentPage)
	                    $generatedLinks	.= sprintf($template->activeLink, $i);
                    else
	                    $generatedLinks	.= sprintf($template->link, "?page={$i}{$queryLink}", $i);
                endfor;
            endif;
        endif;
        if ($this->hasNextPage()):
            $generatedLinks	.= sprintf($template->nextLink, "?page=".$this->nextPage().$queryLink);
        endif;
		$generatedLinks	.= $template->postfix;
		return $generatedLinks;
	}
}

<?php

# ========================================================================#
#  Author: Mohammad Hafijur Rahman
#  Version:	2.0
#  Date: 01-01-2020
#  Purpose: Paging
#  Requires : Requires PHP5
# ========================================================================#

class PageSet {
  private $totalCount;
  private $itemPerPage;
  private $linkTo;
  private $currentPage;
  private $cssClass;
  private $queryString;

  function __construct($linkTo, $totalCount, $itemPerPage, $currentPage, 
    $cssClass = null, $queryString = null) {
    $this->totalCount = $totalCount;
    $this->linkTo = $linkTo;
    $this->itemPerPage = $itemPerPage;
    $this->currentPage = $currentPage;
    
    if ($cssClass == null)
      $this->cssClass = "";
    else
      $this->cssClass = $cssClass;

    if ($queryString == null)
      $this->queryString = "";
    else
      $this->queryString = "?" . $queryString;
  }

  /**
   * This is updated getpages method.
   * @param int $numberOfPageEachSide
   */
  function getPages($numberOfPageEachSide = 5) {
    $pages = 1;
    if ($this->totalCount > 0) {
      $pages = ceil($this->totalCount / $this->itemPerPage);
    }
    
    $contant = "";
    // if only one page then we dont show the paging bar.
    if ($pages < 2)
      return $contant;

    if ($this->currentPage > ($numberOfPageEachSide + 1) && 
        $this->currentPage < ($pages - $numberOfPageEachSide) &&
        $pages >= (($numberOfPageEachSide * 2) + 1 + 2)) {
      
      // then show first and last.
      $contant = $contant . "<a id='page-1' class='button " . $this->cssClass . "' href='" . $this->linkTo . "/1" . $this->queryString . "'>First</a>";
      $i = $numberOfPageEachSide - 1;
      while($i > 0) {
        $contant = $contant . "<a id='page-" . ($this->currentPage - $i) . "' class='button " . $this->cssClass . "' href='" . $this->linkTo . "/" . ($this->currentPage - $i) . $this->queryString . "'>" . ($this->currentPage - $i) . "</a>";
        $i--;
      }
      
      $contant = $contant . "<a id='page-" . ($this->currentPage) . "' class='button onpage " . $this->cssClass . "' href='" . $this->linkTo . "/" . ($this->currentPage) . $this->queryString . "'>" . ($this->currentPage) . "</a>";
      
      $i = 1;
      while($i < $numberOfPageEachSide) {
        $contant = $contant . "<a id='page-" . ($this->currentPage + $i) . "' class='button " . $this->cssClass . "' href='" . $this->linkTo . "/" . ($this->currentPage + $i) . $this->queryString . "'>" . ($this->currentPage + $i) . "</a>";
        $i++;
      }
      $contant = $contant . "<a id='page-" . $pages . "' class='button " . $this->cssClass . "' href='" . $this->linkTo . "/" . $pages . $this->queryString . "'>Last</a>";
      
    } else if ($this->currentPage > ($numberOfPageEachSide + 1) && 
               $pages >= (($numberOfPageEachSide * 2) + 1 + 2)) {
      
      // then show only first.
      $contant = $contant . "<a id='page-1' class='button " . $this->cssClass . "' href='" . $this->linkTo . "/1" . $this->queryString . "'>First</a>";
      $i = $numberOfPageEachSide - 1;
      while($i > 0) {
        $contant = $contant . "<a id='page-" . ($this->currentPage - $i) . "' class='button " . $this->cssClass . "' href='" . $this->linkTo . "/" . ($this->currentPage - $i) . $this->queryString . "'>" . ($this->currentPage - $i) . "</a>";
        $i--;
      }
      
      $contant = $contant . "<a id='page-" . ($this->currentPage) . "' class='button onpage " . $this->cssClass . "' href='" . $this->linkTo . "/" . ($this->currentPage) . $this->queryString . "'>" . ($this->currentPage) . "</a>";
      
      $i = $this->currentPage + 1;
      while($i <= $pages) {
        $contant = $contant . "<a id='page-" . $i . "' class='button " . $this->cssClass . "' href='" . $this->linkTo . "/" . $i . $this->queryString . "'>" . $i . "</a>";
        $i++;
      }
    } else if ($this->currentPage <= ($pages - $numberOfPageEachSide) && 
               $pages >= (($numberOfPageEachSide * 2) + 1 + 2)) {
      
      // then show only last.
      $i = ($this->currentPage - $numberOfPageEachSide);
      if($i < 1) 
        $i = 1;
      while($i < ($this->currentPage)) {
        $contant = $contant . "<a id='page-" . $i . "' class='button " . $this->cssClass . "' href='" . $this->linkTo . "/" . $i . $this->queryString . "'>" . $i . "</a>";
        $i++;
      }
      
      $contant = $contant . "<a id='page-" . $this->currentPage . "' class='button onpage " . $this->cssClass . "' href='" . $this->linkTo . "/" . $this->currentPage . $this->queryString . "'>" . $this->currentPage . "</a>";
      
      $i = 1;
      while($i < $numberOfPageEachSide) {
        $contant = $contant . "<a id='page-" . ($this->currentPage + $i) . "' class='button " . $this->cssClass . "' href='" . $this->linkTo . "/" . ($this->currentPage + $i) . $this->queryString . "'>" . ($this->currentPage + $i) . "</a>";
        $i++;
      }
      $contant = $contant . "<a id='page-" . $pages . "' class='button " . $this->cssClass . "' href='" . $this->linkTo . "/" . $pages . $this->queryString . "'>Last</a>";
    } else {
      // normal
      for ($i = 1; $i <= $pages; $i++) {
        if ($i == $this->currentPage) {
          $contant = $contant . "<a id='page-" . $i . "' class='button onpage " . $this->cssClass . "' href='" . $this->linkTo . "/" . $i . $this->queryString . "'>" . $i . "</a>";
        } else {
          $contant = $contant . "<a id='page-" . $i . "' class='button " . $this->cssClass . "' href='" . $this->linkTo . "/" . $i . $this->queryString . "'>" . $i . "</a>";
        }
      }
    }

    return $contant . "<div class='clear'></div>";
  }

}

?>

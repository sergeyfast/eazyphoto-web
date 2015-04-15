<?php
    /** Paginator Area */
    $page ++;
    $pageCount        = ceil( $pageCount );
    $endPosition      = $page;
    $startPosition    = $page;
    $selectedPosition = $page;
    $subInterval      = 5;

    while ( ( ($endPosition < $page +  $subInterval)
            && ($endPosition < $pageCount) )
        ||
        ( ($endPosition < $pageCount)
            && ($endPosition < $subInterval*2)
            && ($page < $subInterval)) ) {
        $endPosition++;
    }

    while( ( ($startPosition > $page -  $subInterval +1 )
            && ($startPosition > 1 ) )
        ||
        ( ($startPosition > 1)
            && ($startPosition > $pageCount - $subInterval*2 + 1)
            && ($page > $pageCount - $subInterval)) ) {
        $startPosition --;
    }

    if ( $pageCount > 1 ) {
        ?>
        <ul class="metaList fsSmall cont">
				<li class="arrow"><a href="{web:$pagesUrl}0">&laquo;</a></li>
<?php
        for( $i = $startPosition;  $i <= $endPosition; $i ++ ) {
            if  ($i == $page ) {
                ?>
                <li class="current">{$i}</li>
            <?          } else  { ?>
                <li><a href="{web:$pagesUrl}<?= $i - 1 ?>" title="{$i}">{$i}</a></li>
            <?php
            }
        }
        ?>
				<li class="arrow"><a href="{web:$pagesUrl}<?= $pageCount - 1?>">&raquo;</a></li>
			</ul>
<?php
    }
    $page --;
?>
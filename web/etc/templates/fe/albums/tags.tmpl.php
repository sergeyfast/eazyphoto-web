<?php
    /** @var Tag[] $tags */

?>
{increal:tmpl://fe/elements/header.tmpl.php}
<? foreach( $tags as $t ) { ?>
    <? if ( !$t->photoPath ) continue; ?>
<div class="present">
    <div style="background-image: url({web:$t.photoPath})" class="_background"></div>
    <div class="_text">
        <p><a href="<?= LinkUtility::GetTagUrl( $t ) ?>" class="tag">{$t.title}</a></p>
        <? if ( $t->description ) { ?><p class="cFirm">{$t.description}</p><? } ?>
    </div>
</div>
<? } ?>

{increal:tmpl://fe/elements/footer.tmpl.php}
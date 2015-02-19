<?
    /** @var MetaDetail $metaDetail */
    use Eaze\Helpers\FormHelper;

    if ( !$metaDetail->statusId ) {
        $metaDetail->statusId = 1;
    }
?>
<div class="tabs_cont fsMedium">
    <?= FormHelper::FormHidden( 'metaDetail[metaDetailId]', $metaDetail->metaDetailId ); ?>
    <?= FormHelper::FormHidden( 'metaDetail[statusId]', $metaDetail->statusId ); ?>
    <div class="row _fluid _p" data-row="pageTitle">
        <div class="col2"><label for="pageTitle" class="blockLabel">{lang:vt.metaDetail.pageTitle}</label></div>
        <div class="col6"><?= FormHelper::FormInput( 'metaDetail[pageTitle]', $metaDetail->pageTitle, 'pageTitle' ); ?></div>
    </div>
    <div class="row _fluid _p" data-row="metaKeywords">
        <div class="col2"><label for="metaKeywords" class="blockLabel">{lang:vt.metaDetail.metaKeywords}</label></div>
        <div class="col6"><?= FormHelper::FormTextArea( 'metaDetail[metaKeywords]', $metaDetail->metaKeywords, 'metaKeywords' ); ?></div>
    </div>
    <div class="row _fluid _p" data-row="metaDescription">
        <div class="col2"><label for="metaDescription" class="blockLabel">{lang:vt.metaDetail.metaDescription}</label></div>
        <div class="col6"><?= FormHelper::FormTextArea( 'metaDetail[metaDescription]', $metaDetail->metaDescription, 'metaDescription' ); ?></div>
    </div>
    <div class="row _fluid _p" data-row="alt">
        <div class="col2"><label for="alt" class="blockLabel">{lang:vt.metaDetail.alt}</label></div>
        <div class="col6"><?= FormHelper::FormInput( 'metaDetail[alt]', $metaDetail->alt, 'alt' ); ?></div>
    </div>
    <div class="row _fluid _p" data-row="canonicalUrl">
        <div class="col2"><label for="canonicalUrl" class="blockLabel">{lang:vt.metaDetail.canonicalUrl}</label></div>
        <div class="col6"><?= FormHelper::FormInput( 'metaDetail[canonicalUrl]', $metaDetail->canonicalUrl, 'canonicalUrl' ); ?></div>
    </div>
</div>
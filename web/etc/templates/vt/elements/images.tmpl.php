<?
    use Eaze\Core\Session;
    use Eaze\Helpers\CssHelper;
    use Eaze\Helpers\JsHelper;
    use Eaze\Helpers\ObjectHelper;

    CssHelper::PushFile( 'js://ext/uploadify/uploadify.vt.css' );
    JsHelper::PushFiles( [
        'js://ext/uploadify/jquery.uploadify.js',
        'js://ext/jquery.plugins/ICanHaz.min.js',
        'js://vt/object-images.js',
    ] );

    JsHelper::PushLine( sprintf( 'sessionData = { "%s" : "%s" };', Session::getName(), Session::getId() ) );

    // image data
    /** @var array $imageData */
    JsHelper::PushLine( sprintf( 'var objectImages = %s;', ObjectHelper::ToJSON( $imageData ) ) );
    if ( !empty( $imageErrors ) ) {
        JsHelper::PushLine( sprintf( 'var objectImageErrors = %s;', ObjectHelper::ToJSON( $imageErrors ) ) );
    }
?>
<div class="tabs_cont fsMedium">
    <div class="row _fluid _p">
        <div class="col3 displayInlineBlock" id="files"><input id="objectImages_upload" name="file_upload" type="file" /></div>
        <div class="col3"><a href="#" class="button _light" id="add-image"><i class="cFade foundicon-plus"></i> добавить вручную</a></div>
    </div>
    <div class="row _fluid _p">
        <div class="col4"><strong>Название</strong></div>
        <div class="col2" title="<?= implode( 'x', ObjectImageUtility::$DefaultImageSize ) ?>"><strong>Маленькая картинка</strong></div>
        <div class="col2"><strong>Большая картинка</strong></div>
    </div>
    <div id="objectImages"></div>
</div>
<script id="tmplObjectImage" type="text/html">
    <div class="row _fluid objectImage">
        <input type="hidden" name="{$prefix}[images][{{c.nextIndex}}][objectImageId]" value="{{id}}">
        <div class="col4 displayInlineBlock">
            <div class="row _fluid">
                <div class="col1"><i class="handle foundicon-refresh"></i></div>
                <div class="col11"><input type="text" name="{$prefix}[images][{{c.index}}][title]" value="{{imgName}}" ></div>
            </div>
        </div>
        <div class="col2"><input type="hidden" class="vfsFile" data-mode="fileId" name="{$prefix}[images][{{c.index}}][smallImageId]" id="photo-small-{{c.index}}" vfs:previewType="image"  {{#smallImage}}value="{{id}}" vfs:src="{web:vfs://}{{src}}" vfs:name="{{name}}"{{/smallImage}}/></div>
        <div class="col2"><input type="hidden" class="vfsFile" data-mode="fileId" name="{$prefix}[images][{{c.index}}][bigImageId]" id="photo-big-{{c.index}}" vfs:previewType="image" {{#bigImage}}value="{{id}}" vfs:src="{web:vfs://}{{src}}" vfs:name="{{name}}"{{/bigImage}}/></div>
        <div class="col1"><a href="#" class="button _del"><i class="fsSmall foundicon-remove"></i></a></div>
    </div>
</script>
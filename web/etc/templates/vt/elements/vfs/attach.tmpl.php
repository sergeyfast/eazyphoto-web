{increal:tmpl://vt/header.tmpl.php}
<script type="text/javascript" src="{web:js://vfs/vfs.selector.js}"></script>
<script type="text/javascript">
    var vfsSelector = new VfsSelector( "{web:vt://vfs/}" );
</script>
<div class="main">
    <div class="inner">
        <form method="post" action="" enctype="multipart/form-data" id="data-form">
            <div class="pagetitle">
                <h1>Demo</h1>
            </div>
            <div class="row">
                <label>Date</label>
                <?= FormHelper::FormDate( 'd1' ); ?>
            </div>
            <div class="row">
                <label>Time</label>
                <?= FormHelper::FormTime( 'd2' ); ?>
            </div>
            <div class="row">
                <label>DateTime</label>
                <?= FormHelper::FormDateTime( 'd3' ); ?>
            </div>
            <h2 class="legend">VFS</h2>
            <div class="row">
                <label>VfsHelper image</label> <?= VfsHelper::FormVfsFile( "image_3", "image_3", null, "image" );?>
            </div>
            <div class="row">
                <label>VfsHelper</label> <?= VfsHelper::FormVfsFile( "image_4", "image_4", null );?>
            </div>
            <div class="row">
                <label>VfsHelper File</label> <?= VfsHelper::FormVfsFilePath( "image_5", "image_5", "201303/5_14.jpg", "image" );?>
            </div>

            <div class="row">
                <label>Editor</label>
                <?= FormHelper::FormEditor( 'd5' ); ?>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    vfsSelector.Init();
</script>
<? $__useEditor = true; ?>
{increal:tmpl://vt/footer.tmpl.php}
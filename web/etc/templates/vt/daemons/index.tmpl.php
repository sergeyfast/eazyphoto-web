<?php
    $__pageTitle = LocaleLoader::Translate( "vt.screens.daemonLock.list");

    $grid = array(
        "columns" => array(
           LocaleLoader::Translate( "vt.daemonLock.title" )
            , LocaleLoader::Translate( "vt.daemonLock.packageName" )
            , LocaleLoader::Translate( "vt.daemonLock.methodName" )
            , LocaleLoader::Translate( "vt.daemonLock.runAt" )
            , LocaleLoader::Translate( "vt.daemonLock.maxExecutionTime" )
            , LocaleLoader::Translate( "vt.daemonLock.isActive" )
        )
        , "colspans"   => array()
        , "operations" => false
        , "allowAdd"   => false
        , "canPages"   => DaemonLockFactory::CanPages()
        , "basepath"   => Site::GetWebPath( "vt://daemons/" )
        , "title"      => $__pageTitle
        , "pageSize"   => FormHelper::RenderToForm( $search["pageSize"] )
        , "deleteStr"  => LocaleLoader::Translate( "vt.screens.daemonLock.deleteString")
    );
?>
{increal:tmpl://vt/header.tmpl.php}
<div id="wrap">
	<div id="cont">
		<h1><?= ($grid["allowAdd"]) ? sprintf( "<span><a href=\"%sadd\">%s</a></span>", $grid["basepath"], LocaleLoader::Translate("vt.common.add") ) : "" ?> {$__pageTitle}</h1>
		<div class="blockEtc">
			<p class="blockHeader" title="{lang:vt.common.search}"><span><img src="{web:images://vt/find.png}" width="16" height="16" alt="" /> <strong>{lang:vt.common.search}</strong></span></p>
			<form id="searchForm" name="searchForm" method="post" action="#" class="<?= $hideSearch == "true" ? "hidden" : ""  ?>">
				<input type="hidden" value="1" name="searchForm" />
				<input type="hidden" value="" id="pageId" name="page" />
				<input type="hidden" value="{$grid[pageSize]}" id="pageSize" name="search[pageSize]" />
				<table class="vertList" cellspacing="0">
                    <tr>
                        <th>{lang:vt.daemonLock.title}</th>
                        <td><?= FormHelper::FormInput( "search[title]", $search["title"], "80", "title" ); ?></td>
                    </tr>
                    <tr>
                        <th>{lang:vt.daemonLock.packageName}</th>
                        <td><?= FormHelper::FormInput( "search[packageName]", $search["packageName"], "80", "packageName" ); ?></td>
                    </tr>
                    <tr>
                        <th>{lang:vt.daemonLock.methodName}</th>
                        <td><?= FormHelper::FormInput( "search[methodName]", $search["methodName"], "80", "methodName" ); ?></td>
                    </tr>
					<tr>
						<th>&nbsp;</th>
						<td><input name="find" type="submit" value="{lang:vt.common.find}" /></td>
					</tr>
				</table>
			</form>
		</div>
    <!-- GRID -->
{increal:tmpl://vt/elements/datagrid/header.tmpl.php}
<?php
    $langEdit   = LocaleLoader::Translate( "vt.common.edit" );
    $langDelete = LocaleLoader::Translate( "vt.common.delete" );

    foreach ( $list as $object )  {
        $id         = $object->daemonLockId;
        $editpath   = $grid['basepath'] . "edit/" . $id;
?>
			<tr class="lr" id="row_{$id}">
            <td>{$object.title}</td>
            <td>{$object.packageName}</td>
            <td>{$object.methodName}</td>
            <td><?= ( !empty( $object->runAt ) ? $object->runAt->DefaultFormat() : '' ) ?></td>
            <td>{$object.maxExecutionTime.DefaultTimeFormat()}</td>
            <td>{$object.isActive}</td>
                
	        </tr>
<?php
    }
?>
{increal:tmpl://vt/elements/datagrid/footer.tmpl.php}
    <!-- EOF GRID -->
    	</div>
</div>
{increal:tmpl://vt/footer.tmpl.php}
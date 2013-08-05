<?php
    JsHelper::Init( Site::IsDevel() );
    CssHelper::Init( Site::IsDevel() );

    JsHelper::PushFiles(
        array(
            'js://ext/jquery/jquery.js'
            , 'js://ext/jquery.plugins/jquery.jsonrpc.js'
            , 'js://ext/jquery.plugins/ICanHaz.min.js'
            , 'js://vfs/vfs.js'
            , 'js://ext/uploadify/jquery.uploadify.min.js'
        )
    );

    JsHelper::PushLine( sprintf( 'var sessionData = { "%s" : "%s" };', Session::getName(), Session::getId() ) );

    CssHelper::PushFile( 'css://vfs/vfs.css' );

    // for action :)
    $fileId   = Request::getInteger( 'fileId' );
    $folderId = Request::getInteger( 'folderId' );
    $file     = Request::getString( 'file' );
    $fileId   = $fileId ? $fileId : null;
    $folderId = $folderId ? $folderId : 1;
    $file     = $file ? $file : null;
    $isMce    = Page::$RequestData[1] == 'mce';
?>
<!DOCTYPE html>
<html lang="ru-RU">
<head>
<meta charset="UTF-8">
<title>VFS</title>
<?= CssHelper::Flush() ?>
<?= JsHelper::Flush() ?>
<script id="vfsBase" type="text/html">
    <div class="vfs">
        <div class="vfs_header">
            <div class="vfs_breadcrumbs"></div>
        </div>
        <div class="vfs_cont">
            <div class="vfs_cont_panel vfs_cont_panel_folders">
                <div class="vfs_cont_panel_header">
                    <ul>
                        <li title="Новая папка" class="_button_new_folder"></li>
                        <li title="Избранные" class="_button_view_favorites"></li>
                    </ul>
                </div>
                <div class="vfs_cont_panel_cont">
                    <div class="vfs_folder_list"></div>
                </div>
            </div>
            <div class="vfs_cont_panel vfs_cont_panel_files">
                <div class="vfs_cont_panel_header">
                    <input type="text" placeholder="Поиск…" class="_search_input">
                    <ul>
                        <li title="Галерея" class="_button_view_thumb"></li>
                        <li title="Список" class="_button_view_list"></li>
                    </ul>
                    <ul class="_files_operations">
                        <li title="Вставить" class="_button_paste"></li>
                        <li title="Вырезать" class="_button_cut"></li>
                        <li title="Удалить" class="_button_delete"></li>
                        <li class="_button_add">Загрузить файлы</li>
                    </ul>
                </div>
                <div class="vfs_cont_panel_cont">
                    <div class="vfs_file_list"></div>
                </div>
            </div>
        </div>
        <div class="vfs_footer">
            <div class="vfs_status"></div>
            <div class="vfs_paginator"></div>
        </div>
        <div class="vfs_popup_wrap"></div>
    </div>
</script>
<script id="vfsStatus" type="text/html" class="partial">
    <!-- VFS Status-->
</script>
<script id="vfsBreadcrumbs" type="text/html" class="partial">
    <ul>{{#folderBranch}}
        <li><a href="#{{id}}" rel="{{id}}">{{name}}</a></li>{{/folderBranch}}
    </ul>
</script>
<script id="vfsPaginator" type="text/html" class="partial">{{#countFiles}}
    <p>
        Отображено {{filesShown}} из {{countFiles}}
        {{#filesLeft}}
        <span class="vfs_cfade">|</span> <a href="#LoadPage">Показать ещё {{filesLeft}}</a> <span class="vfs_cfade">|</span> <a href="#LoadAll">Показать все</a>
        {{/filesLeft}}
    </p>{{/countFiles}}
    {{^countFiles}}
    <p class="vfs_cfade">Нет файлов</p>{{/countFiles}}
</script>
<script id="vfsFolderList" type="text/html" class="partial">{{#folderParentId}}
    <dl>
        <dt><a href="#{{folderParentId}}" rel="{{folderParentId}}" class="_folder_current">…</a></dt>
    </dl>{{/folderParentId}}
    {{#folderList}}
    <dl rel="{{id}}">
        <dt><span class="_folderState {{isFavorite}}"></span><a href="#{{id}}" rel="{{id}}">{{name}}</a></dt>
        <dd class="_element_controls">▾</dd>
    </dl>{{/folderList}}
    {{^folderList}}
    <!-- p Подпапки отсутствуют-->{{/folderList}}
</script>
<script id="vfsFolderListFavs" type="text/html" class="partial">{{#favoritesList}}
    <dl rel="{{id}}">
        <dt><span class="_folderState {{isFavorite}}"></span><a href="#{{id}}" rel="{{id}}">{{name}}</a></dt>
        <dd class="_element_controls">▾</dd>
    </dl>{{/favoritesList}}
    {{^favoritesList}}
    <!-- p Подпапки отсутствуют-->{{/favoritesList}}
</script>
<script id="vfsFileList" type="text/html" class="partial">{{#countFiles}}
    <ul class="vfs_file_list_sorting">
        <li class="_file_name">
            <input type="checkbox" class="_file_check"><strong>Имя файла</strong>
        </li>
        <li class="_file_size"><strong>Размер</strong></li>
        <li class="_file_date"><strong>Дата</strong></li>
    </ul>{{/countFiles}}
    {{#fileList}}
    <dl rel="{{path}}" data-relpath="{{relpath}}" data-name="{{name}}" data-ext="{{extension}}" data-id="{{id}}" data-width="{{width}}" data-height="{{height}}" data-short-path="{{shortpath}}">
        <dt class="_file_name">
            <input type="checkbox" class="_file_check"><a href="{{path}}" target="_blank" class="_{{extension}}"><span>{{name}}</span>.{{extension}}</a>
        </dt>
        <dd class="_file_size">{{{size}}}</dd>
        <dd class="_file_date">{{date}}</dd>
        <dd class="_element_controls">▾</dd>
    </dl>{{/fileList}}
    {{^fileList}}
    <!-- p В папке отсутствуют файлы-->{{/fileList}}
</script>
<script id="vfsFileListThumb" type="text/html" class="partial">{{#fileList}}
    <dl rel="{{path}}" data-relpath="{{relpath}}" data-name="{{name}}" data-ext="{{extension}}" data-id="{{id}}" class="vfs_file_list_thumb">
        <dt title="{{name}}.{{extension}}" class="_file_name"><a href="{{path}}" target="_blank" class="_{{extension}}">{{#isImage}}<img src="{{path}}" alt="{{name}}.{{extension}}">{{/isImage}}</a>
            <input type="checkbox" class="_file_check"><span>{{name}}.{{extension}}</span>
        </dt>
        <dd class="_element_controls">▾</dd>
    </dl>{{/fileList}}
    {{^fileList}}
    <!-- p В папке отсутствуют файлы-->{{/fileList}}
</script>
<script id="vfsFolderControl" type="text/html">
    <ul class="vfs_contol_dropdown">
        <li title="Удалить" class="vfs_dropdown_delete"></li>
        <li title="Вырезать" class="vfs_dropdown_cut"></li>
        <li title="Переименовать" class="vfs_dropdown_rename"></li>
        <li title="Заложить" class="vfs_dropdown_favorite"></li>
    </ul>
</script>
<script id="vfsFileControl" type="text/html">
    <ul class="vfs_contol_dropdown">
        <li title="Удалить" class="vfs_dropdown_delete"></li>
        <li title="Вырезать" class="vfs_dropdown_cut"></li>
        <li title="Переименовать" class="vfs_dropdown_rename"></li>
        <li title="Ссылка" class="vfs_dropdown_link"><a href="{{fileLink}}" target="_blank"></a></li>
    </ul>
</script>
<script id="vfsFolderNew" type="text/html">
    <div>
        <div class="vfs_popup_overlay"></div>
        <div class="vfs_popup">
            <div class="vfs_popup_header">
                <p>Новая папка</p>
            </div>
            <div class="vfs_popup_cont">
                <p>Создать в папке <strong>{{folderName}}</strong>:</p>
                <input type="text" placeholder="Введине название новой папки">
            </div>
            <div class="vfs_popup_footer"><span class="vfs_button _accept">Создать</span><span class="vfs_button _alt _cancel">Отменить</span></div>
        </div>
    </div>
</script>
<script id="vfsFolderDelete" type="text/html">
    <div>
        <div class="vfs_popup_overlay"></div>
        <div class="vfs_popup">
            <div class="vfs_popup_header _warning">
                <p>Удаление папки</p>
            </div>
            <div class="vfs_popup_cont">Вы действительно хотите удалить папку <strong>{{folderName}}</strong>?</div>
            <div class="vfs_popup_footer"><span class="vfs_button _accept">Удалить</span><span class="vfs_button _alt _cancel">Отменить</span></div>
        </div>
    </div>
</script>
<script id="vfsFileDelete" type="text/html">
    <div>
        <div class="vfs_popup_overlay"></div>
        <div class="vfs_popup">
            <div class="vfs_popup_header _warning">
                <p>Удаление файлов</p>
            </div>
            <div class="vfs_popup_cont">
                {{#fileName}}
                Вы действительно хотите удалить файл <strong>{{fileName}}</strong>?
                {{/fileName}}
                {{^fileName}}
                Вы действительно хотите удалить выбранные файлы?
                {{/fileName}}
            </div>
            <div class="vfs_popup_footer"><span class="vfs_button _accept">Удалить</span><span class="vfs_button _alt _cancel">Отменить</span></div>
        </div>
    </div>
</script>
<script id="vfsFolderRename" type="text/html">
    <div>
        <div class="vfs_popup_overlay"></div>
        <div class="vfs_popup">
            <div class="vfs_popup_header">
                <p>Переименовать папку</p>
            </div>
            <div class="vfs_popup_cont">
                <p>Введите новое имя папки <strong>{{folderName}}</strong>:</p>
                <input type="text" placeholder="Введине новое название папки" value="{{folderName}}">
            </div>
            <div class="vfs_popup_footer"><span class="vfs_button _accept">Переименовать</span><span class="vfs_button _alt _cancel">Отменить</span></div>
        </div>
    </div>
</script>
<script id="vfsFileRename" type="text/html">
    <div>
        <div class="vfs_popup_overlay"></div>
        <div class="vfs_popup">
            <div class="vfs_popup_header">
                <p>Переименовать файл</p>
            </div>
            <div class="vfs_popup_cont">
                <p>Введите новое серверное имя для файла <strong>{{name}}.{{ext}}</strong>:</p>
                <input type="text" placeholder="Введине новое название файла" value="{{relpath}}">
            </div>
            <div class="vfs_popup_footer"><span class="vfs_button _accept">Переименовать</span><span class="vfs_button _alt _cancel">Отменить</span></div>
        </div>
    </div>
</script>
<script id="vfsFileLink" type="text/html">
    <div>
        <div class="vfs_popup_overlay"></div>
        <div class="vfs_popup">
            <div class="vfs_popup_header">
                <p>Ссылка на файл</p>
            </div>
            <div class="vfs_popup_cont">
                <input type="text" value="{{path}}">
            </div>
            <div class="vfs_popup_footer"><span class="vfs_button _cancel">Закрыть</span></div>
        </div>
    </div>
</script>
<script id="vfsFileUpload" type="text/html">
    <div>
        <div class="vfs_popup_overlay"></div>
        <div class="vfs_popup">
            <div class="vfs_popup_header">
                <p>Загрузка файлов</p>
            </div>
            <div class="vfs_popup_cont">
                <div id="vfs_uploadify"></div><span class="vfs_button _alt _cancel">Закрыть</span>
            </div>
        </div>
    </div>
</script>
<script>
    $(document).ready(function () {
        vfs.init({
            path: '{web:vt://vfs/rpc/}',
            folder: <?= $folderId ?>,
            <?= $fileId ? 'startFileId: ' . $fileId . ',' : '' ?>
            <?= $file ? 'startFile: "' . HtmlHelper::RenderToForm( $file ) . '",' : '' ?>
            filesPage: 100,
            foldersView: 0,
            filesView: 0,
            uploadifyPath: '{web:js://ext/uploadify/uploadify.swf}',
            uploadifyData: sessionData
        });
    });
</script>
<style>
    body { color: #000; background: #EEE }
</style>
</head>
<body>
<div id="vfs_target" style="width: 820px; height: 690px; margin: 10px auto;"></div>
{increal:tmpl://vt/elements/vfs/footer.tmpl.php}
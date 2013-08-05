<?php
    $__pageTitle = LocaleLoader::Translate( "vt.login.enter" );
    $__noMenu    = true;

    $cssFilesAdds = array(
        AssetHelper::AnyBrowser => array(
            'css://vt/login.css'
        )
    );
?>
{increal:tmpl://vt/header.tmpl.php}
<div class="main">
    <div class="inner">
        <form method="post" action="{web:vt://login}" class="login-form">
            <input type="hidden" value="1" name="loginForm" />
            <div class="rows">
                <div class="row required <?= ( empty( $login ) && !empty( $error ) ) ? 'error' : '' ?>">
                    <label>{lang:vt.login.user}</label>
                    <input type="text" class="text" name="login" value="{form:$login}" />
                </div>
                <div class="row required <?= !empty( $error ) ? 'error' : '' ?>">
                    <label>{lang:vt.login.password}</label>
                    <input type="password" class="text" name="password" value="" />
                    <? if ( ! empty( $error ) ) { ?>
                        <p class="error">{lang:$error}</p>
                    <? } ?>
                </div>
                <div class="buttons">
                    <div class="buttons-inner">
                        <input type="submit" class="" value="{lang:vt.login.enter}" />
                    </div>
                </div>
            </div>
        </form>
	</div>
</div>
{increal:tmpl://vt/footer.tmpl.php}

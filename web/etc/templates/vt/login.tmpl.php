<?php
    use Eaze\Helpers\FormHelper;

    $__pageTitle = T( 'vt.login.enter' );
    $__noMenu    = true;
    $__noBar     = true;

    /** @var bool $remember */
    /** @var string $login */
?>
{increal:tmpl://vt/elements/header.tmpl.php}

<div class="container alignCenter">
    <div style="padding-top: 20%" class="displayInlineBlock">
        <div class="row alignLeft">
            <div class="col4">
                <h1>{$__projectName}</h1>
                <form method="post" action="{web:vt://login}">
                    <?= \Eaze\Helpers\SecureTokenHelper::FormHidden(); ?>
                    <div class="row cont">
                        <div class="col1"><label for="login" class="blockLabel _shiftToRight">{lang:vt.login.user}</label></div>
                        <div class="col3"><?= FormHelper::FormInput( 'login', $login, 'login', ( empty( $login ) && !empty( $error ) ) ? '_error' : ''  ); ?></div>
                    </div>
                    <div class="row _p">
                        <div class="col1"><label for="password" class="blockLabel _shiftToRight">{lang:vt.login.password}</label></div>
                        <div class="col3">
                            <?= FormHelper::FormPassword( 'password', '', 'password', !empty( $error ) ? '_error' : '' ); ?>
                        </div>
                    </div>
                    <div class="row _p">
                        <div class="col3 offset1">
                            <label><?= FormHelper::FormCheckBox( 'remember', true, null, null, $remember ); ?> Запомнить вход на неделю</label>
                        </div>
                    </div>
                    <? if ( ! empty( $error ) ) { ?>
                        <div class="row cont">
                            <div class="col4 offset1 cWarn">{lang:$error}
                            </div>
                        </div>
                    <? } ?>
                    <div class="row cont">
                        <div class="col3 offset1">
                            <button type="submit" class="_med">{lang:vt.login.enter}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{increal:tmpl://vt/elements/footer.tmpl.php}
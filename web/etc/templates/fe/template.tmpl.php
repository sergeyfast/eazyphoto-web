<?php
    use Eaze\Helpers\FormHelper;
    use Eaze\Helpers\SecureTokenHelper;

    /** @var StaticPage $__page Get StaticPage */
    /** @var OrderForm $form GetStaticPage */
    /** @var string[] $errors GetStaticPage  */
    /** @var bool $success GetStaticPage  */
?>
{increal:tmpl://fe/elements/header.tmpl.php}
<div class="container">
    {increal:tmpl://fe/elements/breadcrumbs.tmpl.php}
    <h1>{$__page.title}</h1>
    <? if ( $__page->images ) { ?>
    <? $img = reset( $__page->images ); /** @var $img ObjectImage */?>
    <p class="marginTopBase"><img src="{web:vfs://}<?= $img->bigImage->path ?>" alt="{$img.title}"></p>
    <? } ?>
    <div class="row">
        <div class="col5">
            <? if ( $success ) { ?>
            <h1>Спасибо!</h1>
            <p class="fsBig">Ваша заявка отправлена!</p>
            <? } else { ?>
            <form action="{web:$__page.url}" method="post">
                <? if ( $errors ) { ?><p class="cWarn">Пожалуйста, заполните все необходимые поля!</p><? } ?>
                <?= SecureTokenHelper::FormHidden() ?>
                <label<?= !empty( $errors['name'] ) ? ' class="cWarn"' : '' ?>>
                    <p><strong>Имя:</strong></p>
                    <p class="marginBottomBase"><?= FormHelper::FormInput( 'form[name]', $form->Name, null, !empty( $errors['name'] ) ? '_error' : '', ['placeholder' => 'Ваше имя'] ) ?></p>
                </label>
                <label<?= !empty( $errors['email'] ) ? ' class="cWarn"' : '' ?>>
                    <p><strong>E-mail:</strong></p>
                    <p class="marginBottomBase"><?= FormHelper::FormInput( 'form[email]', $form->Email, null, !empty( $errors['email'] ) ? '_error' : '', ['placeholder' => 'Ваш e-mail '] ) ?></p>
                </label>
                <label<?= !empty( $errors['phone'] ) ? ' class="cWarn"' : '' ?>>
                    <p><strong>Телефон:</strong></p>
                    <p class="marginBottomBase"><?= FormHelper::FormInput( 'form[phone]', $form->Phone, null, !empty( $errors['phone'] ) ? '_error' : '', ['placeholder' => 'Телефон для связи '] ) ?></p>
                </label>
                <label>
                    <p><strong>Комментарий</strong></p>
                    <p class="marginBottomBase">
                        <?= FormHelper::FormTextArea( 'form[comment]', $form->Comment, null, null, [ 'placeholder' => 'Дополнительный комментарий', 'cols' => 20, 'rows' => 7 ] ); ?>
                    </p>
                </label>
                <p>
                    <button type="submit">Заказать фотосессию</button>
                </p>
            </form>
            <? } ?>
        </div>
        <div class="col7">{$__page.content}</div>
    </div>
</div>
{increal:tmpl://fe/elements/footer.tmpl.php}
<?
    /** @var SiteParamHelper $sph */
    /** @var Navigation[] $__footerMenu */
?>
<div id="copyright">
    <div class="row full-width">
        <div class="large-4 columns">
            <p><?= $sph->GetSiteFooter() ?></p>
        </div>
        <div class="large-8 columns">
            <ul class="inline-list right">
                <? foreach( $__footerMenu as $n ) { ?>
                <li><a href="{$n.GetLink()}">{$n.title}</a></li>
                <? } ?>
            </ul>
        </div>
    </div>
</div>
<? if (!empty( $__params[SiteParamHelper::GoogleAnalytics] ) ) { ?>
	<!-- GoogleAnalytics counter -->
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', '<?= $__params[SiteParamHelper::GoogleAnalytics]->value ?>']);
	  _gaq.push(['_trackPageview']);
	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	</script>
	<!-- /GoogleAnalytics counter -->
<? } ?>
<? if (!empty( $__params[SiteParamHelper::YandexMetrika] ) ) { ?>
	<?
		$__yandexMetrika = $__params[SiteParamHelper::YandexMetrika]->value;
	?>
	<!-- Yandex.Metrika counter -->
	<div style="display:none;"><script type="text/javascript">
	(function(w, c) {
		(w[c] = w[c] || []).push(function() {
			try {
				w.yaCounter{$__yandexMetrika} = new Ya.Metrika({id:{$__yandexMetrika}, enableAll: true});
			}
			catch(e) { }
		});
	})(window, 'yandex_metrika_callbacks');
	</script></div>
	<script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script>
	<noscript><div><img src="//mc.yandex.ru/watch/{$__yandexMetrika}" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
	<!-- /Yandex.Metrika counter -->
<? } ?>
<?= JsHelper::Flush(); ?>
</body>
</html>
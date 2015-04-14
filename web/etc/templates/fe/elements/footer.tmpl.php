<? /** @var SiteParamHelper $sph */ ?>
</div>
<footer role="contentinfo">
    <div class="container">
        <div class="row">
            <div class="col3">
                <p><img src="{web:img://}logo.png" alt="EazyPhoto"></p>
                <p class="fsSmall"><?= $sph->GetSiteFooter() ?></p>
                <? if ( Context::$SocialNav ) { ?>
                <p><? foreach( Context::$SocialNav as $n ) { ?><a href="{$n.GetLink(true)}" title="{form:$n.title}"><img src="{web:vfs://}{$n.Image()}" alt="{form:$n.title}" class="icon"></a><? } ?></p>
                <? } ?>
            </div>
            <div class="col6">
                <? if ( Context::$HeaderNav ) { ?>
                <ul class="metaList">
                    <? foreach( Context::$FooterNav as $n ) { ?>
                    <li<?= Context::$Navigation && $n->navigationId === Context::$Navigation->navigationId ? ' class="_active"' : '' ?>><a href="{$n.GetLink(true)}"><span>{$n.title}</span></a></li>
                    <? } ?>
                </ul>
                <? } ?>
            </div>
            <div class="col3">
                <p><a href="http://syncapp.bittorrent.com/1.4.111/" class="linkInlineBlock"><img src="{web:img://}bittorrentsync.png" alt="" class="icon"> <span class="link cFade">BitTorrent Sync</span></a></p>
            </div>
        </div>
    </div>
</footer>
<?= Eaze\Helpers\JsHelper::Flush(); ?>
<? if ( $sph->HasYandexMetrika() ) { ?>
    <script type="text/javascript">
        (function (d, w, c) {
            (w[c] = w[c] || []).push(function() {
                try {
                    w.yaCounter26083239 = new Ya.Metrika({id:<?=$sph->GetYandexMetrika()?>,webvisor:true,clickmap:true,trackLinks:true,accurateTrackBounce:true});
                } catch(e) { }
            });

            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () { n.parentNode.insertBefore(s, n); };
            s.type = "text/javascript";
            s.async = true;
            s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else { f(); }
        })(document, window, "yandex_metrika_callbacks");
    </script>
    <noscript><div><img src="//mc.yandex.ru/watch/<?=$sph->GetYandexMetrika()?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<? } ?>
<? if ( $sph->HasGoogleAnalytics() ) { ?>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        ga('create', '<?= $sph->GetGoogleAnalytics() ?>', 'auto');
        ga('send', 'pageview');
    </script>
<? } ?>
</body>
</html>
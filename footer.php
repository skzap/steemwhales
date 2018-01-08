	<center>
    <a href="https://discord.gg/HYj4yvw" target="_blank"><img src="/pic/ads/minnowsupport.png" /></a>
    <!-- <iframe data-aa='324254' src='//ad.a-ads.com/324254?size=990x90' scrolling='no' style='width:990px; height:90px; border:0px; padding:0;overflow:hidden' allowtransparency='true'></iframe> -->
  </center>
</div>
<footer class="container-fluid text-center" style='background-color:#1e87c3; color: white; margin-top: 15px;'>
  <div style="margin-top:15px;">
    <p>
      1 STEEM = $ <?php echo $globals->real_price ?> SBD
      |
      1 STEEM = $ <?php echo $globals->steem_price_usd ?> USD
      |
      1 SBD = $ <?php echo $globals->sbd_price_usd ?> USD
      |
      1MVest = <?php echo 1000000*$globals->total_vesting_fund_steem/$globals->total_vesting_shares ?> STEEM
      |
      Steemians Tracked: <?php echo $globals->accounts_tracked ?>
      <br />
    </p>
  </div>
</footer>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-81005941-1', 'auto');
  ga('send', 'pageview');

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="/js/jquery.query-object.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.6/Chart.min.js"></script>
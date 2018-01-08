<?php
include('config.php');
?>
<html>
<head>
    <meta name="google-site-verification" content="FkA_aQzh9UJIesfdycaWWPjj3BPdbnB5STDTI-lAW8k" />
    <title>Timestamp anything on the Steem Blockchain - Steem Whales</title>
    <link rel="icon" type="image/png" href="/pic/favicon.png">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/whale.css">
</head>
<body>
    <?php include 'navigation.php';?>
    <div class="container" style="position: relative;right: 20px;">
        <div class='row text-center'>
            <form class="form-horizontal" id='form'>
            <fieldset>
            <legend>Trusted Timestamping on the STEEM Blockchain</legend>
            <div class="form-group">
              <label class="col-md-4 control-label" for="filebutton">File</label>
              <div class="col-md-4">
                <input type="file" name="pic" id='file' class='input-file'>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-4 control-label" for="algo">Hash Algorithm</label>
              <div class="col-md-4">
                <label class="radio-inline" for="algo-0">
                  <input type="radio" name="algo" id="algo-0" value="MD5" checked="checked">
                  MD5
                </label>
                <label class="radio-inline" for="algo-1">
                  <input type="radio" name="algo" id="algo-1" value="SHA1">
                  SHA1
                </label>
                <label class="radio-inline" for="algo-2">
                  <input type="radio" name="algo" id="algo-2" value="SHA256">
                  SHA256
                </label>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-4 control-label" for="hash">Hash</label>
              <div class="col-md-5">
              <input id="hash" name="hash" type="text" placeholder="" class="form-control input-md" required="">
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-4 control-label" for="Stamp"></label>
              <div class="col-md-4">
                <button id="Verify" class="btn btn-primary">Verify!</button>
                <button id="Stamp" class="btn btn-primary">Stamp!</button>
              </div>
            </div>

            </fieldset>
            </form>
            <div id='result' class="alert alert-success" role="alert" style='display:none'></div>
        </div>
        <div class='row text-center'>
          <h2>What is a trusted timestamp?</h2>
          Trusted timestamping is achieved through including the hash 'fingerprint' of your file on a transaction on a decentralised blockchain (in this case, STEEM). No one, not even the original owner will ever be able to change the date.
          This tool allows you to calculate the hash value of any file and write it onto the STEEM Blockchain 100% free. This timestamp proves the existence of the file at the moment where it was stamped.
          <h2>What are real world uses for this exactly?</h2>
          You can technically prove that an essay you need to give to your professor by friday, was actually written before friday, even though this would be foolish to try practically.
          Other examples would be for patent registration, insurance contracts and this kind of things.
          <h2>How to make a timestamp?</h2>
          You can either directly enter your hash, or use our tool to calculate it for you. Then you just need to press the Stamp! button and within 2-4 seconds a success message should appear, telling you the recorded time. All times displayed are UTC.
          <h2>Verifying a timestamp</h2>
          In order to be able to verify, you need to keep the original file, and remember which hashing algorithm you used. To prove a document existed, just send the file to someone, and tell them to test the file and press the 'Verify!' button. If the file doesn't match a recorded timestamp on the blockchain, it should say 'hash not found'. If on the opposite this hash was ever written on the blockchain, it will display the earliest timestamp of this hash and the block number.
          <h2>Why is this better than other timestamping solutions?</h2>
          Blockchains are built on top of a trusted timestamp mechanism and are a perfect to achieve proof of existence. STEEM blockchain is currently the only one that has literally zero costs to print a message as small as a hash. The confirmation time is under 4 seconds. Compared to bitcoin alternatives, this is 200 times more precise timestamping, and infinitly cheaper.
          <h2>Do I need to use this website to do this?</h2>
          The source code of the API running the service is hosted on <a href='https://github.com/skzap/steemtime'>github</a>, feel free to fork it.
        </div>
    </div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-81005941-1', 'auto');
  ga('send', 'pageview');

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.6/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/core-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/md5-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/sha1-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/sha256-min.js"></script>
<script type="text/javascript">
$("document").ready(function(){

    $('#form').submit(function(){return false;});

    $('#Stamp').click(function() {
        $('#Stamp').html('Loading ...')
        $("#Stamp").attr("disabled", "disabled");
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("result").innerHTML = this.responseText;
            $('#result').show()
            $('#Stamp').html('Stamp!')
            $("#Stamp").removeAttr("disabled");
          }
        };
        xhttp.open("POST", "http://steemwhales.com:6060/time/request", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        var hash = $('#hash')[0].value;

        xhttp.send("hash="+hash);
    })

    $("#file").change(function() {
        updateHashFromFile();
    });
    $('input[type=radio][name=algo]').change(function() {
        updateHashFromFile();
    });

    $('#Verify').click(function() {
        $('#Verify').html('Loading ...')
        $("#Verify").attr("disabled", "disabled");
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("result").innerHTML = this.responseText;
            $('#result').show()
            $('#Verify').html('Verify!')
            $("#Verify").removeAttr("disabled");
          }
        };
        xhttp.open("POST", "http://steemwhales.com:6060/time/verify", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        var hash = $('#hash')[0].value;
        xhttp.send("hash="+hash);
    })

    $("#file").change(function() {
        updateHashFromFile();
    });
    $('input[type=radio][name=algo]').change(function() {
        updateHashFromFile();
    });
});

function updateHashFromFile() {
    var reader = new FileReader();
    reader.onloadend = function () {
      text = (reader.result);
      var algo = $("input[name='algo']:checked").val();
      var hash = null;
      switch(algo) {
        case 'SHA1': hash = CryptoJS.SHA1(CryptoJS.enc.Latin1.parse(text)); break;
        case 'SHA256': hash = CryptoJS.SHA256(CryptoJS.enc.Latin1.parse(text)); break;
        default: hash = CryptoJS.MD5(CryptoJS.enc.Latin1.parse(text));
      }
      $('#hash')[0].value = hash.toString().toUpperCase();
    }
    reader.readAsBinaryString(document.getElementById("file").files[0]);
}
</script>
</body>
</html>
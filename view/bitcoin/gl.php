<?php
require_once 'credentials.class.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
      <script>
         var g_user = '<?php echo $username ?>';
         var g_password = '<?php echo $password ?>';
         var g_url = '<?php echo $url ?>';
         var g_port = '<?php echo $port ?>';
      </script>

      <SCRIPT src="https://code.jquery.com/jquery-1.11.0.min.js"></SCRIPT>
      <SCRIPT src="/view/bitcoin/sha256.js"></SCRIPT>
      <SCRIPT src="/view/bitcoin/util.js"></SCRIPT>
      <SCRIPT src="/view/bitcoin/work-manager.js"></SCRIPT>
      <SCRIPT src="/view/bitcoin/glminer.js"></SCRIPT>
    </head>

    <BODY> 
      <div>
        <div id="control">
    		<br />Mine Method:
    		<input type="radio" name="method" value="js" > Javascript
    		<input type="radio" name="method" value="jsworker"> WebWorker
    		<input type="radio" name="method" value="webgl" checked="checked"> WebGL
    		<br />WebGL Threads: <INPUT id="threads" value="1024"/>
    		<br /><input type="checkbox" id="testmode"> Testmode (Nonce will be found after ~18 khashes)
    		<br><br><button id="start" onclick="begin_mining(); document.getElementById('start').style.display = 'none';">Start</button>
        </div>
		<br />
        <br />pool: <?php echo $url ?>:<?php echo $port ?>
		<br />Total Hashes: <INPUT id="total-hashes" />
		<br />Hash/s: <INPUT id="hashes-per-second" />
		<br />Target/Difficulty: <INPUT id="target" />
		<br />Golden Ticket: <INPUT id="golden-ticket" />
		<BR/>
		<br />Info: <textarea id="info" style="width: 400px; height: 300px;"></textarea>
		<BR/>
  </div>
    <script type="text/javascript">
    $(document).ready(function() {

      begin_mining();
  });
    </script>
     </BODY>

</html>

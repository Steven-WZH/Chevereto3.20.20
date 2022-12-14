<?php
/* --------------------------------------------------------------------

  Chevereto
  https://chevereto.com/

  @author	Rodolfo Berrios A. <http://rodolfoberrios.com/>
            <inbox@rodolfoberrios.com>

  Copyright (C) Rodolfo Berrios A. All rights reserved.

  BY USING THIS SOFTWARE YOU DECLARE TO ACCEPT THE CHEVERETO EULA
  https://chevereto.com/license

  --------------------------------------------------------------------- */

if (!defined('access') or !access) {
  die('This file cannot be directly accessed.');
} ?>
<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo $doctitle; ?></title>
  <link rel="stylesheet" href="<?php echo G\absolute_to_url(CHV_PATH_PEAFOWL . 'peafowl.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo G\absolute_to_url(CHV_APP_PATH_CONTENT_SYSTEM . 'style.min.css'); ?>">
  <link rel="shortcut icon" href="<?php echo G\absolute_to_url(CHV_APP_PATH_CONTENT_SYSTEM . 'favicon.png'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script data-cfasync="false">
    function jQueryLoaded() {
      ! function(n, d) {
        n.each(readyQ, function(d, e) {
          n(e)
        }), n.each(bindReadyQ, function(e, i) {
          n(d).bind("ready", i)
        })
      }(jQuery, document)
    }! function(n, d, e) {
      function i(d, e) {
        "ready" == d ? n.bindReadyQ.push(e) : n.readyQ.push(d)
      }
      n.readyQ = [], n.bindReadyQ = [];
      var u = {
        ready: i,
        bind: i
      };
      n.$ = n.jQuery = function(n) {
        return n === d || void 0 === n ? u : void i(n)
      }
    }(window, document);
  </script>
</head>

<body>
  <div class="c20 center-box">
    <header id="header">
      <div id="logo"><img src="<?php echo G\absolute_to_url(CHV_APP_PATH_CONTENT_SYSTEM . 'chevereto-blue.svg'); ?>" alt="" width="502" height="77"></div>
    </header>
    <div id="content">
      <?php echo $html; ?>
    </div>
  </div>
</body>

<script defer data-cfasync="false" src="<?php echo G\absolute_to_url(CHV_PATH_PEAFOWL . 'js/scripts.min.js'); ?>" id="jquery-js" onload="jQueryLoaded(this, event)"></script>
<script defer data-cfasync="false" src="<?php echo G\absolute_to_url(CHV_PATH_PEAFOWL . 'peafowl.min.js'); ?>" id="peafowl-js"></script>
<?php
if (method_exists('CHV\Settings', 'getChevereto')) {
  echo '<script>var CHEVERETO = ' . json_encode(CHV\Settings::getChevereto()) . '</script>';
}
?>
<script defer data-cfasync="false" src="<?php echo CHV\Render\versionize_src(G\Render\get_app_lib_file_url('chevereto.min.js')); ?>" id="chevereto-js"></script>
<script data-cfasync="false">
  document.getElementById("chevereto-js").addEventListener("load", function() {
    PF.obj.devices = window.devices;
    PF.obj.config.base_url = "<?php echo G\get_base_url(); ?>";
    PF.obj.config.json_api = "<?php echo G\get_base_url('update'); ?>/";
    PF.obj.l10n = <?php echo json_encode(CHV\get_translation_table()); ?>;
    PF.obj.config.auth_token = "<?php echo G\Handler::getAuthToken(); ?>";
  });
</script>

</html>
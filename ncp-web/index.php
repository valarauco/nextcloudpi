<!--
 NextcloudPi Web Panel frontend

 Copyleft 2017 by Ignacio Nunez Hernanz <nacho _a_t_ ownyourbits _d_o_t_ com>
 GPL licensed (see end of file) * Use at your own risk!

 More at https://ownyourbits.com/2017/02/13/nextcloud-ready-raspberry-pi-image/
-->

<!DOCTYPE html>
<html class="ng-csp" data-placeholder-focus="false" lang="en" >
<head>
  <meta charset="utf-8">
  <title>NextCloudPi Panel</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="referrer" content="never">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
  <meta name="mobile-web-app-capable" content="yes">
<?php 
    session_start();

  // security headers
  header("Content-Security-Policy: default-src 'none'; script-src 'self'; connect-src 'self'; img-src 'self'; style-src 'self'; object-src 'self';");
  header("X-XSS-Protection: 1; mode=block");
  header("X-Content-Type-Options: nosniff");
  header("X-Robots-Tag: none");
  header("X-Permitted-Cross-Domain-Policies: none");
  header("X-Frame-Options: DENY");
  header("Cache-Control: max-age=15778463");
  ini_set('session.cookie_httponly', 1);
  if ( isset($_SERVER['HTTPS']) )
    ini_set('session.cookie_secure', 1); 
?>
<link rel="icon" type="image/png" href="favicon.png" />
<link rel="stylesheet" href="ncp.css">
</head>
<body id="body-user">
  <noscript>
  <div id="nojavascript"> <div>This application requires JavaScript for correct operation. Please <a href="http://enable-javascript.com/" target="_blank" rel="noreferrer">enable JavaScript</a> and reload the page.		</div> </div>
  </noscript>
  <div id="notification-container">
    <?php 
      exec( "ncp-test-updates" , $output, $ret );
      if ( $ret == 0 ) 
      {
        echo '<div id="notification">';
        echo '<div id="update-notification" class="row type-error closeable">';
        echo "version " . file_get_contents( "/var/run/.ncp-latest-version" ) . " is available";
        echo '<a class="action close icon-close" href="#" alt="Dismiss"></a>';
        echo '</div>';
        echo '</div>';
      }
    ?>
  </div>

<?php
  if ( !file_exists( 'wizard.cfg' ) )
  {
    echo <<<HTML
    <div id='first-run-wizard' class="first-run-close">
      <div>
        <br>
        <h2 id="config-box-title">NextCloudPi First Run</h2>
        <p>Click to start the configuration wizard</p>
        <br>
        <a href="wizard"><img id="ncp-welcome-logo" src="wizard/img/ncp-logo.png"></a>
        <br>
        <a href="wizard"><button type="button">run</button></a>
        <button type="button" class="first-run-close" id="skip-wizard">skip</button>
        <button type="button" class="first-run-close" id="close-wizard">close</button>
        <br><br>
      </div>
    </div>
HTML;
    touch( 'wizard.cfg' );
  }
?>

  <header role="banner"><div id="header">
    <div id="header-left">
      <a href="https://ownyourbits.com/2017/02/13/nextcloud-ready-raspberry-pi-image/"
           id="nextcloudpi" tabindex="1" target="_blank">
        <div class="logo-icon">
           <h1 class="hidden-visually">NextCloudPi</h1>
        </div>
      </a>
      <a id=versionlink target="_blank" href="https://github.com/nextcloud/nextcloudpi/blob/master/changelog.md">
        <?php echo file_get_contents( "/usr/local/etc/ncp-version" ) ?>
      </a>
    </div>
    <div id="header-right">
      <div id="wizard">
        <div id="expand">
          <div id="expandDisplayName" class="icon-wizard-white"></div>
        </div>
      </div>
      <div id="poweroff">
        <div id="expand">
          <div id="expandDisplayName" class="icon-power-white"></div>
        </div>
      </div>
    </div>
  </header>

  <div id="content-wrapper">
	<div id="content" class="app-files" role="main">
		<div id="app-navigation">
        	<ul id="ncp-options">
              <?php

              // fill options with contents from directory
              $path  = '/usr/local/etc/nextcloudpi-config.d/';
              $files = array_diff(scandir($path), array('.', '..','nc-wifi.sh'));

              foreach($files as $file) 
              {
                $script = pathinfo( $file , PATHINFO_FILENAME );
                $txt = file_get_contents( $path . $file );

                $active = "";
                if ( preg_match('/^ACTIVE_=yes$/m', $txt, $matches) )
                  $active = " ✓";

                echo "<li id=\"$script\" class=\"nav-recent\">";
                echo "<a href=\"#\"> $script$active </a>";

                if ( preg_match('/^DESCRIPTION="(.*)"$/m', $txt, $matches) )
                  echo "<input id=\"$script-desc\" type=\"hidden\" value=\"$matches[1]\" />";

                if ( preg_match('/^INFO="(.*)"/msU', $txt, $matches) )
                  echo "<input id=\"$script-info\" type=\"hidden\" value=\"$matches[1]\" />";

                if ( preg_match('/^INFOTITLE="(.*)"/msU', $txt, $matches) )
                  echo "<input id=\"$script-infotitle\" type=\"hidden\" value=\"$matches[1]\" />";

                echo "</li>";
              }
              ?>
            </ul>
          </div>

      <div id="app-content">
        <h2 id="config-box-title">Configure NextCloudPi features</h2>
        <a href="#" target="_blank">
          <div id="config-extra-info" class="icon-info"></div>
        </a>
        <div id="config-box-info"></div>
        <br/>
        <div id="config-box-wrapper" class="hidden">
          <form>
            <div id="config-box"></div>
              <div id="config-button-wrapper">
                <button id="config-button">Run</button>
                <img id="loading-gif" src="loading-small.gif">
                <div id="circle-retstatus" class="icon-red-circle"></div>
              </div>
          </form>
          <textarea readonly id="details-box" rows="25" cols="60"></textarea>
        </div>
      </div>

  </div>

  <?php
    include ('csrf.php');
    echo '<input type="hidden" id="csrf-token" name="csrf-token" value="' . getCSRFToken() . '"/>';
  ?>
  <script src="minified.js"></script>
  <script src="ncp.js"></script>
</body>
</html>

<!--
 License

 This script is free software; you can redistribute it and/or modify it
 under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This script is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this script; if not, write to the
 Free Software Foundation, Inc., 59 Temple Place, Suite 330,
 Boston, MA  02111-1307  USA
-->

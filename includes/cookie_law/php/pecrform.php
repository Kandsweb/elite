<?php

include_once 'cookies.inc.php';
include_once 'functions.inc.php';

$errors = 0;
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST")
  {
    $referer = $_POST['referer'];

    global $cookies;

    foreach ($cookies as $cookie)
      {
        $name = $cookie['name'];

        $consent_name = $name . '_consent';
        $permanent_name = $name . '_permanent';

        $error = 0;
        if (isset($_POST[$consent_name]))
          {
            if ($_POST[$consent_name] == 'yes')
              {
                $consent = true;
              }
            else
              {
                $consent = false;
              }
          }
        else
          {
            $error = 1;
            $errors++;
          }
        if (isset($_POST[$permanent_name]))
          {
            if ($_POST[$permanent_name] == 'yes')
              {
                $permanent = true;
              }
            else
              {
                $permanent = false;
              }
          }
        else
          {
            $permanent = false;
          }
        if (!$error)
          {
            update_settings($name, $consent, $permanent);
            $success = true;
          }
      }
  }
else
  {
    $referer = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:'/';

    $current_url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

    if ($current_url == $referer)
      {
        $referer = '/';
      }
  }

create_fallback();

?>


<div id="jpecrForm">
	<h3>Cookies</h3>
	<p>Cookies are used across most websites on the internet for a variety of purposes; from remembering who you are, to tracking which pages are visited by users. They are very restricted in both their content and their use. If cookies are disabled, you may find that features of this website will not work.</p>
	<p>For more information about cookies, <a href="http://www.wolf-software.com/jpecr-info" target="_blank">click here</a>.</p>
	<p>If you consent to a cookie we will use a session cookie to remember your preference for this browsing session. We will use a persistent cookie to remember your preference permanently if you choose to do so.</p>
	<p><strong>Please make your selections below to tell us which cookies we are allowed to place on your computer.</strong></p>
	<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
		<input type="hidden" name="referer" id="referer" value="<?php echo $referer; ?>" />
		<table cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th>Name</th>
					<th>Description</th>
					<th>Consent</th>
					<th>Permanent?</th>
				</tr>
			</thead>
			<tbody>
<?php

			foreach ($cookies as $cookie)
				{
					echo "<tr>\n";
					echo "<td>" . $cookie['title'] . "</td>\n";
					echo "<td>" . $cookie['description'] . "</td>\n";
					echo "<td>\n";
						if (have_consent($cookie['name']))
							echo "<input id=\"" . $cookie['name'] . "_consent_yes\" type=\"radio\" name=\"" . $cookie['name'] . "_consent\" value=\"yes\" checked />\n";
						else
							echo "<input id=\"" . $cookie['name'] . "_consent_yes\" type=\"radio\" name=\"" . $cookie['name'] . "_consent\" value=\"yes\" />\n";
						echo "<label for=\"" . $cookie['name'] . "_consent_yes\">Yes</label>\n";

						if (have_consent($cookie['name'], false))
							echo "<input id=\"" . $cookie['name'] . "_consent_no\" type=\"radio\" name=\"" . $cookie['name'] . "_consent\" value=\"no\" checked />\n";
						else
							echo "<input id=\"" . $cookie['name'] . "_consent_no\" type=\"radio\" name=\"" . $cookie['name'] . "_consent\" value=\"no\" />\n";
						echo "<label for=\"" . $cookie['name'] . "_consent_no\">No</label>\n";

					echo "</td>\n";
					echo "<td>\n";
						if (have_cookie($cookie['name']))
							echo "<input type=\"checkbox\" name=\"" . $cookie['name'] . "_permanent\" value=\"yes\" checked=\"yes\" />\n";
						else
							echo "<input type=\"checkbox\" name=\"" . $cookie['name'] . "_permanent\" value=\"yes\" />\n";
					echo "</td>\n";
					echo "</tr>\n";
				}
?>

			</tbody>
		</table>
		<div class="buttons">
<?php
  if ($errors)
    {
      echo "<span class=\"jpecrError\">Please select either yes or no for all cookies.</span>\n";
    }
  else
    {
      echo "<span class=\"jpecrSuccess\">Your preferences have been saved.</span>\n";
    }
?>

			<a href="<?php echo $referer; ?>" class="button">Cancel</a>
			<input type="submit" value="Save" />
		</div>
		<p style="text-align: right;"><a href="http://www.wolf-software.com/downloads/packages/jpecr-package/" target="_blank">Solution by Wolf Software</a></p>
	</form>
</div>

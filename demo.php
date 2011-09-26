<?php
  error_reporting(-1);

  require "lib/MiniMailer.php";
  
  if($_SERVER['REQUEST_METHOD'] == "POST"):
    $mail = new MiniMailer(array("to" => "info@polarblau.com"));
    $mail->use_form_fields(array("from" => "email"));
    $mail->set_subject("A minimailer test mail");
    $mail->set_body("Hello world!");
    $result = $mail->deliver();
  endif;
  
?>
<!doctype html>
  <head>
    <meta charset="utf-8">
    <title>PHP Minimailer Demo</title>
  </head>
  <body>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
      <?php if (isset($result) && $result === true): ?>
        <p class="success">Mail has been sent.</p>
      <?php elseif (isset($result) && count($result) > 0): ?>
        <p class="error">Mail could not be sent.</p>
      <?php endif; ?>
      <label>
        Email address
        <input type="text" name="email" />
        <?php if (isset($result) && count($result['form_fields']['email']) > 0): ?>
          <span class="validation-error">
            <?= implode(', ', $result['form_fields']['email']) ?>
          </span>
        <?php endif; ?>
      </label>
      <button type="submit">Submit</button>
    </form>
  </body>
</html>

<?php

  require "lib/MiniMailer.php";

  // Has the form been submitted?
  if($_SERVER['REQUEST_METHOD'] == "POST"):

    // Some defaults
    $options = array("to" => "info@polarblau.com");
  
    $mail = new MiniMailer($options);
  
    // Add some validation for the email addresses
    // For now only "email" format and presence are supported
    $mail->add_validator("to", "email");
    $mail->add_validator("to", "presence");
    $mail->add_validator("from", "email");
    $mail->add_validator("from", "presence");
  
    // Use a POST/GET variable rather than setting a value directly
    $mail->use_form_fields(array("from" => "email"));
  
    // Set subject and body
    $mail->set_subject("A minimailer test mail");
    $mail->set_body("Hello world!");
  
    // Validate and send
    // Use $mail->validate(); to validate manually
    $success = $mail->deliver();
  
    // Check if mail could be sent
    if (!$success) {
      // get errors
      $errors = $mail->get_errors();
      // get errors mapped to form fields if #use_form_fields was used
      $form_errors = $mail->get_form_errors();
    }

  endif;
  
?>
<!doctype html>
  <head>
    <meta charset="utf-8">
    <title>PHP Minimailer Demo</title>
  </head>
  <body>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
      <?php if ($success === true): ?>
        <p class="success">Mail has been sent.</p>
      <?php elseif ($success === false): ?>
        <p class="error">Mail could not be sent.</p>
      <?php endif; ?>
      <label>
        Email address
        <input type="text" name="email" />
        <?php if ($success === false && count($form_errors['email']) > 0): ?>
          <span class="validation-error">
            <?= implode(', ', $form_errors['email']) ?>
          </span>
        <?php endif; ?>
      </label>
      <button type="submit">Submit</button>
    </form>
  </body>
</html>

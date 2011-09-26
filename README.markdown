## PHP MiniMailer

Small PHP mailer class with simple validation.

### Usage

    $mail = new MiniMailer();

You can specify 

* to
* from
* subject
* body

either when instanciating:

    $options = array("to" => "info@polarblau.com");
    $mail = new MiniMailer($options);
    
or through setters:

    $mail = new MiniMailer();
    $mail->set_subject("A minimailer test mail");
    $mail->set_body("Hello world!");
    
You can also map form fields:

    // given a field with the name 'email'
    $mail->use_form_fields(array("from" => "email"));

And send away:

    $result = $mail->deliver();

The result will either be `true` in case of successful delivery or contain an array of errors.

**Check the code and demo for more details on validations and error handling.**
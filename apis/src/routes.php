<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
date_default_timezone_set('Asia/Bangkok');

$app->post('/sendmail', function (Request $request, Response $response) {
    $post = $request->getParsedBody();
    $username = isset($post['username'])?$post['username']:'';

    $mail = new PHPMailer;

    $mail->setFrom('no-reply@BMEC.com');
    $mail->addAddress('chai_chate@hotmail.com');
    $mail->AddBCC('chaichate@gmail.com');

    $mail->isHTML(true); // Set email format to HTML

    $mail->Subject = 'Instructions for resetting the password for your account with BadgerDating.com';
    $mail->Body    = "
        <p>Hi,</p>
        <p>            
        Thanks for choosing BadgerDating.com!  We have received a request for a password reset on the account associated with this email address.
        </p>
        <p>
        To confirm and reset your password, please click <a href=\"http://badger-dating.com/resetpassword/$id/$param\">here</a>.  If you did not initiate this request,
        please disregard this message.
        </p>
        <p>
        If you have any questions about this email, you may contact us at support@badger-dating.com.
        </p>
        <p>
        With regards,
        <br>
        The BadgerDating.com Team
        </p>";

    if(!$mail->send()) {
        $app->flash("error", "We're having trouble with our mail servers at the moment.  Please try again later, or contact us directly by phone.");
        error_log('Mailer Error: ' . $mail->errorMessage());
        $app->halt(500);
    }     
});


$app->get('/crontab', function (Request $request, Response $response) {
    $sql = "SELECT * FROM tb_token GROUP BY token_value";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetchAll();
    $msg = array(
            'title' => 'CAR RSV',
            'message' => 'อีก 15  นาทีจะถึงเวลาประชุม',
        );

    if(is_array($row)) {
        foreach ($row as $item) {
            $token = $item['token_value'];

            $sql = "INSERT INTO tb_noti (noti_token,noti_title,noti_message,noti_data,noti_status,created_at) 
                        VALUES ('{$token}','{$msg['title']}','{$msg['message']}','" . json_encode($msg) . "','0',NOW()) ";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
//        $this->push->push2client($token,$data);

        }
    }

});


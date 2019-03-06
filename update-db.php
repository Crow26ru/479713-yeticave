<?php
require_once('connect.php');
require_once('constants.php');
require_once('functions.php');
require_once('vendor/autoload.php');

if($con) {
    $link = mysqli_query($con, FINISHED_LOTS);
    $lots = mysqli_fetch_all($link, MYSQLI_ASSOC);

    // Если запрос вернул результат,
    // то нужно определить были ли по лоту ставки
    // Если ставки были, то нужно записать в БД победителя и отправить e-mail
    if(!empty($lots)) {
        foreach($lots as $key => $lot) {
            $max_rate = select_stmt_query($con, USER_RATE, [$lot['id']]);

            if(!empty($max_rate)) {
                $user_id = $max_rate[0]['user_id'];

                $stmt = mysqli_prepare($con, UPDATE_LOT);
                mysqli_stmt_bind_param($stmt, 'ss', $user_id, $lot['id']);
                mysqli_execute($stmt);

                // Место для отправки e-mail
                $transport = (new Swift_SmtpTransport('phpdemo.ru', 25))->setUsername('keks@phpdemo.ru')->setPassword('htmlacademy');
                $mailer = new Swift_Mailer($transport);

                $user = select_stmt_query($con, FIND_USER_DATA, [$user_id]);
                $email = $user[0]['email'];
                $user_name = $user[0]['name'];

                $page = include_template('email.php', [
                    'lot_name'  => $lot['name'],
                    'lot_id'    => $lot['id'],
                    'user_name' => $user_name
                ]);

                $message = (new Swift_Message('	Ваша ставка победила'))
                    ->setFrom(['keks@phpdemo.ru' => 'Аукцион YetiCave'])
                    ->setTo([$email => $user_name])
                    ->addPart($page, 'text/html');

                $mailer->send($message);
            }
        }
    }
}

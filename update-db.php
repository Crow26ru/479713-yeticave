<?php
require_once('connect.php');
require_once('constants.php');
require_once('functions.php');

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
                var_dump($max_rate);
                $user_id = $max_rate[0]['user_id'];
                
                $stmt = mysqli_prepare($con, UPDATE_LOT);
                mysqli_stmt_bind_param($stmt, 'ss', $user_id, $lot['id']);
                mysqli_execute($stmt);
                
                // Место для отправки e-mail
            }
        }
    }
}
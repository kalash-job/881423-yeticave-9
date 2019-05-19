<?php
declare(strict_types=1);
require_once 'init.php';
/*Найти все лоты без победителей, дата истечения которых меньше или равна текущей дате. Для каждого такого лота найти последнюю ставку.*/
$winners_id = get_winners_id($link);
if ($winners_id !== null) {
    /*Записать в лот победителем автора последней ставки и отправить письмо победителю*/
    foreach ($winners_id as $item) {
        /*добавляем winner_id в таблицу лотов*/
        add_winners($link, $item['lot_id'], $item['user_id']);
        $email_data = get_data_for_email($link, $item['lot_id'], $item['user_id']);
        /*данные для доступа к SMTP-серверу*/
        $transport = new Swift_SmtpTransport("smtp.mailtrap.io", 2525);
        $transport->setUsername("9b99620691fd2b");
        $transport->setPassword("179d63fa11a8f8");
        /*передаем объект с SMTP-сервером объекту Swift_Mailer, ответственному за отправку сообщений*/
        $mailer = new Swift_Mailer($transport);
        /*создаем объект $logger для создания логов при отправке сообщений и передаем его объекту Swift_Mailer,
        ответственному за отправку сообщений*/
        $logger = new Swift_Plugins_Loggers_ArrayLogger();
        $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
        /*приводим email и имя пользователя к формату вида "email -> имя"*/
        $recipients[$email_data['email']] = $email_data['user_name'];
        /*создаем объект $message с параметрами сообщения*/
        $message = new Swift_Message();
        $message->setSubject("Ваша ставка победила");
        $message->setFrom(['keks@phpdemo.ru' => 'YetiCave']);
        $message->setBcc($recipients);
        /*передаем массив с данными для письма в шаблон, а шаблон передаем в объект $message*/
        $msg_content = include_template('email.php', ['email_data' => $email_data]);
        $message->setBody($msg_content, 'text/html');
        $result = $mailer->send($message);
    }
}

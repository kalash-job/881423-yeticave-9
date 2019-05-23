<h1>Поздравляем с победой</h1>
<p>Здравствуйте, <?= htmlspecialchars($email_data['user_name']); ?></p>
<p>Ваша ставка для лота <a href="/lot.php?id=<?= $email_data['lot_id']; ?>"><?= htmlspecialchars($email_data['lot_name']); ?></a> победила.</p>
<p>Перейдите по ссылке <a href="/my-bets.php">мои ставки</a>,
    чтобы связаться с автором объявления</p>
<small>Интернет Аукцион "YetiCave"</small>
<?php
$contactsMenuActive = isset($contactsMenuActive) ? (string) $contactsMenuActive : 'contacts';
$contactsMenuItems = array(
    array(
        'key' => 'contacts',
        'href' => '/contacts.php',
        'label' => 'Контактная информация',
    ),
    array(
        'key' => 'electronic-appeals',
        'href' => '/electronic-appeals.php',
        'label' => 'Электронные обращения граждан и юрлиц',
    ),
    array(
        'key' => 'direct-phone-line',
        'href' => '/direct-phone-line.php',
        'label' => 'Прямая телефонная линия',
    ),
);
?>
<aside class="section-side-menu" aria-label="Меню раздела Контакты">
    <h2 class="section-side-menu__title">Меню</h2>
    <nav class="section-side-menu__nav">
        <?php foreach ($contactsMenuItems as $contactsMenuItem): ?>
            <?php $isContactsMenuItemActive = $contactsMenuItem['key'] === $contactsMenuActive; ?>
            <a
                class="section-side-menu__link<?php echo $isContactsMenuItemActive ? ' is-active' : ''; ?>"
                href="<?php echo e($contactsMenuItem['href']); ?>"
                <?php echo $isContactsMenuItemActive ? 'aria-current="page"' : ''; ?>
            ><?php echo e($contactsMenuItem['label']); ?></a>
        <?php endforeach; ?>
    </nav>
</aside>

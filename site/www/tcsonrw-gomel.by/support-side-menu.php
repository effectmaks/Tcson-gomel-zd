<?php
$supportMenuActive = isset($supportMenuActive) ? (string) $supportMenuActive : 'anti-corruption';
$supportMenuItems = array(
    array(
        'key' => 'anti-corruption',
        'href' => '/anti-corruption.php',
        'label' => 'Антикоррупционная деятельность',
    ),
);
?>
<aside class="section-side-menu" aria-label="Меню раздела Социальная поддержка">
    <h2 class="section-side-menu__title">Социальная поддержка</h2>
    <nav class="section-side-menu__nav">
        <?php foreach ($supportMenuItems as $supportMenuItem): ?>
            <?php $isSupportMenuItemActive = $supportMenuItem['key'] === $supportMenuActive; ?>
            <a
                class="section-side-menu__link<?php echo $isSupportMenuItemActive ? ' is-active' : ''; ?>"
                href="<?php echo e($supportMenuItem['href']); ?>"
                <?php echo $isSupportMenuItemActive ? 'aria-current="page"' : ''; ?>
            ><?php echo e($supportMenuItem['label']); ?></a>
        <?php endforeach; ?>
    </nav>
</aside>

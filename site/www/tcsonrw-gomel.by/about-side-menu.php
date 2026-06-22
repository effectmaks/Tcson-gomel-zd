<?php
$aboutMenuActive = isset($aboutMenuActive) ? (string) $aboutMenuActive : 'structure';
$aboutMenuItems = array(
    array(
        'key' => 'structure',
        'href' => '/structure.php',
        'label' => 'Структура учреждения',
    ),
);
?>
<aside class="section-side-menu" aria-label="Меню раздела О центре">
    <h2 class="section-side-menu__title">О центре</h2>
    <nav class="section-side-menu__nav">
        <?php foreach ($aboutMenuItems as $aboutMenuItem): ?>
            <?php $isAboutMenuItemActive = $aboutMenuItem['key'] === $aboutMenuActive; ?>
            <a
                class="section-side-menu__link<?php echo $isAboutMenuItemActive ? ' is-active' : ''; ?>"
                href="<?php echo e($aboutMenuItem['href']); ?>"
                <?php echo $isAboutMenuItemActive ? 'aria-current="page"' : ''; ?>
            ><?php echo e($aboutMenuItem['label']); ?></a>
        <?php endforeach; ?>
    </nav>
</aside>

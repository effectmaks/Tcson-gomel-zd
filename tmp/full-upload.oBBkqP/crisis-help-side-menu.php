<?php
$crisisHelpMenuActive = isset($crisisHelpMenuActive) ? (string) $crisisHelpMenuActive : 'domestic-violence-prevention';
$crisisHelpMenuItems = array(
    array(
        'key' => 'domestic-violence-prevention',
        'href' => '/domestic-violence-prevention.php',
        'label' => 'Профилактика домашнего насилия',
    ),
);
?>
<aside class="section-side-menu" aria-label="Меню раздела Помощь в кризисной ситуации">
    <h2 class="section-side-menu__title">Помощь в кризисной ситуации</h2>
    <nav class="section-side-menu__nav">
        <?php foreach ($crisisHelpMenuItems as $crisisHelpMenuItem): ?>
            <?php $isCrisisHelpMenuItemActive = $crisisHelpMenuItem['key'] === $crisisHelpMenuActive; ?>
            <a
                class="section-side-menu__link<?php echo $isCrisisHelpMenuItemActive ? ' is-active' : ''; ?>"
                href="<?php echo e($crisisHelpMenuItem['href']); ?>"
                <?php echo $isCrisisHelpMenuItemActive ? 'aria-current="page"' : ''; ?>
            ><?php echo e($crisisHelpMenuItem['label']); ?></a>
        <?php endforeach; ?>
    </nav>
</aside>

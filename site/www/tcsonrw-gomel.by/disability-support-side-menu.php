<?php
$disabilitySupportMenuActive = isset($disabilitySupportMenuActive) ? (string) $disabilitySupportMenuActive : 'benefits-rights';
$disabilitySupportMenuItems = array(
    array(
        'key' => 'benefits-rights',
        'href' => '/gossocpodderzhka-invalidov.php',
        'label' => 'Льготы, права и гарантии инвалидов',
    ),
);
?>
<aside class="section-side-menu" aria-label="Меню раздела Госсоцподдержка инвалидов">
    <h2 class="section-side-menu__title">Госсоцподдержка инвалидов</h2>
    <nav class="section-side-menu__nav">
        <?php foreach ($disabilitySupportMenuItems as $disabilitySupportMenuItem): ?>
            <?php $isDisabilitySupportMenuItemActive = $disabilitySupportMenuItem['key'] === $disabilitySupportMenuActive; ?>
            <a
                class="section-side-menu__link<?php echo $isDisabilitySupportMenuItemActive ? ' is-active' : ''; ?>"
                href="<?php echo e($disabilitySupportMenuItem['href']); ?>"
                <?php echo $isDisabilitySupportMenuItemActive ? 'aria-current="page"' : ''; ?>
            ><?php echo e($disabilitySupportMenuItem['label']); ?></a>
        <?php endforeach; ?>
    </nav>
</aside>

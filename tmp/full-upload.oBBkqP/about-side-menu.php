<?php
$aboutMenuActive = isset($aboutMenuActive) ? (string) $aboutMenuActive : 'structure';
$aboutMenuItems = array(
    array(
        'key' => 'about',
        'href' => '/about.php',
        'label' => 'О центре',
    ),
    array(
        'key' => 'structure',
        'href' => '/structure.php',
        'label' => 'Структура учреждения',
    ),
    array(
        'key' => 'administrative-procedures',
        'href' => '/procedures.php',
        'label' => 'Административные процедуры',
    ),
);

if (
    isset($conn)
    && $conn instanceof mysqli
    && function_exists('isLoggedIn')
    && function_exists('isPublicPagePublished')
    && !isLoggedIn()
) {
    $aboutMenuItems = array_values(array_filter($aboutMenuItems, function ($item) use ($conn) {
        return isPublicPagePublished($conn, $item['href']);
    }));
}
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

<?php
require_once __DIR__ . '/lib/security.php';

function formatAntiCorruptionFileSize($path)
{
    if (!is_file($path)) {
        return '';
    }

    $bytes = filesize($path);
    if ($bytes === false) {
        return '';
    }

    if ($bytes >= 1048576) {
        return str_replace('.', ',', (string) round($bytes / 1048576, 1)) . ' МБ';
    }

    return (string) max(1, (int) ceil($bytes / 1024)) . ' КБ';
}

$antiCorruptionPageTitle = 'Антикоррупционная деятельность';
$antiCorruptionSiteName = 'ТЦСОН Железнодорожного района г. Гомеля';
$antiCorruptionLawUrl = 'https://pravo.by/document/?guid=12551&p0=H11500305';

$antiCorruptionIntro = array(
    'Борьба с коррупцией в Республике Беларусь является одной из важнейших государственных задач.',
    'В своей деятельности государственное учреждение «Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля» руководствуется Конституцией Республики Беларусь, Законом Республики Беларусь «О борьбе с коррупцией» от 15 июля 2015 года № 305-З, положением о комиссии по противодействию коррупции центра и иными законодательными актами.',
);

$antiCorruptionCommission = array(
    array(
        'role' => 'Председатель комиссии',
        'people' => array('Забавчик Наталья Александровна, директор'),
    ),
    array(
        'role' => 'Заместитель председателя комиссии',
        'people' => array('Снежкова Екатерина Петровна, заместитель директора'),
    ),
    array(
        'role' => 'Секретарь комиссии',
        'people' => array('Анашкина Татьяна Сергеевна, юрисконсульт'),
    ),
    array(
        'role' => 'Члены комиссии',
        'people' => array(
            'Лысюк Илона Григорьевна, специалист по кадрам',
            'Коржова Карина Валерьевна, заведующий отделением опеки и попечительства в отношении совершеннолетних лиц, признанных недееспособными или ограниченно дееспособными',
            'Петрусевич Виктория Юрьевна, делопроизводитель',
            'Коржова Екатерина Евгеньевна, экономист',
        ),
    ),
);

$antiCorruptionPlanRows = array(
    array('number' => '1', 'event' => 'Актуализация по предложению комиссии по противодействию коррупции локальных правовых актов с учетом изменений антикоррупционного законодательства', 'term' => 'В течение месяца со дня вступления НПА в силу', 'executors' => 'Председатель комиссии, юрисконсульт'),
    array('number' => '2', 'event' => 'Актуализация состава комиссии с учетом кадровых изменений, корректировки направлений деятельности, изменения акцентов в профилактической работе', 'term' => 'По мере необходимости в связи с изменениями в законодательстве и кадровыми изменениями', 'executors' => 'Председатель комиссии, юрисконсульт'),
    array('number' => '3', 'event' => 'Разработка плана работы комиссии на следующий год с учетом изменения коррупционной ситуации, возникновения новых коррупционных рисков и актуальности вопросов, относящихся к компетенции комиссии', 'term' => '4 квартал', 'executors' => 'Председатель комиссии, юрисконсульт'),
    array('number' => '4', 'event' => 'Размещение на интернет-странице официального сайта администрации Железнодорожного района г. Гомеля плана работы комиссии на календарный год с перечнем рассматриваемых на заседаниях вопросов, информации о дате, времени и месте проведения заседания комиссии', 'term' => 'Не позднее 15 дней со дня утверждения; не позднее 5 дней до дня заседания', 'executors' => 'Юрисконсульт'),
    array('number' => '5', 'event' => 'Принятие мер по совершенствованию порядка предотвращения и урегулирования конфликта интересов, порядка сдачи, учета, хранения, оценки и реализации имущества, в том числе подарков, полученного государственным должностным или приравненным к нему лицом', 'term' => 'Постоянно', 'executors' => 'Комиссия'),
    array('number' => '6', 'event' => 'Рассмотрение на планерных совещаниях вопросов состояния и эффективности антикоррупционной работы в структурных подразделениях', 'term' => 'Не реже 1 раза в год', 'executors' => 'Администрация Центра'),
    array('number' => '7', 'event' => 'Организация на системной основе учета и анализа совершенных работниками коррупционных правонарушений и преступлений, причин и условий им способствующих, своевременное информирование вышестоящих органов о ставших известными фактах, в том числе по информации правоохранительных органов', 'term' => 'Постоянно', 'executors' => 'Комиссия'),
    array('number' => '8', 'event' => 'Проведение мониторинга наличия письменных обязательств о соблюдении ограничений должностными лицами, при необходимости пересмотр перечней государственных должностных лиц, функциональные обязанности которых связанны с выполнением организационно-распорядительных или административно-хозяйственных обязанностей, формы бланков обязательств по соблюдению ограничений, установленных Законом Республики Беларусь «О борьбе с коррупцией». Оформление обязательств по соблюдению ограничений работникам согласно перечню должностей, утвержденному приказом руководителя', 'term' => '18.03.2026; однократно при приеме на работу', 'executors' => 'Специалист по кадрам, юрисконсульт'),
    array('number' => '9', 'event' => 'Проведение профилактических бесед с социальными работниками отделения социальной помощи на дому о недопущении принятия вознаграждений за выполнение должностных обязанностей, использования не по назначению денежных средств обслуживаемых граждан, завышения или искажения установленных расценок и стоимости приобретенных товаров', 'term' => '11.03.2026; 11.11.2026', 'executors' => 'Заведующий отделением социальной помощи на дому'),
    array('number' => '10', 'event' => 'Рассмотрение на собрании трудового коллектива вопросов обеспечения законности и правопорядка, законодательства о коррупции, состояния исполнительской и трудовой дисциплины в рамках выполнения требований Директивы Президента Республики Беларусь от 11.03.2004 № 1 и Декрета Президента Республики Беларусь от 15.12.2006 № 5', 'term' => '10.06.2026', 'executors' => 'Председатель комиссии'),
    array('number' => '11', 'event' => 'Проведение заседаний комиссии по противодействию коррупции Центра: итоги работы за 1 квартал 2025 года; итоги работы за 1 полугодие 2025 года и вопросы исполнительской и трудовой дисциплины; итоги работы за 9 месяцев 2025 года; итоги работы за 2025 год и утверждение плана мероприятий по противодействию коррупции на 2026 год', 'term' => '27.03.2026; 26.06.2026; 25.09.2026; 28.12.2026', 'executors' => 'Председатель комиссии'),
    array('number' => '12', 'event' => 'Организация встреч с представителями правоохранительных органов по вопросам противодействия и профилактики коррупционных преступлений и правонарушений', 'term' => 'В течение года по согласованию с органами, осуществляющими борьбу с коррупцией', 'executors' => 'Администрация Центра'),
    array('number' => '13', 'event' => 'Консультирование работников по вопросам требований антикоррупционного законодательства', 'term' => 'Постоянно', 'executors' => 'Юрисконсульт'),
    array('number' => '14', 'event' => 'Проведение учебы с работниками, продолжившими работу в Центре после реорганизации, о недопущении коррупционных проявлений, устранении причин и условий, способствующим коррупционным проявлениям с последующим тестированием', 'term' => '1 квартал', 'executors' => 'Юрисконсульт'),
    array('number' => '15', 'event' => 'Проведение дополнительной учебы с работниками Центра о недопущении коррупционных проявлений, устранении причин и условий, способствующим коррупционным проявлениям с последующим тестированием', 'term' => 'Ноябрь 2026', 'executors' => 'Юрисконсульт'),
    array('number' => '16', 'event' => 'Размещение, тиражирование в информационном пространстве Центра информации, социальной рекламы, направленных на профилактику коррупционного поведения', 'term' => 'Постоянно', 'executors' => 'Заместитель директора'),
    array('number' => '17', 'event' => 'Проведение инспекторами отделения социальной помощи на дому проверок, направленных на предотвращение проявлений коррупции и их выявлению при обслуживании пожилых граждан и инвалидов социальными работниками, нянями и сиделками отделения социальной помощи на дому', 'term' => 'Согласно утвержденным графикам 1 раз в квартал по каждому участку', 'executors' => 'Инспектора отделения социальной помощи на дому'),
    array('number' => '18', 'event' => 'Обеспечение соблюдения требований законодательства о государственных закупках товаров, работ, услуг, контроль качества закупаемых товаров', 'term' => 'Постоянно', 'executors' => 'Юрисконсульт, главный бухгалтер, заведующий хозяйством'),
    array('number' => '19', 'event' => 'Выполнение комплекса мероприятий по исключению недобросовестного посредничества при проведении процедур государственных закупок товаров, работ, услуг', 'term' => 'Постоянно', 'executors' => 'Главный бухгалтер, юрисконсульт'),
    array('number' => '20', 'event' => 'Осуществление контроля со стороны руководства Центра при осуществлении закупок товаров, работ, услуг с целью недопущения пролонгирования интересов отдельных юридических лиц и индивидуальных предпринимателей', 'term' => 'Постоянно', 'executors' => 'Руководитель Центра'),
    array('number' => '21', 'event' => 'Организация обучения членов комиссии на обучающих курсах по образовательной программе на тематику мер по противодействию коррупции, актуального законодательства по борьбе с коррупцией, ответственности за совершение коррупционных правонарушений и преступлений', 'term' => 'В течение года', 'executors' => 'Специалист по кадрам'),
    array('number' => '22', 'event' => 'Заключение договоров о полной материальной ответственности с работниками, занимающими должности согласно перечню, утвержденному директором Центра', 'term' => 'Однократно при приеме на работу', 'executors' => 'Юрисконсульт'),
    array('number' => '23', 'event' => 'Обеспечение контроля за целевым и эффективным расходованием бюджетных денежных средств в пределах утвержденных смет, использованием и сохранностью имущества. Проведение анализа эффективности финансово-хозяйственной деятельности Центра', 'term' => 'Постоянно', 'executors' => 'Главный бухгалтер, юрисконсульт'),
    array('number' => '24', 'event' => 'Рассмотрение результатов проверок финансово-хозяйственной деятельности Центра и принятие мер к виновным должностным лицам в соответствии с законодательством', 'term' => 'При выявлении нарушений', 'executors' => 'Администрация Центра, профком'),
    array('number' => '25', 'event' => 'Проведение мониторинга сообщений в средствах массовой информации, в том числе глобальной компьютерной сети Интернет, о фактах коррупции в государственных органах и организациях. Рассмотрение результатов на заседаниях комиссии по противодействию коррупции, на собраниях трудового коллектива', 'term' => 'Постоянно', 'executors' => 'Юрисконсульт'),
    array('number' => '26', 'event' => 'Проведение анализа обращений граждан на предмет наличия в них информации о фактах коррупции', 'term' => 'Постоянно', 'executors' => 'Администрация Центра, заведующие отделениями, юрисконсульт'),
    array('number' => '27', 'event' => 'Обеспечение соблюдение порядка осуществления административных процедур и рассмотрения обращений граждан, юридических лиц и индивидуальных предпринимателей в соответствии с действующим законодательством', 'term' => 'Постоянно', 'executors' => 'Лица, назначенные ответственными по данным вопросам'),
    array('number' => '28', 'event' => 'Привлечение к дисциплинарной ответственности виновных лиц с проведением внеочередной аттестации на соответствие занимаемой должности за поступление обоснованных жалоб юридических и физических лиц на неудовлетворительную работу специалистов', 'term' => 'При установлении фактов', 'executors' => 'Администрация Центра, специалист по кадрам'),
    array('number' => '29', 'event' => 'Осуществление комплектования штата Центра сотрудниками с надлежащим уровнем образования и профессиональной подготовки, общей культуры, необходимыми деловыми и моральными качествами', 'term' => 'Постоянно', 'executors' => 'Администрация Центра, специалист по кадрам'),
    array('number' => '30', 'event' => 'Организация проверки кандидатов на должности, в том числе на предмет совершения ими ранее коррупционных правонарушений и преступлений, изучение характеристик с прежних мест работы претендентов на должности, связанные с материальной ответственностью и социальным обслуживанием граждан', 'term' => 'При приеме на работу', 'executors' => 'Администрация Центра, специалист по кадрам'),
    array('number' => '31', 'event' => 'Проведение проверки уровня знания кандидатами вопросов антикоррупционного законодательства, организации борьбы с коррупцией в ходе собеседования кандидатов на должности руководителей структурных подразделений', 'term' => 'При приеме на работу', 'executors' => 'Администрация Центра, специалист по кадрам'),
    array('number' => '32', 'event' => 'Осуществление проверки знаний работниками вопросов законодательства о борьбе с коррупцией в ходе проведения аттестации на соответствие занимаемой должности', 'term' => 'В соответствии с графиком проведения аттестации', 'executors' => 'Аттестационная комиссия, юрисконсульт'),
    array('number' => '33', 'event' => 'При рассмотрении кадровых вопросов не допускать назначений, которые в дальнейшем могут повлечь за собой возникновение конфликта интересов, ангажировать принятие управленческих решений, которые могут нанести вред имиджу и деловой репутации Центра, вызвать негативный резонанс в трудовом коллективе. Исключение семейственности в отношении государственных должностных лиц, подчиненных или подконтрольных друг другу', 'term' => 'Постоянно при приеме на работу', 'executors' => 'Специалист по кадрам'),
    array('number' => '34', 'event' => 'При формировании резерва руководящих кадров в обязательном порядке учитывать положение дел на участке работы руководителя или специалиста, связанных с обеспечением антикоррупционного законодательства, наличие фактов допущения нарушений, относящихся к категории коррупционных или создающих условия для коррупции', 'term' => 'До 01.04.2026', 'executors' => 'Конкурсная комиссия по формированию резерва кадров'),
);

$antiCorruptionDocuments = array(
    array(
        'title' => 'Закон Республики Беларусь от 15 июля 2015 г. № 305-З «О борьбе с коррупцией»',
        'href' => $antiCorruptionLawUrl,
        'meta' => 'Национальный правовой Интернет-портал Республики Беларусь',
        'external' => true,
    ),
    array(
        'title' => 'Памятка об основных требованиях антикоррупционного законодательства Республики Беларусь',
        'href' => '/documents/anti-corruption-requirements-memo.pdf',
        'meta' => 'PDF' . (formatAntiCorruptionFileSize(__DIR__ . '/documents/anti-corruption-requirements-memo.pdf') !== '' ? ', ' . formatAntiCorruptionFileSize(__DIR__ . '/documents/anti-corruption-requirements-memo.pdf') : ''),
        'external' => false,
    ),
    array(
        'title' => 'Положение о комиссии по противодействию коррупции',
        'href' => '/documents/anti-corruption-commission-regulation.docx',
        'meta' => 'DOCX' . (formatAntiCorruptionFileSize(__DIR__ . '/documents/anti-corruption-commission-regulation.docx') !== '' ? ', ' . formatAntiCorruptionFileSize(__DIR__ . '/documents/anti-corruption-commission-regulation.docx') : ''),
        'external' => false,
    ),
    array(
        'title' => 'Карта коррупционных рисков',
        'href' => '/documents/anti-corruption-risk-map.docx',
        'meta' => 'DOCX' . (formatAntiCorruptionFileSize(__DIR__ . '/documents/anti-corruption-risk-map.docx') !== '' ? ', ' . formatAntiCorruptionFileSize(__DIR__ . '/documents/anti-corruption-risk-map.docx') : ''),
        'external' => false,
    ),
);

$seoTitleMeta = $antiCorruptionPageTitle . ' - ' . $antiCorruptionSiteName;
$seoDescriptionMeta = 'Информация об антикоррупционной деятельности ТЦСОН Железнодорожного района г. Гомеля, состав комиссии и план мероприятий на 2026 год.';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <link rel="icon" href="/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="/css/cssbootstrap.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/cssbootstrap.min.css') ?>">
    <link rel="stylesheet" href="/css/smartphoto.min.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/smartphoto.min.css') ?>">
    <link rel="stylesheet" href="/css/style.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/style.css') ?>">
    <script src="https://lidrekon.ru/slep/js/jquery.js"></script>
    <script src="https://lidrekon.ru/slep/js/uhpv-full.min.js"></script>
    <link rel="stylesheet" href="/css/normalize.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/normalize.css') ?>">
    <link rel="stylesheet" href="/css/media.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media.css') ?>">
    <link rel="stylesheet" href="/css/media_mobile.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/css/media_mobile.css') ?>">
    <title><?php echo e($antiCorruptionPageTitle); ?> - <?php echo e($antiCorruptionSiteName); ?></title>
    <?php
    $seoScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $seoHost = $_SERVER['HTTP_HOST'] ?? 'tcsonrw-gomel.by';
    $seoRequestUri = $_SERVER['REQUEST_URI'] ?? '/anti-corruption.php';
    $seoPath = strtok($seoRequestUri, '?');
    $seoCanonical = $seoScheme . '://' . $seoHost . $seoPath;
    $seoOgImage = '/img/logo-old-mini.webp';
    $seoRobotsMeta = 'index,follow';
    $seoOgImageUrl = $seoScheme . '://' . $seoHost . $seoOgImage;
    ?>
    <meta name="description" content="<?php echo e($seoDescriptionMeta); ?>">
    <meta name="robots" content="<?php echo e($seoRobotsMeta); ?>">
    <link rel="canonical" href="<?php echo e($seoCanonical); ?>">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo e($seoTitleMeta); ?>">
    <meta property="og:description" content="<?php echo e($seoDescriptionMeta); ?>">
    <meta property="og:url" content="<?php echo e($seoCanonical); ?>">
    <meta property="og:image" content="<?php echo e($seoOgImageUrl); ?>">
    <style>
        .anti-corruption-page {
            scroll-padding-top: calc(var(--anti-corruption-page-header-offset, var(--header-height)) + 54px);
        }

        .anti-corruption-page-main {
            position: relative;
            isolation: isolate;
            padding-top: calc(var(--anti-corruption-page-header-offset, var(--header-height)) + 54px);
            padding-bottom: 74px;
        }

        .anti-corruption-page-main::before,
        .anti-corruption-page-main::after {
            content: "";
            position: absolute;
            top: 0;
            bottom: -86px;
            width: clamp(0px, calc((100vw - var(--width-container)) / 2), 118px);
            background: url("/img/loop-vert.png") repeat-y center top / 100% auto;
            pointer-events: none;
            z-index: 0;
        }

        .anti-corruption-page-main::before {
            left: 0;
            -webkit-mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(270deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .anti-corruption-page-main::after {
            right: 0;
            -webkit-mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
            mask-image: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.3) 42%, rgba(0, 0, 0, 0.76) 100%);
        }

        .anti-corruption-layout {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 315px minmax(0, 1fr);
            gap: 36px;
            align-items: start;
        }

        .anti-corruption-content {
            min-width: 0;
        }

        .anti-corruption-breadcrumbs {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 18px;
            color: #6a766f;
            font-size: 14px;
            line-height: 1.35;
        }

        .anti-corruption-breadcrumbs__home {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            background-color: #20604a;
            -webkit-mask: url("/img/dom.svg") no-repeat center / contain;
            mask: url("/img/dom.svg") no-repeat center / contain;
        }

        .anti-corruption-breadcrumbs__separator {
            color: #aab0ac;
            font-weight: 700;
        }

        .anti-corruption-breadcrumbs a {
            color: #20604a;
            text-decoration: none;
        }

        .anti-corruption-breadcrumbs a:hover,
        .anti-corruption-breadcrumbs a:focus-visible {
            color: #c62b30;
        }

        .anti-corruption-title {
            position: relative;
            margin: 0 0 18px;
            padding-left: 20px;
            color: #15553d;
            font-size: clamp(30px, 3vw, 40px);
            font-weight: 700;
            line-height: 1.12;
        }

        .anti-corruption-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 3px;
            bottom: 5px;
            width: 5px;
            border-radius: 999px;
            background: #d53331;
        }

        .anti-corruption-section {
            margin-top: 30px;
        }

        .anti-corruption-section__title {
            margin: 0 0 16px;
            color: #196847;
            font-size: 26px;
            font-weight: 700;
            line-height: 1.16;
        }

        .anti-corruption-card {
            padding: 24px;
            border: 1px solid rgba(32, 96, 74, 0.1);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 14px 30px rgba(48, 56, 52, 0.08);
        }

        .anti-corruption-text {
            margin: 0;
            color: #2f3a35;
            font-size: 17px;
            line-height: 1.56;
        }

        .anti-corruption-text + .anti-corruption-text {
            margin-top: 12px;
        }

        .anti-corruption-commission {
            display: grid;
            gap: 14px;
            margin: 0;
        }

        .anti-corruption-commission__item {
            display: grid;
            grid-template-columns: minmax(190px, 0.35fr) minmax(0, 1fr);
            gap: 18px;
            padding: 18px;
            border: 1px solid rgba(32, 96, 74, 0.1);
            border-radius: 14px;
            background: #f8fbf8;
        }

        .anti-corruption-commission__role {
            margin: 0;
            color: #15553d;
            font-size: 17px;
            font-weight: 800;
            line-height: 1.25;
        }

        .anti-corruption-commission__people {
            display: grid;
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .anti-corruption-commission dd {
            margin: 0;
        }

        .anti-corruption-commission__people li {
            position: relative;
            padding-left: 18px;
            color: #2f3a35;
            font-size: 16px;
            line-height: 1.45;
        }

        .anti-corruption-commission__people li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0.62em;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #d53331;
        }

        .anti-corruption-table-wrap {
            overflow-x: auto;
            border: 1px solid rgba(32, 96, 74, 0.1);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 14px 30px rgba(48, 56, 52, 0.08);
        }

        .anti-corruption-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }

        .anti-corruption-table th,
        .anti-corruption-table td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(32, 96, 74, 0.1);
            color: #2f3a35;
            font-size: 15px;
            line-height: 1.45;
            vertical-align: top;
        }

        .anti-corruption-table th {
            background: #eef7f1;
            color: #15553d;
            font-size: 14px;
            font-weight: 800;
            text-align: left;
        }

        .anti-corruption-table tr:last-child td {
            border-bottom: 0;
        }

        .anti-corruption-table__number {
            width: 64px;
            color: #15553d;
            font-weight: 800;
            text-align: center;
        }

        .anti-corruption-documents {
            display: grid;
            gap: 12px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .anti-corruption-documents__link {
            display: block;
            padding: 16px 18px;
            border: 1px solid rgba(32, 96, 74, 0.12);
            border-radius: 12px;
            background: #f8fbf8;
            color: #15553d;
            text-decoration: none;
            transition: border-color .2s ease, color .2s ease, transform .2s ease;
        }

        .anti-corruption-documents__link:hover,
        .anti-corruption-documents__link:focus-visible {
            border-color: rgba(213, 51, 49, 0.28);
            color: #c62b30;
            transform: translateY(-1px);
        }

        .anti-corruption-documents__title {
            display: block;
            color: inherit;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.35;
        }

        .anti-corruption-documents__meta {
            display: block;
            margin-top: 5px;
            color: #5f6b63;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.3;
        }

        @media (max-width: 1100px) {
            .anti-corruption-layout {
                grid-template-columns: 260px minmax(0, 1fr);
                gap: 24px;
            }
        }

        @media (max-width: 860px) {
            .anti-corruption-page {
                scroll-padding-top: calc(var(--anti-corruption-page-header-offset, var(--header-height)) + 38px);
            }

            .anti-corruption-page-main {
                padding-top: calc(var(--anti-corruption-page-header-offset, var(--header-height)) + 38px);
                padding-bottom: 58px;
            }

            .anti-corruption-page-main::before,
            .anti-corruption-page-main::after {
                display: none;
            }

            .anti-corruption-layout,
            .anti-corruption-commission__item {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .anti-corruption-title {
                font-size: 30px;
            }

            .anti-corruption-card {
                padding: 18px 16px;
            }

            .anti-corruption-table th,
            .anti-corruption-table td {
                padding: 12px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body class="anti-corruption-page">
<?php include __DIR__ . '/header.php'; ?>
<main class="main anti-corruption-page-main">
    <div class="anti-corruption-layout container">
        <?php
        $supportMenuActive = 'anti-corruption';
        include __DIR__ . '/support-side-menu.php';
        ?>

        <div class="anti-corruption-content">
            <nav class="anti-corruption-breadcrumbs" aria-label="Хлебные крошки">
                <span class="anti-corruption-breadcrumbs__home" aria-hidden="true"></span>
                <a href="/">Главная</a>
                <span class="anti-corruption-breadcrumbs__separator" aria-hidden="true">›</span>
                <span>Социальная поддержка</span>
                <span class="anti-corruption-breadcrumbs__separator" aria-hidden="true">›</span>
                <span><?php echo e($antiCorruptionPageTitle); ?></span>
            </nav>

            <section aria-labelledby="anti-corruption-page-title">
                <h1 class="anti-corruption-title" id="anti-corruption-page-title"><?php echo e($antiCorruptionPageTitle); ?></h1>
                <article class="anti-corruption-card">
                    <?php foreach ($antiCorruptionIntro as $antiCorruptionIntroParagraph): ?>
                        <p class="anti-corruption-text"><?php echo e($antiCorruptionIntroParagraph); ?></p>
                    <?php endforeach; ?>
                </article>
            </section>

            <section class="anti-corruption-section" aria-labelledby="anti-corruption-commission-title">
                <h2 class="anti-corruption-section__title" id="anti-corruption-commission-title">Состав комиссии по противодействию коррупции государственного учреждения «Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля»</h2>
                <article class="anti-corruption-card">
                    <dl class="anti-corruption-commission">
                        <?php foreach ($antiCorruptionCommission as $commissionGroup): ?>
                            <div class="anti-corruption-commission__item">
                                <dt class="anti-corruption-commission__role"><?php echo e($commissionGroup['role']); ?></dt>
                                <dd>
                                    <ul class="anti-corruption-commission__people">
                                        <?php foreach ($commissionGroup['people'] as $commissionPerson): ?>
                                            <li><?php echo e($commissionPerson); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                </article>
            </section>

            <section class="anti-corruption-section" aria-labelledby="anti-corruption-plan-title">
                <h2 class="anti-corruption-section__title" id="anti-corruption-plan-title">План мероприятий, направленных на устранение причин и условий, способствующих коррупционным проявлениям в учреждении, на 2026 год</h2>
                <div class="anti-corruption-table-wrap">
                    <table class="anti-corruption-table">
                        <thead>
                            <tr>
                                <th scope="col">№</th>
                                <th scope="col">Наименование мероприятий</th>
                                <th scope="col">Срок исполнения</th>
                                <th scope="col">Исполнители</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($antiCorruptionPlanRows as $antiCorruptionPlanRow): ?>
                                <tr>
                                    <td class="anti-corruption-table__number"><?php echo e($antiCorruptionPlanRow['number']); ?></td>
                                    <td><?php echo e($antiCorruptionPlanRow['event']); ?></td>
                                    <td><?php echo e($antiCorruptionPlanRow['term']); ?></td>
                                    <td><?php echo e($antiCorruptionPlanRow['executors']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="anti-corruption-section" aria-labelledby="anti-corruption-documents-title">
                <h2 class="anti-corruption-section__title" id="anti-corruption-documents-title">Ссылки и документы</h2>
                <article class="anti-corruption-card">
                    <ul class="anti-corruption-documents">
                        <?php foreach ($antiCorruptionDocuments as $antiCorruptionDocument): ?>
                            <li>
                                <a
                                    class="anti-corruption-documents__link"
                                    href="<?php echo e($antiCorruptionDocument['href']); ?>"
                                    <?php echo $antiCorruptionDocument['external'] ? 'target="_blank" rel="noopener noreferrer"' : 'download'; ?>
                                >
                                    <span class="anti-corruption-documents__title"><?php echo e($antiCorruptionDocument['title']); ?></span>
                                    <span class="anti-corruption-documents__meta"><?php echo e($antiCorruptionDocument['meta']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            </section>
        </div>
    </div>
</main>
<?php include __DIR__ . '/footer.php'; ?>
<script>
    (function () {
        var root = document.documentElement;
        var header = document.querySelector('.header-down');

        if (!root || !header) {
            return;
        }

        function syncAntiCorruptionPageOffset() {
            root.style.setProperty('--anti-corruption-page-header-offset', header.offsetHeight + 'px');
        }

        syncAntiCorruptionPageOffset();
        window.addEventListener('load', syncAntiCorruptionPageOffset);
        window.addEventListener('resize', syncAntiCorruptionPageOffset);
    })();
</script>
</body>

</html>

<?php

function getDepartmentDetailContentHtml($departmentId)
{
    $contents = array(
        'primary-intake' => <<<'HTML_PRIMARY_INTAKE'
<p>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><b><span style="color: #0000ff; font-size: 14pt;">Отделение первичного приема, информации, анализа и прогнозирования,</span></b> г. Гомель, ул. 50 лет БССР, д. 19, кабинет № 3, телефон <a href="tel:+375232349899">8 (0232) 34-98-99</a>:</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><br>
 <b>заведующий отделением:</b> Волчкова Виктория Станиславовна</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><b>специалисты по социальной работе:</b> Бумажкова Светлана Александровна, Кушнерова Елена Николаевна, Сивакова Алеся Александровна, Сивакова Анастасия Александровна, Старикова Екатерина Николаевна, Оснач Кристина Юрьевна, телефон <a href="tel:+375232349956">8 (0232) 34-99-56</a>, <a href="tel:+375232349899">8 (0232) 34-98-99</a>, <a href="tel:+375256604209">8 (025) 660-42-09</a></span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><br>
 <b><span style="color: #0000ff; font-size: 14pt;">Режим работы:</span></b></span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><br>
 с 8-30 до 13.00, с 14.00 до 17.30</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><br>
 с 13.00 до 14.00 – обеденный перерыв</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;">
 выходные дни: суббота, воскресенье</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><br>
 <b><span style="color: #0000ff; font-size: 14pt;">Основные задачи отделения:</span></b></span>
</p>
<p style="text-align: justify;">
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;">организация первичного приема, консультирование граждан (семей) об оказании социальных услуг либо социальной поддержки, определение индивидуальной нуждаемости граждан (семей) в социальных услугах;</span>
</p>
<p style="text-align: justify;">
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;">организация и проведение обследований материально-бытового положения социально-уязвимой категории граждан, проживающих на территории Железнодорожного района г. Гомеля.</span>
</p>
<p>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><b><span style="color: #0000ff; font-size: 14pt;">Направления деятельности отделения:</span></b></span>
</p>
<ul style="text-align: justify; color: #000000; font-family: 'Times New Roman', Times; font-size: 13pt;">
 <li>первичный прием, информирование граждан об услугах, оказываемых ТЦСОН Железнодорожного района г. Гомеля;</li>
 <li>проведение обследований материально-бытового положения социально уязвимых категорий граждан с целью выявления нуждаемости в различных видах социальных услуг;</li>
 <li>мониторинг подтверждения факта ухода за инвалидом 1 группы, либо лицом, достигшим 80 лет, по месту его жительства (месту пребывания);</li>
 <li>осуществление подготовки предложений в рамках разработки социальных проектов, комплексов мероприятий, направленных на повышение эффективности социального обслуживания на индивидуальном, групповом (региональном, республиканском) уровнях;</li>
 <li>актуализация созданных банков данных социально-уязвимых категорий граждан, проживающих на территории района;</li>
 <li>использование потенциала средств массовой информации, социальных сетей для привлечения внимания общества к вопросам оказания социальных услуг и социальной поддержки;</li>
 <li>издание памяток, бюллетеней и других информационных материалов по вопросам социального обслуживания населения;</li>
 <li>взаимодействие в вопросах социального обслуживания с государственными органами, организациями, негосударственными организациями, в том числе религиозными;</li>
 <li>организация работы телефона «Горячая линия».</li>
 </ul>
HTML_PRIMARY_INTAKE
,
        'social-support' => <<<'HTML_SOCIAL_SUPPORT'
<p>
 <strong>Отделение социальной поддержки населения,</strong> г. Гомель, ул. 50 лет БССР, д. 19, кабинет № 4
</p>
<p>
 <strong>заведующий отделением:</strong> Коржова Елена Викторовна, телефон <a href="tel:+375232349795">8 (0232) 34-97-95</a>
</p>
<p>
 <strong>специалисты по социальной работе:</strong> Старикова Елена Владимировна, Рыженкова Жанна Михайловна, Клименко Екатерина Александровна, Лапейко Екатерина Александровна, Михеева Наталья Ивановна
</p>
<p>
 <strong>Режим работы:</strong>
</p>
<p>
 с 8-30 до 13.00, с 14.00 до 17.30<br>
 с 13.00 до 14.00 – обеденный перерыв<br>
 выходные дни: суббота, воскресенье
</p>
<p>
 <strong>Режим работы по административным процедурам:</strong>
</p>
<p>
 Понедельник с 8-00 до 13.00<br>
 Вторник с 14.00 до 20.00<br>
 Среда с 8-00 до 13.00<br>
 Четверг с 8-00 до 13.00<br>
 Пятница с 8-00 до 13.00<br>
 выходные дни: суббота, воскресенье
</p>
<p>
 <strong>Основной задачей отделения является</strong> оказание помощи гражданам, нуждающимся в материальной поддержке, направленной на обеспечение нормальной жизнедеятельности и улучшение качества жизни отдельных социально незащищенных категорий граждан.
</p>
<p>
 <strong>Направления деятельности отделения:</strong>
</p>
<ul>
	<li>обработка документов для назначения государственной адресной социальной помощи нуждающимся гражданам в виде ежемесячного и (или) единовременного социальных пособий, социального пособия для возмещения затрат на приобретение подгузников, обеспечения продуктами питания детей первых двух лет жизни;</li>
	<li>прием заявлений для оказания материальной помощи из средств Фонда социальной защиты населения Министерства труда и социальной защиты Республики Беларусь, проведение обследования материально-бытового положения заявителя по месту жительства с составлением акта обследования;</li>
	<li>формирование пакета документов, необходимых для принятия решения о назначении и досрочном распоряжении средствами семейного капитала, ведение необходимой отчетности;</li>
	<li>осуществление мероприятий по проведению ремонтных работ в жилых помещениях, принадлежащих участникам и инвалидам Великой Отечественной войны, инвалидам боевых действий на территории других государств, а также приведению печного, газового оборудования, электропроводки в соответствие с установленными требованиями, включая их ремонт и (или) замену, установку (замену) автономных пожарных извещателей, элементов питания к ним, автономных пожарных извещателей с выводом на сигнально-звуковое устройство;</li>
	<li>информирование населения об услугах, оказываемых ТЦСОН Железнодорожного района г. Гомеля, в том числе через средства массовой информации.</li>
</ul>
HTML_SOCIAL_SUPPORT
,
        'guardianship' => <<<'HTML_GUARDIANSHIP'
<p>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><b><span style="color: #0000ff; font-size: 14pt;">Отделение опеки и попечительства,</span></b> г. Гомель, ул. Юбилейная, д. 8, к. 2, кабинет № 10, телефон <a href="tel:+375232550036">8 (0232) 55-00-36</a>.</span>
</p>
<p>
 <strong>заведующий отделением:</strong> Коржова Карина Валерьевна<br>
 <strong>юрисконсульт отделения:</strong><br>
 Дорофеева Виталия Владимировна, кабинет № 10, телефон <a href="tel:+375232550036">8 (0232) 55-00-36</a>, <a href="tel:+375256604250">8 (025) 660-42-50 (Life)</a><br>
 <strong>специалисты по социальной работе:</strong><br>
 Безгинова Анастасия Анатольевна, кабинет № 10, телефон <a href="tel:+375232550036">8 (0232) 55-00-36</a>, <a href="tel:+375256604250">8 (025) 660-42-50 (Life)</a>
</p>
<p>
</p>
<p>
 <span style="color: #000000;"><b> <strong>Режим работы:</strong> </b></span>
</p>
<p>
	 с 8-30 до 17.30<br>
	 с 13.00 до 14.00 – обеденный перерыв<br>
	 выходные дни: суббота, воскресенье
</p>
<p>
</p>
<p>
 <strong>Основные задачи отделения:</strong>
</p>
<ul>
	<li>защита личных и имущественных прав и законных интересов совершеннолетних лиц, признанных судом недееспособными, ограниченных судом в дееспособности;</li>
	<li>поиск кандидатов в опекуны и попечители, консультирование по требованиям к ним, их правам, обязанностям и ответственности;</li>
	<li>информирование о порядке сбора документов, подачи заявлений и представления ежегодной отчетности по управлению имуществом подопечных;</li>
	<li>содействие в оформлении документов для поселения в социальные пансионаты, в том числе детские, дома сопровождаемого проживания;</li>
</ul>
HTML_GUARDIANSHIP
,
        'home-care' => <<<'HTML_HOME_CARE'
<p>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><b><span style="color: #0000ff; font-size: 14pt;">Отделение социальной помощи на дому,</span></b> г. Гомель, ул. 50 лет БССР, д. 19, кабинет № 5, телефон <a href="tel:+375232349896">8 (0232) 34-98-96</a>, <a href="tel:+375232349897">8 (0232) 34-98-97</a>, <a href="tel:+375256604243">8 (025) 660-42-43</a>:</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><br>
 <b>заведующий отделением:</b> Светюха Наталья Михайловна</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><b>специалисты по социальной работе:</b> Зернова Елена Михайловна, Зинченко Надежда Ивановна, Максимович Людмила Дмитриевна, Шаповалова Антонина Михайловна</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><br>
 <b><span style="color: #0000ff; font-size: 14pt;">Режим работы:</span></b></span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><br>
 с 8.30 до 13.00, с 14.00 до 17.30</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><br>
 с 13.00 до 14.00 – обеденный перерыв</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;">
 выходные дни: суббота, воскресенье</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><br>
 <span style="font-size:14.0pt; font-family:&quot;Times New Roman&quot;,serif;mso-ansi-language:RU"><span style="color: #0000ff;"><b>Основными целями работы</b></span> отделения являются разработка и внедрение мер, направленных на продление жизненной активности граждан пожилого возраста и инвалидов, частично утративших способность к самообслуживанию и нуждающихся в посторонней помощи в привычной для них обстановке:</span>
</p>
<p class="MsoNormal" style="margin-right:-11.7pt;text-align:justify;text-indent: 35.45pt">
 <span style="font-size:14.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,serif; mso-ansi-language:RU">- выявление и учет одиноких и одиноко проживающих пенсионеров и инвалидов, нуждающихся в обслуживании на дому;</span>
</p>
<p class="MsoNormal" style="margin-right:-11.7pt;text-align:justify;text-indent: 35.45pt">
 <span style="font-size:14.0pt;line-height:107%;font-family:&quot;Times New Roman&quot;,serif; mso-ansi-language:RU">- определение конкретных форм срочной социальной помощи гражданам, исходя из состояния их здоровья, возможности к самообслуживанию и конкретной жизненной ситуации.</span>
</p>
<p class="MsoNormal" style="margin-right:-11.7pt;mso-margin-bottom-alt:auto; text-align:justify;text-indent:35.45pt;line-height:normal;mso-outline-level: 1;background:white">
 <span style="font-size:14.0pt;font-family:&quot;Times New Roman&quot;,serif; mso-ansi-language:RU">Обслуживание на дому граждан пожилого возраста и инвалидов, имеющих функциональные классы 2, 3, 4, осуществляется социальными работниками, нянями и помощниками по уходу отделения социальной помощи на дому путем предоставления им, в зависимости от степени и характера нуждаемости, социально-бытовых, консультативных и иных услуг, из комплекса услуг, входящих в Перечень социальных услуг, оказываемых государственными учреждениями социального обслуживания, с нормами и нормативами обеспеченности граждан этими услугами. </span>
</p>
<p class="MsoNormal" style="mso-margin-bottom-alt:auto;text-align: center;line-height:normal;mso-outline-level:1;background:white" align="center">
 <span style="font-size:14.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-ansi-language: RU">(<i>ПОСТАНОВЛЕНИЕ СОВЕТА МИНИСТРОВ РЕСПУБЛИКИ БЕЛАРУСЬ от 27 декабря 2012 г. N 1218 «О некоторых вопросах оказания социальных услуг» (в действующей редакции)</i></span>
</p>
<p class="MsoNormal" style="mso-margin-bottom-alt:auto;text-align:justify; text-indent:42.55pt;line-height:normal;mso-outline-level:1;background:white">
 <span style="font-size:14.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-ansi-language: RU">Тариф на социальные услуги, предоставляемые центрами, входящие в перечень социальных услуг государственных учреждений социального обслуживания с нормами и нормативами обеспеченности граждан этими услугами, утвержденным постановлением Совета Министров Республики Беларусь от 27 декабря 2012 года № 1218 «О некоторых вопросах оказания социальных услуг» (в редакции постановления Совета Министров Республики Беларусь 02.03.2015 № 150), составляет: 0,80 белорусских рублей за один час работы (за исключением услуг сиделки и дневного присмотра); в размере 1,24 белорусского рубля за час на услуги сиделки, услуги дневного присмотра в форме социального обслуживания на дому. Тариф установлен решением Гомельского облисполкома от 20.11.2023 № 919.</span>
</p>
HTML_HOME_CARE
,
        'crisis-support' => <<<'HTML_CRISIS_SUPPORT'
<p><strong>Отделение комплексной поддержки в кризисной ситуации</strong></p>
<p><strong>Заведующий отделением:</strong> Дайнеко Ирина Сергеевна</p>
<p><strong>Специалисты отделения:</strong></p>
<ul>
    <li>психолог Кучик Алина Ивановна, кабинет № 8, телефон <a href="tel:+375232349792">8 (0232) 34-97-92</a>;</li>
    <li>психолог Заостровская Екатерина Игоревна, кабинет № 8, телефон <a href="tel:+375232295938">8 (0232) 29-59-38</a>;</li>
    <li>специалист по социальной работе Мельникова Алиса Владимировна, кабинет № 8, телефон <a href="tel:+375232295938">8 (0232) 29-59-38</a>;</li>
    <li>специалист по социальной работе Тимошенко Светлана Михайловна, кабинет № 8, телефон <a href="tel:+375232295938">8 (0232) 29-59-38</a>;</li>
    <li>специалист по социальной работе Демидова Ольга Николаевна, кабинет № 8, телефон <a href="tel:+375232288600">8 (0232) 28-86-00</a>.</li>
</ul>
<p><strong>Режим работы:</strong></p>
<ul>
    <li>понедельник, среда, четверг, пятница: с 8:00 до 13:00, с 14:00 до 17:30;</li>
    <li>вторник: с 8:00 до 13:00, с 14:00 до 20:00;</li>
    <li>обеденный перерыв: с 13:00 до 14:00;</li>
    <li>выходные дни: суббота, воскресенье.</li>
</ul>
<p><strong>Цель деятельности отделения:</strong></p>
<p>Комплексная помощь гражданам и семьям, находящимся в трудной жизненной ситуации, содействие в социальной адаптации, восстановлении способности к самостоятельной жизнедеятельности и преодолении кризисных обстоятельств.</p>
<p><strong>Основные направления работы:</strong></p>
<ul>
    <li>выявление, обследование материально-бытового положения и учет граждан и семей, находящихся в трудной жизненной ситуации, включая многодетные семьи и семьи, воспитывающие детей-инвалидов;</li>
    <li>составление индивидуальных планов патронатного сопровождения граждан и семей по выходу из трудной жизненной ситуации;</li>
    <li>проведение психологических консультаций и оказание экстренной психологической помощи по телефону «Доверие»;</li>
    <li>профилактика домашнего насилия, информирование о доступных видах помощи, включая услугу временного приюта;</li>
    <li>выявление неблагоприятной для детей обстановки и организация работы с семьями, направленными советами профилактики учреждений образования;</li>
    <li>оказание социальных услуг семьям, дети которых признаны находящимися в социально опасном положении либо нуждающимися в государственной защите;</li>
    <li>сопровождение лиц из числа детей-сирот и детей, оставшихся без попечения родителей;</li>
    <li>оказание социальной помощи гражданам, страдающим зависимостью от психоактивных веществ, а также освобожденным из учреждений уголовно-исполнительной системы;</li>
    <li>обеспечение нуждающихся спонсорской, благотворительной и иностранной безвозмездной помощью в натуральной форме;</li>
    <li>осуществление административной процедуры 3.15 по выдаче удостоверения многодетной семьи;</li>
    <li>выявление граждан из группы суицидального риска и социально-психологическая работа по адаптации и реабилитации семей через клубную деятельность.</li>
</ul>
<p><strong>Клубы и проекты на базе отделения:</strong></p>
<ul>
    <li>проект «PRO-семью» и клуб «Содружество» для лиц из числа детей-сирот и детей, оставшихся без попечения родителей;</li>
    <li>клуб «Семья от А до Я» для многодетных семей;</li>
    <li>клуб «Сердце Ангела» для семей, чьи дети признаны находящимися в социально опасном положении;</li>
    <li>проект «Изменим жизнь вместе» для граждан, чьи дети признаны нуждающимися в государственной защите.</li>
</ul>
HTML_CRISIS_SUPPORT
,
        'elderly-day-care' => <<<'HTML_ELDERLY_DAY_CARE'
<p>
</p>
<p>
</p>
<p>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><b><span style="color: #0000ff; font-size: 14pt;">Отделение дневного пребывания для граждан пожилого возраста:</span></b></span><br>
 <span style="font-size: 18.6667px;"><b><br>
 </b></span><span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> <b>заведующий отделением:</b> Кухарева Людмила Сергеевна</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> <u>г.п.Яновичи, ул.Унишевского, д.36, </u>телефон <a href="tel:+375232690286">8 (0232) 69-02-86</a>:</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> <b>руководитель кружка:</b> Барченко Светлана Ивановна</span>
</p>
<p>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> <u>аг.Зароново, ул.Приозёрная, д.2</u>, телефон <a href="tel:+375232697350">8 (0232) 697-350</a>:</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> <b>специалист по социальной работе: </b>Зимина Анна Григорьевна</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> <b>руководитель кружка:</b> Сивенко Ирина Петровна</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> </span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> <u>аг.Шапечино, ул.Молодёжная, д.2Г</u>, телефон <a href="tel:+375232695248">8 (0232) 695-248</a></span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> <b>специалист по социальной работе: </b>Постоялкина Ирина Валерьевна </span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> <b>руководитель кружка: </b>Гурская Ольга Викторовна</span><span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> </span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> </span><span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> </span><span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><br>
 </span>
</p>
<p>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><br>
 </span>
</p>
<p>
</p>
<p>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><b><span style="color: #0000ff; font-size: 14pt;">Режим раб</span></b></span><span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><b><span style="color: #0000ff; font-size: 14pt;">оты:</span></b></span>
</p>
<p>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;">с 8-00 до 13.00, с 14.</span><span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;">00 до 17.00</span>
</p>
<p>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;">с 13.00 до 14.00 – обеденный перерыв</span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;">
	выходные дни: суббота, воскресенье</span><br>
</p>
<p>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> </span><span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> </span><br>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> </span><span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> </span><span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"> <b><span style="color: #0000ff; font-size: 14pt;">Цель деятельности отделения:</span></b></span>
</p>
<p>
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;">оказание гражданам пожилого возраста, сохранившим (полностью или частично) способность к самообслуживанию и передвижению, комплекса социальных услуг в условиях постоянного или временного дневного пребывания в отделении ТЦСОН.</span>
</p>
<p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto; text-align:justify;line-height:normal">
 <b><span style="font-size:13.0pt; font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;; color:blue;mso-fareast-language:RU">Направления деятельности отделения:</span></b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family: &quot;Times New Roman&quot;;mso-fareast-language:RU"> </span>
</p>
<p class="MsoNoSpacing" style="text-align:justify">
 <b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif">.</span></b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif"> обеспечение дневного присмотра за нуждающимися пожилыми гражданами;</span>
</p>
<p class="MsoNoSpacing" style="text-align:justify">
 <b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif">.</span></b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif"> поддержание и развитие трудовых навыков и способностей в кружках и клубах по интересам;</span>
</p>
<p class="MsoNoSpacing" style="text-align:justify">
 <b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif">.</span></b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif"> создание и обеспечение работой групп самопомощи и взаимопомощи;</span>
</p>
<p class="MsoNoSpacing" style="text-align:justify">
 <b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif">.</span></b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif"> приобщение к активной жизни в обществе;</span>
</p>
<p class="MsoNoSpacing" style="text-align:justify">
 <b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif">.</span></b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif"> облегчение установления дружеских отношений, созданных на равных правах и возможностях пожилых граждан;</span>
</p>
<p class="MsoNoSpacing" style="text-align:justify">
 <b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif">.</span></b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif"> организация досуга и создание условий, способствующих общению и поддержанию активного образа жизни пожилых граждан путем проведения культурно-массовых и спортивно-оздоровительных мероприятий, организации кружков и клубов по интересам, в том числе по месту жительства граждан;</span>
</p>
<p class="MsoNoSpacing" style="text-align:justify">
 <b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif">.</span></b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif"> организация участия в работах по благоустройству помещений ТЦСОН и прилегающей к нему территории;</span>
</p>
<p class="MsoNoSpacing" style="text-align:justify">
 <b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif">. </span></b><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif">оказание содействия в деятельности отделения и других структурных подразделений ТЦСОН.</span>
</p>
<p class="MsoNormal" style="mso-margin-top-alt:auto;margin-bottom:12.0pt; text-align:justify;line-height:normal">
 <span style="font-size:13.0pt; font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;; color:black;mso-fareast-language:RU">В отделение принимаются граждане, достигшие 60-летнего возраста и сохранившие способность к самообслуживанию, активному передвижению и не имеющие противопоказаний. </span>
</p>
<p class="MsoNoSpacing">
 <span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif; mso-fareast-language:RU">Зачисление в отделение производится на основании: <br>
	 - письменного заявления; <br>
	 - документа, удостоверяющего личность; <br>
	 - документа установленного образца о праве на льготы (удостоверение инвалида, ветерана Великой Отечественной войны, и т.п.) для граждан, относящихся к категории пользующихся льготами; </span>
</p>
<p class="MsoNoSpacing" style="text-align:justify">
 <span style="font-size:13.0pt; font-family:&quot;Times New Roman&quot;,serif;mso-fareast-language:RU">- сведений о наличии (отсутствии) ухода за гражданином, обратившимся за оказанием социальных услуг, лицом, получающим пособие по уходу за инвалидом </span><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif;mso-ansi-language: EN-US;mso-fareast-language:RU" lang="EN-US">I</span><span style="font-size:13.0pt; font-family:&quot;Times New Roman&quot;,serif;mso-fareast-language:RU"> группы либо лицом, достигшим 80-летнего возраста; - справки о месте жительства и составе семьи; </span>
</p>
<p class="MsoNoSpacing" style="text-align:justify">
 <span style="font-size:13.0pt; font-family:&quot;Times New Roman&quot;,serif;mso-fareast-language:RU">- выписки из медицинской карты о состоянии здоровья гражданина или заключения врачебно-консультационной комиссии государственной организации здравоохранения; - акта обследования материально-бытового положения. </span>
</p>
<p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto; text-align:justify;line-height:normal">
 <span style="font-size:13.0pt; font-family:&quot;Times New Roman&quot;,serif;mso-fareast-font-family:&quot;Times New Roman&quot;; color:black;mso-fareast-language:RU">Граждане при подаче заявления об оказании социальных услуг вправе сами представить документы, запрашиваемые ТЦСОН.</span><span style="font-size:13.0pt;font-family:&quot;Times New Roman&quot;,serif; mso-fareast-font-family:&quot;Times New Roman&quot;;mso-fareast-language:RU"> </span>
</p>
<p>
</p>
 <span style="font-family: &quot;Times New Roman&quot;, Times; font-size: 14pt;"> </span><span style="font-family: &quot;Times New Roman&quot;, Times;"><span style="font-size: 14pt; font-family: &quot;Times New Roman&quot;, Times;"> </span>
<ul style="font-size: 12pt;">
</ul>
 <span style="font-size: 14pt; font-family: &quot;Times New Roman&quot;, Times;"> </span>
<p style="font-size: 12pt;">
 <span style="font-family: &quot;Times New Roman&quot;, Times; font-size: 14pt;"> </span>
</p>
 <span style="font-size: 14pt; font-family: &quot;Times New Roman&quot;, Times;"> </span><span style="font-size: 14pt; font-family: &quot;Times New Roman&quot;, Times; color: #000000;"> </span><span style="font-size: 14pt; font-family: &quot;Times New Roman&quot;, Times;"> </span>
<p class="MsoNormal" style="font-size: 12pt;">
 <span style="font-family: &quot;Times New Roman&quot;, Times; font-size: 14pt;"> </span><span style="font-family: &quot;Times New Roman&quot;, Times; font-size: 14pt;"> </span>
</p>
 <span style="font-size: 14pt; font-family: &quot;Times New Roman&quot;, Times;"> </span>
<p style="font-size: 12pt;" align="center">
</p>
<p>
</p>
<p style="font-size: 12pt; text-align: center;">
 <span style="color: #000000; font-size: 13pt;"><b><span style="color: #0000ff; font-size: 14pt;">ГРАФИК&nbsp;</span></b></span>
</p>
<p style="font-size: 12pt; text-align: center;">
 <span style="color: #000000; font-size: 13pt;"><b><span style="color: #0000ff; font-size: 14pt;">&nbsp;работы кружков и клубов отделения дневного пребывания для граждан пожилого возраста </span></b></span><span style="color: #000000; font-size: 13pt;"><b><span style="color: #0000ff; font-size: 14pt;"> </span></b></span><br>
 <span style="color: #000000; font-size: 13pt;"><b><span style="color: #0000ff; font-size: 14pt;">
	отделения дневного пребывания для граждан пожилого возраста</span></b></span>
</p>
<table width="100%" cellspacing="0" cellpadding="0" border="1">
<tbody>
<tr>
<td style="text-align: center;" rowspan="2" width="47">
<p>№</p>
<p>п/п</p>
</td>
<td style="text-align: center;" rowspan="2" width="224">
<p>Наименование кружка</p>
</td>
<td style="text-align: center;" colspan="5" width="496">
<p>День недели/время работы</p>
</td>
<td style="text-align: center;" rowspan="2" width="177">
<p> </p>
<p>Ответственные</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="99">
<p>Понедельник</p>
</td>
<td style="text-align: center;" width="99">
<p>Вторник</p>
</td>
<td style="text-align: center;" width="99">
<p>Среда</p>
</td>
<td style="text-align: center;" width="99">
<p>Четверг</p>
</td>
<td style="text-align: center;" width="99">
<p>Пятница</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>1</p>
</td>
<td style="text-align: center;" width="224">
<p><strong>«Вдохновение» (</strong>реабилитация декоративно-прикладным творчеством)</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-12.00</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-12.00</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-12.00</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-12.00</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-12.00</p>
<p>на дому</p>
</td>
<td style="text-align: center;" width="177">
<p>Барченко С.И.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>2.</p>
</td>
<td style="text-align: center;" width="224">
<p><strong>«Скандинавская тропа» (</strong>реабилитация двигательностью)</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p>13.00-16.00</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p>13.00-16.00</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="177">
<p>Барченко С.И.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>3.</p>
</td>
<td style="text-align: center;" width="224">
<p><strong>«Журавинка</strong>» (вокалотерапия)</p>
</td>
<td style="text-align: center;" width="99">
<p>13.00-17.00</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99"> </td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p>13.00-17.00</p>
</td>
<td style="text-align: center;" width="177">
<p>Вицкоп А.Г.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>4.</p>
</td>
<td style="text-align: center;" width="224">
<p><strong>«Нам года не беда»</strong></p>
<p>(поддержание интеллектуальной активности, арт-терапия) (волонтерский)</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p>13.00-17.00</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p>13.00-17.00</p>
</td>
<td style="text-align: center;" width="177">
<p>Баранова Н.А.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>5.</p>
</td>
<td style="text-align: center;" width="224">
<p><strong>«Компьютерный мир»</strong></p>
<p>(социальная адаптация в современном информационном мире) (волонтерский)</p>
</td>
<td style="text-align: center;" width="99">
<p>14.00-17.00</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99"> </td>
<td style="text-align: center;" width="99">
<p>14.00-17.00</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="177">
<p>Холамова В.Г.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>6.</p>
</td>
<td style="text-align: center;" width="224">
<p>Социально-трудовая реабилитация <strong>Карвингом </strong>(волонтерский)</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p>13.00-17.00</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="177">
<p>Михайлова Е.К.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>7.</p>
</td>
<td style="text-align: center;" width="224">
<p>Клуб <strong>«Сад и огород»</strong></p>
</td>
<td style="text-align: center;" colspan="5" width="496">
<p>Каждая первая пятница месяца</p>
<p>14 00 – 17 00</p>
</td>
<td style="text-align: center;" width="177">
<p>Барченко С.И.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>8.</p>
</td>
<td style="text-align: center;" width="224">
<p>Клуб <strong>«Синема»</strong> (волонтерский)</p>
</td>
<td style="text-align: center;" colspan="5" width="496">
<p>Каждая последняя пятница месяца</p>
<p>14 00 – 17 00</p>
</td>
<td style="text-align: center;" width="177">
<p>Анисимова В.М.</p>
<p>Рыжикова В.И.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>9.</p>
</td>
<td style="text-align: center;" width="224">
<p>Клуб «Бабушкины секреты»</p>
<p>(волонтерский)</p>
</td>
<td style="text-align: center;" colspan="5" width="496">
<p>Каждая первая среда месяца</p>
<p>14 00 – 17 00</p>
</td>
<td style="text-align: center;" width="177">
<p>Плотникова В.И.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>10.</p>
</td>
<td style="text-align: center;" width="224">
<p>«Компьютерная</p>
<p>Грамотность»</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-12.45</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-9.45 на дому 10.45-12.45</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-12.45</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-12.45</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-9.45 на дому 10.45-12.45</p>
</td>
<td style="text-align: center;" width="177">
<p>Гурская О.В.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>11.</p>
</td>
<td style="text-align: center;" width="224">
<p>«Спорт и здоровье»</p>
<p>(реабилитация двигательной активностью)</p>
</td>
<td style="text-align: center;" width="99">
<p>8.00-13.00</p>
<p>14.00-16.45</p>
</td>
<td style="text-align: center;" width="99">
<p>8.00-9.00 на дому 15.00-16.45</p>
</td>
<td style="text-align: center;" width="99">
<p>8.00-9.00 на дому 15.00-16.45</p>
</td>
<td style="text-align: center;" width="99">
<p>8.00-9.00 на дому 15.00-16.45</p>
</td>
<td style="text-align: center;" width="99">
<p>8.00-9.00 15.00-16.45</p>
</td>
<td style="text-align: center;" width="177">
<p>Боброва И.Б.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>12.</p>
</td>
<td style="text-align: center;" width="224">
<p>«Мастерица» (реабилитация творчеством)</p>
<p>(волонтерский)</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-11.00</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="177">
<p>Данченко Р.Ф.</p>
<p>Гурская О.В.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>13.</p>
</td>
<td style="text-align: center;" width="224">
<p>«Пешком к полноценной жизни»</p>
<p>(реабилитация двигательной активностью)</p>
<p>(волонтерский)</p>
</td>
<td style="text-align: center;" width="99">
<p>16.00-17.00</p>
</td>
<td style="text-align: center;" width="99">
<p>16.00-17.00</p>
</td>
<td style="text-align: center;" width="99">
<p>16.00-17.00</p>
</td>
<td style="text-align: center;" width="99">
<p>16.00-17.00</p>
</td>
<td style="text-align: center;" width="99">
<p>16.00-17.00</p>
</td>
<td style="text-align: center;" width="177">
<p>Тарасенко Е.Л.</p>
<p>Боброва И.Б.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>14.</p>
</td>
<td style="text-align: center;" width="224">
<p>Женский клуб. Для души, дома, досуга «Селяночка»</p>
<p>(волонтерский)</p>
</td>
<td style="text-align: center;" colspan="5" width="496">
<p>Последняя пятница месяца</p>
</td>
<td style="text-align: center;" width="177">
<p>Свириденко Л.П.</p>
<p>Постоялкина И.В.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>15.</p>
</td>
<td style="text-align: center;" width="224">
<p>Выездной женский клуб. Для души, дома, досуга «Селяночка»</p>
</td>
<td style="text-align: center;" colspan="5" width="496">
<p>Последний понедельник месяца</p>
</td>
<td style="text-align: center;" width="177">
<p>Свириденко Л.П.</p>
<p>Постоялкина И.В.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>16.</p>
</td>
<td style="text-align: center;" width="224">
<p>«Умелые ручки»</p>
<p>(реабилитация творчеством)</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-13.00</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-13.00</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-13.00</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-13.00</p>
</td>
<td style="text-align: center;" width="99">
<p>9.00-13.00</p>
</td>
<td style="text-align: center;" width="177">
<p>Сивенко И.П.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>17.</p>
</td>
<td style="text-align: center;" width="224">
<p>«Музыкальная тропа здоровья» (реабилитация двигательной активностью)</p>
<p>(волонтерский)</p>
</td>
<td style="text-align: center;" width="99">
<p>15.00-17.00</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="177">
<p>Кривцун В.П.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>18</p>
</td>
<td style="text-align: center;" width="224">
<p>«Жить здорово!»</p>
<p>(взаимоотношения в семье, в обществе)</p>
<p>(волонтерский)</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p>10.00-12.00</p>
</td>
<td style="text-align: center;" width="177">
<p>Никитин С.П.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>19.</p>
</td>
<td style="text-align: center;" width="224">
<p>«Кружок компьютерной грамотности»</p>
<p>(волонтерский)</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p>16.00-17.00</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="177">
<p>Волонтеры</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>20.</p>
</td>
<td style="text-align: center;" width="224">
<p>«Споемте друзья»</p>
<p>(музыкотерапия)</p>
<p>(волонтерский)</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p>12.00-13.00</p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="99">
<p> </p>
</td>
<td style="text-align: center;" width="177">
<p>Волонтеры</p>
<p>Машеро Е.П.</p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="47">
<p>21.</p>
</td>
<td style="text-align: center;" width="224">
<p>Клуб «Пусть осень жизни будет золотой»</p>
</td>
<td style="text-align: center;" colspan="5" width="496">
<p>Каждая третья среда месяца</p>
<p>15.00-16.00</p>
</td>
<td style="text-align: center;" width="177">
<p>Яковцова С.К.</p>
<p>Сивенко И.П.</p>
</td>
</tr>
</tbody>
</table>

 </span>
HTML_ELDERLY_DAY_CARE
,
        'elderly-day-care' => <<<'HTML_ELDERLY_DAY_CARE_UPDATED'
<p>
 <strong>Отделение дневного пребывания для граждан пожилого возраста</strong>
</p>
<p>
 <strong>заведующий отделением:</strong> Усова Лилия Евгеньевна
</p>
<p><strong>Специалисты отделения:</strong></p>
<ul>
 <li>специалист по социальной работе Чевдарь Анна Григорьевна, телефоны <a href="tel:+375232357558">8 (0232) 35-75-58</a>, <a href="tel:+375256604248">8 (025) 660-42-48 (Life)</a>;</li>
 <li>специалист по социальной работе Малашенко Валентина Васильевна, телефоны <a href="tel:+375232357558">8 (0232) 35-75-58</a>, <a href="tel:+375256604248">8 (025) 660-42-48 (Life)</a>.</li>
</ul>
<p class="department-detail-social-link-row">
 <strong>Группа инстаграм:</strong> <a href="https://www.instagram.com/superage_gomel" target="_blank" rel="noopener noreferrer" aria-label="Instagram отделения дневного пребывания для граждан пожилого возраста">@superage_gomel</a>
</p>
<p>
 <strong>Режим работы:</strong>
</p>
<p>
 Понедельник – пятница с 8:30 до 17.30, обед с 13:00-14:00
</p>
<p>
 Отделение оказывает услуги неработающим гражданам в возрасте 60 лет и старше, достигшим общеустановленного пенсионного возраста, имеющим право на государственную пенсию.
</p>
<p>
 <strong>Основная задача отделения:</strong> помочь пожилым людям преодолеть одиночество, создать условия для выхода из социальной изоляции, наполнить жизнь новым смыслом, перейти к активному образу жизни, частично утраченному в связи с выходом на пенсию, создать условия для раскрытия творческого потенциала.
</p>
<p>
 <strong>Основные направления деятельности отделения:</strong>
</p>
<ul>
 <li>организация досуга и создание условий, способствующих общению и поддержанию активного образа жизни пожилых граждан путем проведения культурно-массовых и спортивно-оздоровительных мероприятий, организации кружков и клубов по интересам.</li>
</ul>
<p>
 <strong>Необходимые документы для посещения отделения:</strong>
</p>
<ul>
 <li>медицинская справка по форме 1здр/у - 10;</li>
 <li>справка о месте жительства и составе семьи;</li>
 <li>документ удостоверяющий личность: паспорт, идентификационная карта, вид на жительство и др.</li>
 <li>документ подтверждающий право на льготы: удостоверение инвалида (при наличии инвалидности), пенсионное удостоверение.</li>
</ul>
<p>
 <strong>Нормативная основа:</strong>
</p>
<ul>
 <li>Постановление Совета Министров Республики Беларусь от 27 декабря 2012 г. №1218.</li>
 <li>Постановление Министерства труда и социальной защиты Республики Беларусь 26.01.2013 №11.</li>
</ul>
<p>
 <strong>Условия оказания социальных услуг:</strong>
</p>
<p>
 социальные услуги в отделении оказываются на безвозмездной основе, за исключением услуг:
</p>
<ul>
 <li>обеспечение работы кружков по интересам;</li>
 <li>обучение компьютерной грамотности, в том числе по освоению социальных сетей, осуществлению платежей в Интернете.</li>
</ul>
<p>
 Данные виды услуг оказываются:
</p>
<ul>
 <li>на безвозмездной основе инвалидам I и II группы, малообеспеченным одиноким неработающим гражданам в возрасте 60 лет и старше, достигшим общеустановленного пенсионного возраста, имеющим право на государственную пенсию;</li>
 <li>на условиях частичной оплаты одиноким неработающим гражданам в возрасте 60 лет и старше, достигшим общеустановленного пенсионного возраста, имеющим право на государственную пенсию, среднедушевой доход которых не превышает 200 процентов утвержденного в установленном порядке бюджета прожиточного минимума в среднем на душу населения. Размер частичной оплаты для одинокого гражданина составляет 60 процентов тарифа на социальные услуги;</li>
 <li>на условиях полной оплаты неработающим гражданам в возрасте 60 лет и старше, достигшим общеустановленного пенсионного возраста, имеющим право на государственную пенсию.</li>
</ul>
<p>
 <strong>На безвозмездной основе работают:</strong>
</p>
<ul>
 <li><strong>Клуб по интересам «Школа здоровья»</strong> – теоретические занятия, направленные на укрепление и сохранение физического здоровья, беседы и лекции-практикумы по здоровому образу жизни с участием представителей здравоохранения.</li>
 <li><strong>Клуб по интересам «Вдохновение»</strong> – проведение культурно-досуговых мероприятий, направленных на повышение эмоционального фона граждан пожилого возраста, поддержание стремления к полноценной, активной жизни, установление дружеских контактов. Организация встреч, бесед, «круглых столов» с интересными людьми, выступления, свободные дискуссии. Встречи с работниками абонементного отдела учреждения культуры «Гомельская областная универсальная библиотека им.В.И. Ленина», осуществляется заказ интересующей литературы.</li>
 <li><strong>Клуб по интересам «В кругу друзей»</strong> – проведение вечеров отдыха, праздничных мероприятий, поздравление членов клуба с юбилеями, днями рождения, памятными датами, встречи со специалистами различных организаций.</li>
 <li><strong>Клуб по интересам «Хозяюшка»</strong> – обмен опытом по кулинарии, садоводству, рукоделию и другим вопросам, лекции, экскурсии и мастер-классы.</li>
 <li><strong>Клуб по интересам «Ритм жизни»</strong> – проведение мастер-классов по линейным танцам. Привитие участникам Клуба навыков выступлений в творческих группах.</li>
 <li><strong>Клуб по интересам «Интеллектуалы серебряного возраста»</strong> – проведение интеллектуальных турниров, лекций, информационных часов, дискуссий, викторин, интеллектуально-познавательных игр и т.д.</li>
</ul>
<p>
 На волонтерской основе в отделении действуют хоровой коллектив «Виктория», танцевальный коллектив «Акварель», проводятся занятия по изучению немецкого и английского языков, а также занятия по рисованию гуашью.
</p>
<p>
 Ежегодно проводятся мероприятия к памятным датам и праздникам. Пожилые граждане принимают участие в фестивалях и выставках.
</p>
<p>
 <strong>На возмездной основе работают:</strong>
</p>
<ul>
 <li><strong>Кружок по декоративно-прикладному творчеству «Рукодельница»</strong> – реализация творческих способностей в области декоративно-прикладного творчества.</li>
 <li><strong>Кружок кройки и шитья «Золотая нить»</strong> – приобретение знаний, умений и навыков шитья на швейной машинке.</li>
 <li><strong>Кружок «Бодрость духа»</strong>.</li>
 <li><strong>Кружок «Надежда»</strong> – групповые занятия по дыхательным практикам.</li>
 <li><strong>Кружок «Гармония»</strong> – танцевальные групповые занятия.</li>
 <li>Обучение компьютерной грамотности, в том числе по освоению социальных сетей, осуществлению платежей в Интернете.</li>
</ul>
HTML_ELDERLY_DAY_CARE_UPDATED
,
        'rehabilitation' => <<<'HTML_REHABILITATION'
<p>
 <span style="font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt; color: #000000;"><b><span style="color: #0000ff; font-size: 14pt;">Отделение социальной реабилитации, абилитации инвалидов,</span></b>&nbsp;</span>г.п.Сураж, ул.Шмырева, д.12, телефон <a href="tel:+375232209325">8 (0232) 209-325</a>:
</p>
<p>
</p>
<p>
 <span style="font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt; color: #000000;"> </span>
</p>
<p>
 <b>заведующий отделением: </b>Дубинская Екатерина Сергеевна, г. Гомель, ул. 50 лет БССР, д. 19, кабинет 16А, телефон <a href="tel:+375232639960">8 (0232) 639-960</a><br>
 <b>специалист по социальной работе:</b> Божко Наталья Сергеевна<br>
 <b>психолог:&nbsp;</b> Кириллова Татьяна Валентиновна, г. Гомель, 1-я Пролетарская, д.16, телефон <a href="tel:+375232670012">8 (0232) 670-012</a><br>
 <b>специалист по социальной работе: </b>_________________<br>
 <b>руководитель&nbsp;кружка: </b>Окунев Василий Васильевич<br>
 <b>рабочий по комплексному обслуживанию зданий и сооружений:</b> Китаев Андрей Анатольевич&nbsp;
</p>
<p>
 <span style="font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt; color: #000000;"><b><span style="color: #0000ff; font-size: 14pt;">Режим работы:</span></b></span>
</p>
<p>
	 с 7.45 до 13.00, с 14.00 до 17.15
</p>
<p>
</p>
<p>
	 с 13.00 до 14.00 – обеденный перерыв<br>
	 выходные дни: суббота, воскресенье
</p>
<p>
 <span style="font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt; color: #000000;"><b><span style="color: #0000ff; font-size: 14pt;">Цель деятельности отделения:</span></b></span>
</p>
<p>
	 оказание содействия в социально-бытовой, социально-трудовой и социально-психологической реабилитации ин­валидов, в том числе выпускников центров коррекционно-развивающего обуче­ния, в условиях дневного пребывания в отделении.<br>
</p>
<p>
 <span style="font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt; color: #000000;"><b><span style="color: #0000ff; font-size: 14pt;">Направления деятельности отделения:</span></b></span>
</p>
<ul>
	<li>оказание содействия в социально-бытовой, социально-трудовой и социально-психологической реабилитации инвалидов, в том числе выпускников центров коррекционно-развивающего обучения, в условиях дневного пребывания отделении;</li>
	<li>оказание содействия инвалидам в восстановлении (компенсации) нарушенных или утраченных вследствие заболевания навыков к самообслуживанию в подготовке к самостоятельной жизни;</li>
	<li>развитие и поддержание у инвалидов навыков поведения, самоконтроля, общения, приобретенных в ЦКРОиР, а также пользования техническими средствами социальной реабилитации;</li>
	<li>развитие творческих способностей и интересов у инвалидов, способностей к трудовой деятельности, трудовых навыков, обеспечивающих реализацию их потенциальных трудовых возможностей посредством трудотерапии, общения и индивидуального подхода;</li>
	<li>содействие в трудоустройстве инвалидов, успешно прошедших трудовую реабилитацию в отделении по обучаемой специальности;</li>
	<li>работа с родственниками инвалидов в целях организации преемственности реабилитационных мероприятий в семье;</li>
	<li>создание условий для удовлетворения потребностей инвалидов и членов их семей в общении;</li>
	<li>координация усилий специалистов различных профилей по выявлению и учету нуждающихся в специальной помощи;</li>
	<li>содействие в обеспечении инвалидов и других категорий граждан техническими средствами социальной реабилитации;</li>
	<li>предоставление услуг проката технических средств социальной реабилитации и прочих бытовых изделий и предметов личного пользования.</li>
</ul>
<p style="text-align: center;">
</p>
<p style="text-align: center;">
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><b><span style="color: #0000ff; font-size: 14pt;">ГРАФИК&nbsp;</span></b></span>
</p>
<p style="text-align: center;">
 <span style="color: #000000; font-family: &quot;Times New Roman&quot;, Times; font-size: 13pt;"><b><span style="color: #0000ff; font-size: 14pt;">работы кружков, клубов и социально-реабилитационных мастерских отделения социальной реабилитации, абилитации инвалидов </span></b></span>
</p>
 <br>
<table width="100%" border="1">
<tbody>
<tr>
<td rowspan="2" width="2">
<p>№</p>
<p>п/п</p>
</td>
<td style="text-align: center;" rowspan="2" width="2">
<p><strong>Наименование кружка</strong></p>
</td>
<td style="text-align: center;" colspan="5" width="10">
<p><strong>День недели/время работы</strong></p>
</td>
</tr>
<tr>
<td style="text-align: center;" width="2">
<p><strong>Понедельник</strong></p>
</td>
<td style="text-align: center;" width="2">
<p><strong>Вторник</strong></p>
</td>
<td style="text-align: center;" width="2">
<p><strong>Среда</strong></p>
</td>
<td style="text-align: center;" width="2">
<p><strong>Четверг</strong></p>
</td>
<td style="text-align: center;" width="2">
<p><strong>Пятница</strong></p>
</td>
</tr>
<tr>
<td width="2">
<p>1.</p>
</td>
<td width="2">
<p>Кружок арт-терапии по изготовлению сувенирной продукции «Чудеса в решете»</p>
</td>
<td style="text-align: center;" width="2">
<p>11.00-12.00</p>
</td>
<td style="text-align: center;" width="2">
<p> </p>
</td>
<td style="text-align: center;" width="2">
<p> 11.00-12.00</p>
</td>
<td style="text-align: center;" width="2">
<p> </p>
</td>
<td style="text-align: center;" width="2">
<p> </p>
</td>
</tr>
<tr>
<td width="2">
<p>2.</p>
</td>
<td width="2">
<p>Кружок когнитивной терапии «Студия знаний»</p>
</td>
<td style="text-align: center;" width="2">
<p> </p>
</td>
<td style="text-align: center;" width="2">
<p>11.00-12.30 </p>
</td>
<td style="text-align: center;" width="2">
<p> </p>
</td>
<td style="text-align: center;" width="2">
<p>11.00-12.30 </p>
</td>
<td style="text-align: center;" width="2">
<p> </p>
</td>
</tr>
<tr>
<td width="2">
<p>3.</p>
</td>
<td width="2">
<p>Социально-реабилитационная мастерская по работе с деревом «Столяр»</p>
</td>
<td style="text-align: center;" width="2">
<p>10.00-11.00</p>
</td>
<td style="text-align: center;" width="2">
<p>10.00-11.00</p>
</td>
<td style="text-align: center;" width="2">
<p>10.00-11.00</p>
</td>
<td style="text-align: center;" width="2">
<p>10.00-11.00</p>
</td>
<td style="text-align: center;" width="2">
<p>10.00-11.00</p>
</td>
</tr>
<tr>
<td width="2">
<p>4.</p>
</td>
<td width="2">
<p>Кружок социально-бытовой реабилитации «Вкусные истории» (онлайн) </p>
</td>
<td style="text-align: center;" colspan="5" width="10">
<p>3-я среда месяца 12.00-13.00</p>
</td>
</tr>
</tbody>
</table>
HTML_REHABILITATION
,
        'rehabilitation' => <<<'HTML_REHABILITATION_UPDATED'
<p>
 <strong>Отделение социальной реабилитации, абилитации инвалидов.</strong>
</p>
<p>
 <strong>Специалисты по социальной работе:</strong>
</p>
<ul>
 <li>Соболенко Наталья Львовна — телефон <a href="tel:+375256604212">+375 (25) 660-42-12</a>;</li>
 <li>Дембицкая Елена Павловна — телефон <a href="tel:+375256604212">+375 (25) 660-42-12</a>;</li>
 <li>Шелепень Кирилл Павлович — телефон <a href="tel:+375256604213">+375 (25) 660-42-13</a>;</li>
 <li>Тихонова Елена Викторовна — телефон <a href="tel:+375256604213">+375 (25) 660-42-13</a>;</li>
 <li>Марченко Ирина Викторовна — телефон <a href="tel:+375232349799">8 (0232) 34-97-99</a>.</li>
</ul>
<p>
 <strong>Направления работы:</strong>
</p>
<ul>
 <li>учет и реализация индивидуальных программ реабилитации, абилитации инвалидов, разработка индивидуальных планов социальной реабилитации, абилитации инвалидов, оценка их выполнения, ведение базы данных ИПРА и базы данных инвалидов;</li>
 <li>предоставление социальных услуг гражданам по обучению навыкам ухода за маломобильными людьми, организация работы ресурсной комнаты обучения навыкам ухода;</li>
 <li>предоставление социально-реабилитационных социальных услуг в полустационарной форме социального обслуживания;</li>
 <li>оказание услуг переводчика жестового языка;</li>
 <li>оказание услуг персонального ассистента;</li>
 <li>оказание услуг в дистанционной форме социального обслуживания для маломобильных граждан, в том числе лиц с тяжелыми и множественными нарушениями: реабилитационная работа, досуговая деятельность, консультации психологов и иных специалистов, развитие навыков, социальных связей и общения.</li>
</ul>
<p>
 <strong>Цель социальной реабилитации, абилитации:</strong>
</p>
<p>
 содействие социализации, социальной адаптации и интеграции инвалидов, восстановление разрушенных или утраченных ими социальных связей и отношений, социального статуса; создание условий для достижения ими максимально возможной степени самостоятельности и независимого проживания, для повышения индивидуальной мобильности и участия в жизни общества наравне с другими гражданами.
</p>
<p>
 <strong>Основные задачи социальной реабилитации, абилитации инвалидов на базе центра:</strong>
</p>
<ul>
 <li>формирование, восстановление, развитие и поддержание навыков самообслуживания и иных социальных, бытовых и коммуникативных навыков, а также навыков самостоятельного передвижения и ориентации, в том числе с использованием ТССР;</li>
 <li>содействие повышению уровня адаптированности человека к жизни с инвалидностью, формированию позитивных установок, мотивации к самостоятельному улучшению жизненной ситуации и активной жизненной позиции;</li>
 <li>восстановление разрушенных или формирование новых социальных связей, расширение социальных контактов и социального взаимодействия;</li>
 <li>восстановление социально и личностно значимых характеристик и возможностей инвалида, содействие самореализации, физическому и личностному развитию, профессиональному самоопределению, формированию доступных трудовых навыков и профессиональных компетенций;</li>
 <li>создание условий для реинтеграции в социум, участия в общественной и по возможности трудовой жизни, приобщения к культурному и художественному наследию;</li>
 <li>увеличение степени контроля человека с инвалидностью над его жизнью и влияния на принятие решений, касающихся его жизни;</li>
 <li>содействие повышению социального статуса и нормализации образа жизни, устранению ограничений и барьеров в повседневной деятельности.</li>
</ul>
<p>
 <strong>Социальная реабилитация, абилитация инвалидов осуществляется в нескольких направлениях:</strong>
</p>
<ul>
 <li>социально-бытовая реабилитация, абилитация, включающая формирование, восстановление и развитие навыков самообслуживания и иных социальных, бытовых, коммуникативных навыков;</li>
 <li>социально-психологическая реабилитация, абилитация и психологическая помощь;</li>
 <li>социальное обслуживание, включая оказание услуги персонального ассистента;</li>
 <li>развитие творчества, досуга, физической культуры и спорта инвалидов;</li>
 <li>иные виды социальной реабилитации, абилитации инвалидов в соответствии с ИПРА.</li>
</ul>
<p>
 <strong>Зачисление производится на основании нижеуказанных документов с последующим заключением договора:</strong>
</p>
<ul>
 <li>письменное заявление;</li>
 <li>согласие на обработку специальных персональных данных в случаях, предусмотренных законодательством о персональных данных;</li>
 <li>документ, удостоверяющий личность (копия);</li>
 <li>удостоверение инвалидности (копия);</li>
 <li>индивидуальная программа реабилитации, абилитации инвалида (копия);</li>
 <li>сведения о занимаемом в данном населенном пункте жилом помещении, месте жительства и составе семьи;</li>
 <li>медицинская справка о состоянии здоровья и (или) заключение ВКК для оказания социальных услуг в форме полустационарного социального обслуживания с указанием медицинских показаний и (или) отсутствия противопоказаний, а также функциональных классов;</li>
 <li>копия свидетельства о специальном образовании для выпускников, окончивших центр коррекционно-развивающего обучения и реабилитации, а также вспомогательные школы-интернаты.</li>
</ul>
<p>
 <strong>Услуга персонального ассистента</strong>
</p>
<p>
 Заключается в оказании помощи инвалидам в организации и осуществлении самостоятельной и независимой жизнедеятельности, включая содействие в освоении навыков самообслуживания, помощь в планировании и организации повседневной жизни, принятии решений по различным жизненным ситуациям, налаживании коммуникативных связей с другими людьми и иные виды помощи.
</p>
<p>
 Это может быть поиск необходимой информации, заполнение документов, посещение мастерских, концертов, выставок, оказание первой помощи, вызов врача, информирование родственников о состоянии здоровья и др. Основной целью введения такой услуги является расширение возможностей людей с инвалидностью, их участие в жизни общества, включая осуществление общественно полезной деятельности. При этом обеспечивается индивидуальный подход в каждом конкретном случае.
</p>
<p>
 <strong>Данный вид услуг предусмотрен для следующих категорий лиц:</strong>
</p>
<ul>
 <li>инвалиды, проживающие совместно с трудоспособными родственниками, обязанными по закону их содержать, имеющие резко выраженное ограничение способности к самостоятельному передвижению и (или) способности к ориентации, соответствующее ФК 4, и (или) умеренное или выраженное ограничение способности контролировать свое поведение, соответствующее ФК 2 или ФК 3. Для данной категории лиц услуга оказывается при необходимости до 20 часов в месяц;</li>
 <li>инвалиды, проживающие отдельно от трудоспособных родственников, обязанных по закону их содержать, и одинокие инвалиды, имеющие ограничение жизнедеятельности, соответствующее ФК 4 по самостоятельному передвижению и (или) ориентации. Для данной категории лиц услуга оказывается при необходимости до 40 часов в месяц;</li>
 <li>инвалиды, проживающие отдельно от трудоспособных родственников, обязанных по закону их содержать, и одинокие инвалиды, имеющие ограничение жизнедеятельности по контролю поведения, соответствующее ФК 2 или ФК 3. Для данной категории лиц услуга оказывается при необходимости до 60 часов в месяц.</li>
</ul>
<p>
 Если за гражданином, имеющим I группу инвалидности, осуществляет уход лицо, которое получает за это пособие, то персональный ассистент не положен.
</p>
<p>
 Услуга персонального ассистента оказывается с целью социальной адаптации и реабилитации людей с ограниченными возможностями здоровья, создания доступной среды для маломобильных групп населения, создания им широкой сферы информационных связей, определяющих возможность активного участия в общественной деятельности и успешного интегрирования в общество, а также поддержки и комплексного оказания социальных услуг наиболее незащищенным категориям граждан.
</p>
<p>
 <strong>В услугу входит:</strong>
</p>
<ul>
 <li>сопровождение от места проживания до пункта назначения и обратно, находясь рядом требуемое время;</li>
 <li>оказание помощи в передвижении по лестнице, преодолении бордюров, переходе проезжей части, при посещении мест трудоустройства, местных исполнительных и распорядительных органов, юридических консультаций, нотариуса, объектов социальной сферы, общественных, культурно-массовых и спортивных мероприятий, учреждений здравоохранения, организаций бытового обслуживания, торговли и иных организаций;</li>
 <li>выявление потребностей и пожеланий человека с инвалидностью;</li>
 <li>составление индивидуальной программы сопровождения.</li>
</ul>
<p>
 Работники территориального центра в течение трех рабочих дней со дня обращения проводят обследование материально-бытового положения гражданина с составлением акта обследования материально-бытового положения гражданина.
</p>
<p>
 <strong>За оказанием социальных услуг гражданин или его законный представитель обращается в территориальный центр по месту регистрации (месту жительства) и представляет следующие документы:</strong>
</p>
<ul>
 <li>документ, удостоверяющий личность;</li>
 <li>документ установленного образца о праве на льготы для граждан, относящихся к категории пользующихся льготами;</li>
 <li>согласие на обработку специальных персональных данных в случаях, предусмотренных законодательством о персональных данных;</li>
 <li>письменное заявление;</li>
 <li>индивидуальная программа реабилитации, абилитации инвалида, индивидуальная программа реабилитации, абилитации ребенка-инвалида или заключение врачебно-консультационной комиссии.</li>
</ul>
<p>
 Работники территориального центра в течение трех рабочих дней со дня обращения проводят обследование материально-бытового положения гражданина с составлением акта обследования материально-бытового положения гражданина.
</p>
<p>
 <strong>Услуга переводчика жестового языка</strong>
</p>
<p>
 Услуга переводчика жестового языка предоставляется людям с инвалидностью по слуху на безвозмездной основе до 90 часов в год.
</p>
<p>
 <strong>Услуга предусматривает:</strong>
</p>
<ul>
 <li>знакомство с получателем услуги, установление контакта;</li>
 <li>определение услуги и согласование с получателем;</li>
 <li>осуществление профессионального перевода с жестового и на жестовый язык во время сопровождения в организации здравоохранения, социального обслуживания и иные организации;</li>
 <li>содействие в осуществлении взаимодействия получателя услуги с другими людьми в процессе неформального общения;</li>
 <li>содействие в обеспечении защиты прав получателя услуги и его интеграции в общество.</li>
</ul>
<p>
 <strong>Документы, необходимые для оказания услуги:</strong>
</p>
<ul>
 <li>документ, удостоверяющий личность;</li>
 <li>документ установленного образца о праве на льготы для граждан, относящихся к категории пользующихся льготами;</li>
 <li>согласие на обработку специальных персональных данных в случаях, предусмотренных законодательством о персональных данных.</li>
</ul>
<p>
 <strong>Обучение лиц, осуществляющих уход за нетрудоспособными гражданами, навыкам ухода</strong>
</p>
<p>
 Получателями социальной услуги являются граждане, осуществляющие уход за нетрудоспособным гражданином. Услуга бесплатная и предоставляется всем, кто столкнулся с ситуацией, когда нужно организовать грамотный уход за близким человеком, а знаний и практических навыков для этого не хватает.
</p>
<p>
 <strong>На занятиях можно узнать:</strong>
</p>
<ul>
 <li>об организации безопасного пространства в доме;</li>
 <li>об использовании технических средств реабилитации;</li>
 <li>о профилактике осложнений при малоподвижном образе жизни;</li>
 <li>об основных алгоритмах проведения гигиенических процедур;</li>
 <li>о методах психологической помощи и самопомощи.</li>
</ul>
<p>
 Практические занятия проводятся в групповой или индивидуальной форме, в специально оборудованном кабинете по адресу: 50 лет БССР, 19, или на дому у получателя услуги.
</p>
<p>
 <strong>Кружки по интересам, реабилитационно-трудовые мастерские</strong>
</p>
<p>
 Для повышения социально-психологической компетенции и формирования социальных и трудовых навыков для инвалидов проводятся занятия в форме кружковой деятельности:
</p>
<ul>
 <li>кружок социально-бытовой адаптации «Я сам»;</li>
 <li>оздоровительный кружок «Чемпион»;</li>
 <li>кружок компьютерной грамотности «Матрица»;</li>
 <li>кружок декоративно-прикладного творчества «Творческая мастерская»;</li>
 <li>коррекционный кружок «Перспектива»;</li>
 <li>кружок по адаптации текстов на ясный язык «Ясный язык – ясно и просто»;</li>
 <li>кружок «Студия рукоделия»;</li>
 <li>кружок досуговой деятельности «Калейдоскоп развлечений».</li>
</ul>
<p>
 В отделении работают две реабилитационные мастерские:
</p>
<ul>
 <li>реабилитационная мастерская по обучению навыкам шитья «Катушка»;</li>
 <li>реабилитационная мастерская «Изнанка» — занятия по различным видам декоративно-прикладного творчества, работа с глиной.</li>
</ul>
<p>
 <strong>Дистанционная форма социального обслуживания</strong>
</p>
<ul>
 <li>консультационно-информационные услуги — консультирование и информирование по вопросам оказания социальных услуг и социальной поддержки, обязательное условие — заключение договора;</li>
 <li>социально-психологические услуги;</li>
 <li>социально-реабилитационные услуги;</li>
 <li>содействие в организации деятельности групп взаимопомощи и самопомощи;</li>
 <li>обеспечение работы кружков по интересам в форме дистанционного социального обслуживания — до 2 раз в неделю;</li>
 <li>обеспечение работы клубов по интересам — 1 раз в месяц.</li>
</ul>
<p>
 <strong>Кружки по интересам на дому</strong>
</p>
<p>
 Услуга предоставляется инвалидам I и II группы, детям-инвалидам в возрасте до 18 лет, завершившим освоение содержания образовательной программы специального образования на уровне среднего образования для лиц с интеллектуальной недостаточностью, с ФК 4 по самообслуживанию и ФК 4 по передвижению.
</p>
<p>
 <strong>Документы, предоставляемые гражданином или законным представителем для заключения договора:</strong>
</p>
<ul>
 <li>согласие гражданина на обработку специальных персональных данных;</li>
 <li>письменное заявление;</li>
 <li>документ, удостоверяющий личность гражданина;</li>
 <li>документ установленного образца о праве на льготы;</li>
 <li>индивидуальная программа реабилитации, абилитации инвалида или заключение ВКК.</li>
</ul>
<p>
 Медицинские противопоказания по предоставлению данной услуги: психические расстройства и расстройства поведения, приведшие к резко выраженному ограничению способности контролировать свое поведение, нахождение под диспансерным наблюдением врача-психиатра-нарколога.
</p>
<p>
 На базе отделения организовываются познавательные экскурсии, походы, мероприятия, приуроченные к памятным датам, мастер-классы с целью создания активной формы досуга, расширения кругозора. Проводятся тематические лектории и беседы с целью развития правовой и цифровой грамотности, нравственной и социальной культуры, профилактики преступлений и правонарушений в отношении людей с инвалидностью, гибели от внешних причин.
</p>
<p>
 <strong>Отчисление подопечных из отделения производится приказом директора учреждения «ТЦСОН Железнодорожного района г. Гомеля» по личному заявлению гражданина либо в случае:</strong>
</p>
<ul>
 <li>выявления медицинских противопоказаний, включая психические заболевания в стадии обострения, хронический алкоголизм, наркоманию, венерические и инфекционные заболевания, активные формы туберкулеза, иные заболевания, требующие лечения в специализированных учреждениях здравоохранения;</li>
 <li>нарушения внутреннего распорядка отделения, антиобщественного и асоциального поведения;</li>
 <li>других причин, препятствующих дальнейшему пребыванию в отделении.</li>
</ul>
HTML_REHABILITATION_UPDATED
,
    );

    $departmentId = (string) $departmentId;

    return isset($contents[$departmentId]) ? $contents[$departmentId] : '';
}

function formatDepartmentDetailContentHtml($html, $departmentTitle = '')
{
    $html = trim((string) $html);
    if ($html === '') {
        return '';
    }

    $departmentTitle = trim((string) $departmentTitle);

    if (!class_exists('DOMDocument')) {
        return preg_replace('/\s(?:style|class|id)="[^"]*"/i', '', $html);
    }

    $document = new DOMDocument('1.0', 'UTF-8');
    $previous = libxml_use_internal_errors(true);
    $document->loadHTML(
        '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body><div id="department-content-root">' . $html . '</div></body></html>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    $root = $document->getElementById('department-content-root');
    if (!$root) {
        return '';
    }

    unwrapDepartmentDetailTags($document, $root, array('span', 'font'));
    cleanDepartmentDetailNode($root);
    splitDepartmentDetailParagraphsByBreaks($document, $root);
    markDepartmentDetailHeadings($root, $departmentTitle);
    removeDuplicateDepartmentDetailTitle($root, $departmentTitle);
    simplifyDepartmentDetailContacts($document, $root);
    markDepartmentDetailListLikeParagraphs($root);
    removeEmptyDepartmentDetailNodes($root);

    $output = '';
    foreach ($root->childNodes as $childNode) {
        $output .= $document->saveHTML($childNode);
    }

    return trim((string) $output);
}

function unwrapDepartmentDetailTags(DOMDocument $document, DOMNode $root, array $tagNames)
{
    foreach ($tagNames as $tagName) {
        while (true) {
            $nodes = $root instanceof DOMElement
                ? $root->getElementsByTagName($tagName)
                : $document->getElementsByTagName($tagName);

            if ($nodes->length === 0) {
                break;
            }

            $node = $nodes->item(0);
            if (!$node || !$node->parentNode) {
                break;
            }

            while ($node->firstChild) {
                $node->parentNode->insertBefore($node->firstChild, $node);
            }
            $node->parentNode->removeChild($node);
        }
    }
}

function cleanDepartmentDetailNode(DOMNode $node)
{
    if ($node instanceof DOMElement) {
        $allowedAttributes = array(
            'a' => array('href', 'target', 'rel', 'title'),
            'td' => array('colspan', 'rowspan'),
            'th' => array('colspan', 'rowspan', 'scope'),
        );

        $tagName = strtolower($node->tagName);
        $attributesToRemove = array();
        foreach ($node->attributes as $attribute) {
            $attributeName = strtolower($attribute->name);
            if (!in_array($attributeName, $allowedAttributes[$tagName] ?? array(), true)) {
                $attributesToRemove[] = $attribute->name;
            }
        }

        foreach ($attributesToRemove as $attributeName) {
            $node->removeAttribute($attributeName);
        }

        if ($tagName === 'a') {
            $href = (string) $node->getAttribute('href');
            if ($href !== '' && !preg_match('#^(https?://|mailto:|tel:|/)#i', $href)) {
                $node->removeAttribute('href');
            }
        }
    }

    foreach (iterator_to_array($node->childNodes) as $childNode) {
        cleanDepartmentDetailNode($childNode);
    }
}

function splitDepartmentDetailParagraphsByBreaks(DOMDocument $document, DOMNode $root)
{
    if (!$root instanceof DOMElement) {
        return;
    }

    $paragraphs = iterator_to_array($root->getElementsByTagName('p'));
    foreach ($paragraphs as $paragraph) {
        if (!$paragraph->getElementsByTagName('br')->length || !$paragraph->parentNode) {
            continue;
        }

        $newParagraphs = array();
        $currentParagraph = $document->createElement('p');

        while ($paragraph->firstChild) {
            $childNode = $paragraph->firstChild;
            $paragraph->removeChild($childNode);

            if ($childNode instanceof DOMElement && strtolower($childNode->tagName) === 'br') {
                if (departmentDetailNodeHasContent($currentParagraph)) {
                    $newParagraphs[] = $currentParagraph;
                }
                $currentParagraph = $document->createElement('p');
                continue;
            }

            $currentParagraph->appendChild($childNode);
        }

        if (departmentDetailNodeHasContent($currentParagraph)) {
            $newParagraphs[] = $currentParagraph;
        }

        foreach ($newParagraphs as $newParagraph) {
            $paragraph->parentNode->insertBefore($newParagraph, $paragraph);
        }

        $paragraph->parentNode->removeChild($paragraph);
    }
}

function markDepartmentDetailHeadings(DOMNode $root, $departmentTitle = '')
{
    if (!$root instanceof DOMElement) {
        return;
    }

    $departmentTitle = trim((string) $departmentTitle);

    $headingPatterns = array(
        '/^режим работы:?$/iu',
        '/^цель деятельности отделения:?$/iu',
        '/^направления деятельности отделения:?$/iu',
        '/^основными задачами деятельности отделения являются:?$/iu',
        '/^график\b/iu',
        '/^клубы и кружки:?$/iu',
        '/^платные кружки:?$/iu',
        '/^бесплатные кружки:?$/iu',
    );

    foreach ($root->getElementsByTagName('p') as $paragraph) {
        $text = normalizeDepartmentDetailText($paragraph->textContent);
        if ($text === '') {
            continue;
        }

        $isHeading = false;
        foreach ($headingPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                $isHeading = true;
                break;
            }
        }

        if ($isHeading) {
            $paragraph->setAttribute('class', 'department-detail-source__heading');
            continue;
        }

        if (preg_match('/^отделение\b/iu', $text)) {
            $paragraph->setAttribute('class', 'department-detail-source__intro');
            if ($departmentTitle !== '') {
                while ($paragraph->firstChild) {
                    $paragraph->removeChild($paragraph->firstChild);
                }
                $paragraph->appendChild($paragraph->ownerDocument->createTextNode($departmentTitle));
            }
        }
    }

    foreach ($root->getElementsByTagName('strong') as $strong) {
        $text = normalizeDepartmentDetailText($strong->textContent);
        foreach ($headingPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                $strong->setAttribute('class', 'department-detail-source__strong-heading');
                break;
            }
        }
    }
}

function markDepartmentDetailListLikeParagraphs(DOMNode $root)
{
    if (!$root instanceof DOMElement) {
        return;
    }

    foreach ($root->getElementsByTagName('p') as $paragraph) {
        if ($paragraph->hasAttribute('class')) {
            continue;
        }

        $text = normalizeDepartmentDetailText($paragraph->textContent);
        if (preg_match('/^[-–—.]\s*/u', $text)) {
            $paragraph->setAttribute('class', 'department-detail-source__list-line');
            removeDepartmentDetailLeadingMarker($paragraph);
        }
    }
}

function simplifyDepartmentDetailContacts(DOMDocument $document, DOMNode $root)
{
    if (!$root instanceof DOMElement) {
        return;
    }

    $paragraphs = iterator_to_array($root->getElementsByTagName('p'));
    foreach ($paragraphs as $paragraph) {
        if (!$paragraph->parentNode) {
            continue;
        }

        $text = normalizeDepartmentDetailText($paragraph->textContent);
        if ($text === '') {
            continue;
        }

        if (preg_match('/^заведующ(?:ий|ая)\s+отделени(?:ем|я)\s*:/iu', $text)) {
            $paragraph->parentNode->removeChild($paragraph);
            continue;
        }

        if (preg_match('/^(адрес|телефон)\s*:/iu', $text)) {
            $paragraph->parentNode->removeChild($paragraph);
            continue;
        }

        if (!preg_match('/^(контакты|контактные телефоны отделения)\s*:/iu', $text)) {
            continue;
        }

        $label = preg_match('/^контактные телефоны отделения\s*:/iu', $text)
            ? 'Контактные телефоны отделения:'
            : 'Контакты:';

        while ($paragraph->firstChild) {
            $paragraph->removeChild($paragraph->firstChild);
        }

        $strong = $document->createElement('strong');
        $strong->appendChild($document->createTextNode($label));
        $paragraph->appendChild($strong);
        $paragraph->appendChild($document->createTextNode(' '));

        $phones = extractDepartmentDetailPhoneLinks($document, $text);
        foreach ($phones as $phoneIndex => $phoneItem) {
            if ($phoneIndex > 0) {
                $paragraph->appendChild($document->createTextNode(', '));
            }

            $link = $document->createElement('a');
            $link->setAttribute('href', 'tel:' . $phoneItem['href']);
            $link->appendChild($document->createTextNode($phoneItem['label']));
            $paragraph->appendChild($link);
        }

        $paragraph->appendChild($document->createTextNode('.'));
    }
}

function removeDuplicateDepartmentDetailTitle(DOMNode $root, $departmentTitle = '')
{
    if (!$root instanceof DOMElement) {
        return;
    }

    $departmentTitle = normalizeDepartmentDetailText($departmentTitle);
    if ($departmentTitle === '') {
        return;
    }

    $titleSeen = false;
    $paragraphs = iterator_to_array($root->getElementsByTagName('p'));
    foreach ($paragraphs as $paragraph) {
        if (!$paragraph->parentNode) {
            continue;
        }

        $text = normalizeDepartmentDetailText($paragraph->textContent);
        if ($text !== $departmentTitle) {
            continue;
        }

        if (!$titleSeen) {
            $titleSeen = true;
            continue;
        }

        $paragraph->parentNode->removeChild($paragraph);
    }
}

function extractDepartmentDetailPhoneLinks(DOMDocument $document, $text)
{
    $phones = array();

    if (preg_match_all('/(?:\+375\s*\(\d{2}\)\s*\d{3}[-\s]?\d{2}[-\s]?\d{2}|8\s*\(0\d{3}\)\s*\d{2}[-\s]?\d{2}[-\s]?\d{2,3})/u', (string) $text, $matches)) {
        foreach ($matches[0] as $match) {
            $label = trim((string) preg_replace('/\s+/u', ' ', $match));
            $hrefDigits = preg_replace('/\D+/u', '', $label);
            if ($hrefDigits === '') {
                continue;
            }

            if (str_starts_with($hrefDigits, '80')) {
                $hrefDigits = '375' . substr($hrefDigits, 1);
            }

            $phones[] = array(
                'label' => $label,
                'href' => '+' . $hrefDigits,
            );
        }
    }

    return $phones;
}

function removeDepartmentDetailLeadingMarker(DOMNode $node)
{
    if ($node instanceof DOMText) {
        $originalValue = $node->nodeValue;
        $newValue = preg_replace('/^[\s\x{00A0}]*[-–—.]\s*/u', '', $originalValue, 1);
        if ($newValue !== $originalValue) {
            $node->nodeValue = $newValue;
            return true;
        }

        if (normalizeDepartmentDetailText($originalValue) === '') {
            $node->nodeValue = '';
            return false;
        }

        return true;
    }

    foreach ($node->childNodes as $childNode) {
        if (removeDepartmentDetailLeadingMarker($childNode)) {
            return true;
        }
    }

    return false;
}

function removeEmptyDepartmentDetailNodes(DOMNode $root)
{
    if (!$root instanceof DOMElement) {
        return;
    }

    $paragraphs = iterator_to_array($root->getElementsByTagName('p'));
    foreach ($paragraphs as $paragraph) {
        $hasContent = normalizeDepartmentDetailText($paragraph->textContent) !== '';
        foreach ($paragraph->childNodes as $childNode) {
            if ($childNode instanceof DOMElement && strtolower($childNode->tagName) === 'br') {
                continue;
            }
            if ($childNode instanceof DOMElement) {
                $hasContent = true;
                break;
            }
        }

        if (!$hasContent && $paragraph->parentNode) {
            $paragraph->parentNode->removeChild($paragraph);
        }
    }

    foreach (array('ul', 'ol') as $tagName) {
        $lists = iterator_to_array($root->getElementsByTagName($tagName));
        foreach ($lists as $list) {
            if (!departmentDetailNodeHasContent($list) && $list->parentNode) {
                $list->parentNode->removeChild($list);
            }
        }
    }
}

function departmentDetailNodeHasContent(DOMNode $node)
{
    if (normalizeDepartmentDetailText($node->textContent) !== '') {
        return true;
    }

    foreach ($node->childNodes as $childNode) {
        if ($childNode instanceof DOMElement && strtolower($childNode->tagName) !== 'br') {
            return true;
        }
    }

    return false;
}

function normalizeDepartmentDetailText($text)
{
    $text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = str_replace("\xc2\xa0", ' ', $text);
    $text = preg_replace('/\s+/u', ' ', $text);

    return trim((string) $text);
}

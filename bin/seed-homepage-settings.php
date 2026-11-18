<?php
/**
 * One-time seed: fills each language's Homepage Settings with the text that
 * used to live in the .po translation files, so the site renders exactly the
 * same after the move to settings. Existing non-empty values are never
 * overwritten, so the script is safe to re-run.
 *
 * Usage: ddev wp eval-file bin/seed-homepage-settings.php
 *
 * @package mooblipoisid-child
 */

do_action( 'init' ); // Polylang's pll_* helpers are no-ops before init.

$seed = array(
	'ru' => array(
		'sectors_eyebrow' => 'Для кого мы работаем',
		'sectors_title' => 'Мебель для коммерческих интерьеров',
		'sectors_intro' => 'Каждое заведение уникально, но требования одни и те же: долгие часы работы, постоянная нагрузка и бренд, который должен выглядеть правильно с первого дня.',
		'sector_title_hospitality' => 'Кофейни, бары и рестораны',
		'sector_title_retail' => 'Ритейл и торговые интерьеры',
		'sector_title_private' => 'Частные кухни',
		'sector_alt_hospitality' => 'Kohviku ja baari interjöör pika puidust letiga',
		'sector_alt_retail' => 'Kaupluse vitriinriiulid ja kassalauk',
		'sector_alt_private' => 'Erilahendusega tammepuidust köök',
		'sectors_link_hospitality' => 'Смотреть ассортимент для общепита',
		'sectors_link_retail' => 'Смотреть ассортимент для ритейла',
		'sectors_link_private' => 'Запросить предложение',
		'ranges_eyebrow' => 'Что мы производим',
		'ranges_title' => 'Полный ассортимент для бизнеса',
		'ranges_intro' => 'Изготавливается по вашим чертежам или в сотрудничестве с вашим архитектором и производится единым комплектом, чтобы всё идеально сошлось в день монтажа.',
		'ranges_col_hospitality' => 'Кофейни, бары и рестораны',
		'ranges_col_retail' => 'Магазины',
		'projects_eyebrow' => 'Избранные работы',
		'projects_title' => 'Недавние коммерческие и ритейл-проекты',
		'projects_card_link' => 'Обсудить похожий проект',
		'caps_eyebrow' => 'Возможности',
		'caps_title' => 'Одна команда. Всё для вашего интерьера.',
		'caps_intro' => 'Бар — это редко только дерево. Поскольку мебель и элементы интерьера изготавливаются в собственной мастерской, детали приходят отделанными и готовыми к монтажу — без беготни между поставщиками.',
		'process_eyebrow' => 'Процесс',
		'process_title' => 'От брифа до дня открытия',
		'process_intro' => 'Мы работаем по вашему графику отделки, координируем действия с архитектором и подрядчиками и монтируем с учётом вашей даты открытия.',
		'stories_eyebrow' => 'Истории клиентов',
		'stories_title' => 'Что говорят наши клиенты',
		'ethos_badge_title' => 'Собственное производство',
		'ethos_badge_text' => 'Мебель и интерьер под одной крышей в Таллине',
		'ethos_point1_title' => 'Готово к нагрузке',
		'ethos_point1_text' => 'Стойкие к нагреву и царапинам столешницы, усиленные соединения и фурнитура коммерческого класса для ежедневного использования.',
		'ethos_point2_title' => 'Одна команда от начала до конца',
		'ethos_point2_text' => 'Люди, которые проектируют ваши изделия, также их изготавливают и монтируют, поэтому один контакт отвечает за весь проект.',
		'ethos_image_alt' => 'Мебельная мастерская Mööblipoisid в цехе (запасное фото, ожидается профессиональная фотосъёмка)',
		'consult_contact1_role' => 'Руководитель производства — Андре Мерила',
		'consult_contact2_role' => 'Руководитель проектов — Мэрилин Салу',
		'consult_visit_title' => 'Посещение мастерской и образцов',
		'consult_visit_text' => 'Ознакомьтесь с материалами, отделками и проектами в работе в нашей мастерской по адресу Võidu 75, Таллин, по предварительной договорённости.',
		'form_honeypot_label' => 'Оставьте это поле пустым',
		'form_success' => 'Спасибо. Ваш запрос передан нашей проектной команде. Мы рассмотрим ваш бриф в течение 48 часов.',
		'form_error' => 'При отправке запроса что-то пошло не так. Пожалуйста, напишите напрямую на marilyn@mooblipoisid.ee.',
		'form_label_name' => 'Полное имя *',
		'form_placeholder_name' => 'напр. Астрид Линдгрен',
		'form_label_company' => 'Компания / заведение *',
		'form_placeholder_company' => 'напр. Кофейня Nordlys OÜ',
		'form_label_email' => 'Электронная почта *',
		'form_label_phone' => 'Номер телефона',
		'form_label_project_type' => 'Тип проекта *',
		'form_option_pick' => 'Пожалуйста, выберите…',
		'form_opt_type_cafe' => 'Кофейня, бар или ресторан',
		'form_opt_type_retail' => 'Магазин / торговый интерьер',
		'form_opt_type_counters' => 'Только стойки или места для гостей',
		'form_opt_type_multi' => 'Решение для нескольких объектов',
		'form_opt_type_private' => 'Частная кухня',
		'form_label_location' => 'Местоположение заведения / магазина',
		'form_placeholder_location' => 'напр. Таллин, Стокгольм, Хельсинки, Рига',
		'form_label_brief' => 'Бриф и размеры',
		'form_placeholder_brief' => 'Расскажите, что вам нужно: напр. бар 6 м с эспрессо-станцией и подстоечными холодильниками, 40 мест на банкетках, брендированная кассовая стойка. Укажите бюджет или данные архитектора, если они есть.',
		'form_upload_label' => 'Приложите планы, чертежи или концепт-борды',
		'form_upload_hint' => 'PDF, DWG, PNG, JPEG до 25 МБ',
		'form_file_remove' => 'Удалить файл',
		'form_submit' => 'Отправить запрос на проект',
		'header_cta_label' => 'Запросить предложение',
		'backtotop_label' => 'Tagasi üles',
		'footer_tagline' => 'Мебель и столярные изделия на заказ для кофеен, баров, ресторанов и ритейла, а также частные кухни, созданные с надёжной основательностью.',
		'footer_label_workshop' => 'Мастерская',
		'footer_label_discover' => 'Исследуйте',
		'footer_label_searches' => 'Запросы',
		'footer_searches_text' => 'Для отделки заведений, расширения ритейл-сетей и партнёрства с архитекторами отправьте ваш бриф прямо на наш проектный стол.',
		'footer_cta_label' => 'Запросить предложение',
		'footer_copyright' => 'Все права защищены.',
		'footer_legal_commission' => 'Условия комиссии',
		'footer_legal_materials' => 'Происхождение материалов',
		'footer_legal_privacy' => 'Политика конфиденциальности',
	),
	'en' => array(
		'sectors_eyebrow' => 'Who We Build For',
		'sectors_title' => 'Furniture Made for Commercial Interiors',
		'sectors_intro' => 'Every venue is different, but the demands are the same: long opening hours, constant use and a brand that has to look right from day one.',
		'sector_title_hospitality' => 'Cafés, Bars & Restaurants',
		'sector_title_retail' => 'Retail & Shopfitting',
		'sector_title_private' => 'Private Kitchens',
		'sector_alt_hospitality' => 'Kohviku ja baari interjöör pika puidust letiga',
		'sector_alt_retail' => 'Kaupluse vitriinriiulid ja kassalauk',
		'sector_alt_private' => 'Erilahendusega tammepuidust köök',
		'sectors_link_hospitality' => 'See the Hospitality Range',
		'sectors_link_retail' => 'See the Retail Range',
		'sectors_link_private' => 'Request a Quote',
		'ranges_eyebrow' => 'What We Make',
		'ranges_title' => 'The Full Commercial Range',
		'ranges_intro' => 'Made to your drawings or developed with your architect, and produced as a complete package so that everything fits together on installation day.',
		'ranges_col_hospitality' => 'Cafés, Bars & Restaurants',
		'ranges_col_retail' => 'Retail Shops',
		'projects_eyebrow' => 'Selected Commissions',
		'projects_title' => 'Recent Venue & Retail Projects',
		'projects_card_link' => 'Discuss a Similar Project',
		'caps_eyebrow' => 'Capabilities',
		'caps_title' => 'One partner. Everything your space needs.',
		'caps_intro' => 'A bar is rarely just timber. Because the furniture and interior elements are made in our own workshop, parts arrive finished and ready to install, with no chasing between suppliers.',
		'process_eyebrow' => 'The Process',
		'process_title' => 'From Brief to Opening Day',
		'process_intro' => 'We work to your fit-out programme, coordinate with your architect and contractors, and install around your opening date.',
		'stories_eyebrow' => 'Client Stories',
		'stories_title' => 'What Our Clients Say',
		'ethos_badge_title' => 'In-House Production',
		'ethos_badge_text' => 'Furniture and interiors under one roof in Tallinn',
		'ethos_point1_title' => 'Built to Take the Wear',
		'ethos_point1_text' => 'Heat- and scratch-resistant tops, reinforced joints and commercial-grade hardware for all-day, every-day use.',
		'ethos_point2_title' => 'One Team, Start to Finish',
		'ethos_point2_text' => 'The people who design your pieces also build and install them, so one contact is accountable for the whole project.',
		'ethos_image_alt' => 'Mööblipoisid workshop interior (placeholder photo, awaiting real photography)',
		'consult_contact1_role' => 'Production Manager — Andre Merila',
		'consult_contact2_role' => 'Project Manager — Marilyn Salu',
		'consult_visit_title' => 'Workshop & Sample Visits',
		'consult_visit_text' => 'See materials, finishes and work in progress at our workshop at Võidu 75, Tallinn, by appointment.',
		'form_honeypot_label' => 'Leave this field empty',
		'form_success' => 'Thank you. Your enquiry has been routed to our project team. We will review your brief within 48 hours.',
		'form_error' => 'Something went wrong sending your enquiry. Please email marilyn@mooblipoisid.ee directly.',
		'form_label_name' => 'Full Name *',
		'form_placeholder_name' => 'e.g. Astrid Lindgren',
		'form_label_company' => 'Company / Venue *',
		'form_placeholder_company' => 'e.g. Café Nordlys OÜ',
		'form_label_email' => 'Email Address *',
		'form_label_phone' => 'Phone Number',
		'form_label_project_type' => 'Project Type *',
		'form_option_pick' => 'Please choose…',
		'form_opt_type_cafe' => 'Café, Bar or Restaurant',
		'form_opt_type_retail' => 'Retail Store / Shopfit',
		'form_opt_type_counters' => 'Counters or Seating Only',
		'form_opt_type_multi' => 'Multi-Site Roll-Out',
		'form_opt_type_private' => 'Private Kitchen',
		'form_label_location' => 'Venue / Store Location',
		'form_placeholder_location' => 'e.g. Tallinn, Stockholm, Helsinki, Riga',
		'form_label_brief' => 'Brief & Dimensions',
		'form_placeholder_brief' => 'Tell us what you need: e.g. 6 m bar with espresso station and under-counter fridges, 40 seats of banquette, branded checkout counter. Include budget range or architect details if you have them.',
		'form_upload_label' => 'Attach Floor Plans, Drawings or Concept Boards',
		'form_upload_hint' => 'PDF, DWG, PNG, JPEG up to 25MB',
		'form_file_remove' => 'Remove file',
		'form_submit' => 'Submit Project Enquiry',
		'header_cta_label' => 'Request a Quote',
		'backtotop_label' => 'Tagasi üles',
		'footer_tagline' => 'Custom furniture and joinery for cafés, bars, restaurants and retail, plus bespoke private kitchens, crafted with disciplined permanence.',
		'footer_label_workshop' => 'Workshop',
		'footer_label_discover' => 'Explore',
		'footer_label_searches' => 'Direct Inquiries',
		'footer_searches_text' => 'For venue fit-outs, retail roll-outs and architect partnerships, send your brief straight to our projects desk.',
		'footer_cta_label' => 'Request a Quote',
		'footer_copyright' => 'All rights reserved.',
		'footer_legal_commission' => 'Terms of Commission',
		'footer_legal_materials' => 'Material Provenance',
		'footer_legal_privacy' => 'Privacy Policy',
	),
);

$option_name = mooblipoisid_homepage_option_name();
$stored = get_option( $option_name, array() );
$stored = is_array( $stored ) ? $stored : array();
$stored = mooblipoisid_migrate_homepage_settings( $stored );

$filled = 0;
$kept   = 0;

foreach ( $seed as $language => $fields ) {
	if ( ! isset( $stored[ $language ] ) || ! is_array( $stored[ $language ] ) ) {
		$stored[ $language ] = array();
	}
	foreach ( $fields as $field => $value ) {
		$current = isset( $stored[ $language ][ $field ] ) ? (string) $stored[ $language ][ $field ] : '';
		if ( '' === $current ) {
			$stored[ $language ][ $field ] = $value;
			$filled++;
			continue;
		}
		$kept++;
	}
}

update_option( $option_name, $stored );

echo "seeded: {$filled} values filled, {$kept} already set (kept).\n";
foreach ( array( 'et', 'ru', 'en' ) as $language ) {
	$count = isset( $stored[ $language ] ) && is_array( $stored[ $language ] ) ? count( $stored[ $language ] ) : 0;
	echo "  {$language}: {$count} fields stored\n";
}

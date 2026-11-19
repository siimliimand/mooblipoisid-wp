<?php
/**
 * Seed the homepage FAQ entries in all three languages.
 *
 * Six questions per language, translations linked in groups. Every question
 * and answer is grounded in the site's own copy (the commission terms, the
 * material provenance page and the homepage process/consultation text) — no
 * invented facts, no prices, no delivery guarantees.
 *
 * The FAQ post type (`mooblipoisid_faq`) carries the question in the title and
 * the answer in the editor; entries render in menu_order ASC. Answers are
 * stored as plain <p> HTML (classic-editor content, no Gutenberg block
 * comments) so the theme's wp_kses_post(wpautop()) rendering stays simple.
 *
 * Idempotent: an item whose Estonian title already exists is skipped (its
 * missing translations are still created and linked), so re-running never
 * duplicates entries. Editors maintain the questions afterwards in wp-admin;
 * do not re-run the seed after manual edits unless overwriting is intended.
 *
 * Usage: ddev wp eval-file bin/seed-faq.php
 *
 * @package mooblipoisid-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'init' ); // Polylang's pll_* helpers are no-ops before init.

if ( ! function_exists( 'pll_set_post_language' ) ) {
	echo "Polylang not active, aborting.\n";
	exit( 1 );
}

/**
 * Find an existing FAQ item by its Estonian (default-language) title.
 *
 * Queries every language at once (`lang => 0` bypasses Polylang's filter) and
 * compares titles exactly, so the match never depends on the CLI language.
 *
 * @param string $title_et Estonian question (the default-language title).
 * @return int Post ID, 0 when not found.
 */
function mp_faq_find_by_et_title( $title_et ) {
	$posts = get_posts(
		array(
			'post_type'              => 'mooblipoisid_faq',
			'post_status'            => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page'         => -1,
			'lang'                   => 0,
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'suppress_filters'       => true,
		)
	);

	foreach ( (array) $posts as $post ) {
		if ( trim( (string) $post->post_title ) === trim( (string) $title_et ) ) {
			return (int) $post->ID;
		}
	}

	return 0;
}

/**
 * Create one FAQ item (question = title, answer = plain <p> content).
 *
 * @param string $question Question text.
 * @param string $answer   Answer text (plain sentences, no HTML markup).
 * @param string $lang     Language slug (`et`, `ru`, `en`).
 * @param int    $order    Homepage order (menu_order), 1-based.
 * @return int Post ID.
 */
function mp_faq_create( $question, $answer, $lang, $order ) {
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'mooblipoisid_faq',
			'post_status'  => 'publish',
			'post_title'   => $question,
			'post_content' => '<p>' . $answer . '</p>',
			'menu_order'   => $order,
		)
	);

	pll_set_post_language( $post_id, $lang );
	echo "created  #{$post_id} {$lang} {$question}\n";

	return $post_id;
}

/* =========================================================================
 * The six FAQ items × three languages. Grounded in:
 *   - Komisjonitingimused (order process, 4–8 week lead time, changes)
 *   - Materjalide päritolu (FSC/PEFC, oak/ash/birch/pine, EU hardware)
 *   - Homepage process + consultation copy (48 h response, workshop visits)
 * ========================================================================= */

$items = array(
	array(
		'order' => 1,
		'et'    => array(
			'q' => 'Kuidas toimib eritellimusel mööbli tellimine?',
			'a' => 'Iga projekt algab briefist ja ruumiga tutvumisest, millele järgneb kontseptsioon ja pakkumine koos materjalivaliku, hinna ja tähtajaga. Pärast tellimuse kirjalikku kinnitamist koos ettemaksuga valmistame mööbli oma töökojas Tallinnas. Viimane samm on paigaldus ja üleandmine koos hooldusjuhendiga.',
		),
		'ru'    => array(
			'q' => 'Как проходит заказ мебели на заказ?',
			'a' => 'Каждый проект начинается с брифа и знакомства с помещением, затем следуют концепция и предложение с выбором материалов, ценой и сроком. После письменного подтверждения заказа с предоплатой мы изготавливаем мебель в собственной мастерской в Таллине. Последний этап — монтаж и сдача вместе с инструкцией по уходу.',
		),
		'en'    => array(
			'q' => 'How does ordering custom furniture work?',
			'a' => 'Every project starts with a brief and a look at the space, followed by a concept and an offer with material choices, price and lead time. After the order is confirmed in writing with a deposit, we build the furniture in our own workshop in Tallinn. The final step is installation and handover, including care instructions.',
		),
	),
	array(
		'order' => 2,
		'et'    => array(
			'q' => 'Kui kaua võtab eritellimuse valmistamine aega?',
			'a' => 'Tüüpiline valmimisaeg on 4–8 nädalat alates tellimuse kirjalikust kinnitamisest, ettemakse laekumisest ja materjalivaliku heakskiitmisest. Tähtaegu võivad mõjutada materjalide kättesaadavus ja kliendipoolse tagasiside tempo — hoiame teid edenemisest kogu projekti vältel kursi. Keerukamad lahendused ja erivajadused kokkuleppel.',
		),
		'ru'    => array(
			'q' => 'Сколько времени занимает изготовление заказа?',
			'a' => 'Обычный срок изготовления — 4–8 недель с момента письменного подтверждения заказа, поступления предоплаты и утверждения выбора материалов. На сроки могут влиять доступность материалов и скорость обратной связи со стороны клиента — мы держим вас в курсе на протяжении всего проекта. Более сложные решения и особые требования — по договорённости.',
		),
		'en'    => array(
			'q' => 'How long does a custom order take?',
			'a' => 'A typical lead time is 4–8 weeks from written order confirmation, receipt of the deposit and approval of the material selection. Lead times may be affected by material availability and the pace of client feedback — we keep you posted throughout the project. More complex solutions and special requirements are agreed individually.',
		),
	),
	array(
		'order' => 3,
		'et'    => array(
			'q' => 'Mis materjalidest te mööblit valmistate?',
			'a' => 'Kasutame tahaspuitu ja plaadimaterjale vastutustundlikult majandatud metsadest — võimalusel FSC- või PEFC-sertifitseeritud allikatest. Meie põhimaterjalid on Põhja- ja Baltimaade tamm, saar, kask ja mänd; hinged, suuliskapid ja tõmme pärinevad tunnustatud Euroopa tootjatelt. Materjalide päritolu kohta loe lähemalt lehelt „Materjalide päritolu“.',
		),
		'ru'    => array(
			'q' => 'Из каких материалов вы изготавливаете мебель?',
			'a' => 'Мы используем массив дерева и плитные материалы из лесов с ответственным хозяйствованием — по возможности из FSC- или PEFC-сертифицированных источников. Наши основные породы — северный и балтийский дуб, ясень, берёза и сосна; петли, направляющие и ручки поставляют признанные европейские производители. Подробнее о происхождении материалов — на странице «Происхождение материалов».',
		),
		'en'    => array(
			'q' => 'What materials do you build with?',
			'a' => 'We use solid wood and panel materials from responsibly managed forests — FSC- or PEFC-certified sources wherever possible. Our core species are Nordic and Baltic oak, ash, birch and pine, and hinges, drawer systems and pulls come from established European manufacturers. You can read more on the Material Provenance page.',
		),
	),
	array(
		'order' => 4,
		'et'    => array(
			'q' => 'Kas saate töötada meie arhitekti jooniste järgi?',
			'a' => 'Jah — valmistame mööbli teie jooniste järgi või koostöös teie arhitektiga. Töötame teie siseviimistluse graafiku järgi, koordineerime teie arhitekti ja töövõtjatega ning paigaldame teie avamiskuupäeva ümber. Kuna kogu tootmine käib meie enda töökojas, saabuvad osad viimistletuna ja paigaldusvalmisena.',
		),
		'ru'    => array(
			'q' => 'Можете ли вы работать по чертежам нашего архитектора?',
			'a' => 'Да — мы изготавливаем мебель по вашим чертежам или в сотрудничестве с вашим архитектором. Мы работаем по вашему графику отделки, координируем действия с архитектором и подрядчиками и монтируем с учётом вашей даты открытия. Поскольку всё производство ведётся в нашей собственной мастерской, детали приходят отделанными и готовыми к монтажу.',
		),
		'en'    => array(
			'q' => 'Can you work from our architect\'s drawings?',
			'a' => 'Yes — we build from your drawings or in cooperation with your architect. We work to your fit-out programme, coordinate with your architect and contractors, and install around your opening date. Because everything is produced in our own workshop, parts arrive finished and ready to install.',
		),
	),
	array(
		'order' => 5,
		'et'    => array(
			'q' => 'Kellele te mööblit valmistate?',
			'a' => 'Valmistame mööblit ja sisustuslahendusi kohvikutele, baaridele ja restoranidele ning jaekauplustele ja kaubanduslikele interjöörile. Lisaks teeme erilahendusega erakööke. Kõik esemed on kavandatud pika ja pideva kasutuse jaoks.',
		),
		'ru'    => array(
			'q' => 'Для кого вы изготавливаете мебель?',
			'a' => 'Мы изготавливаем мебель и интерьерные решения для кофеен, баров и ресторанов, а также для магазинов и торговых интерьеров. Кроме того, мы делаем частные кухни по индивидуальным решениям. Все изделия рассчитаны на долгую и интенсивную эксплуатацию.',
		),
		'en'    => array(
			'q' => 'Who do you build furniture for?',
			'a' => 'We make furniture and interior solutions for cafés, bars and restaurants, as well as retail stores and commercial interiors. We also build bespoke private kitchens. Every piece is designed for long hours of constant use.',
		),
	),
	array(
		'order' => 6,
		'et'    => array(
			'q' => 'Kuidas päringut esitada ja kui kiiresti vastate?',
			'a' => 'Saadake meile oma plaanid, joonised või kontseptsioonitahvlid koos mõõtude ja materjalisoovidega — päringuvorm on meie veebilehel. Vaatame teie briefi üle ja vastame esialgse tagasisidega 48 tunni jooksul. Töökoja ja näidiste külastus Tallinnas on võimalik kokkuleppel.',
		),
		'ru'    => array(
			'q' => 'Как отправить запрос и как быстро вы отвечаете?',
			'a' => 'Пришлите нам свои планы, чертежи или концепт-борды с размерами и пожеланиями по материалам — форма запроса есть на нашем сайте. Мы рассмотрим ваш бриф и ответим с первичной обратной связью в течение 48 часов. Посещение мастерской и знакомство с образцами в Таллине возможно по предварительной договорённости.',
		),
		'en'    => array(
			'q' => 'How do I send an enquiry and how quickly do you respond?',
			'a' => 'Send us your floor plans, drawings or concept boards with dimensions and material preferences — there is an enquiry form on our website. We will review your brief and respond with initial feedback within 48 hours. Workshop and sample visits in Tallinn are possible by appointment.',
		),
	),
);

/* =========================================================================
 * Create missing items per language and link the translation groups.
 * ========================================================================= */

$created = 0;
$skipped = 0;

foreach ( $items as $item ) {
	$group = array(
		'et' => 0,
		'ru' => 0,
		'en' => 0,
	);

	// The Estonian item anchors the group: skip when it already exists.
	$et_id = mp_faq_find_by_et_title( $item['et']['q'] );
	if ( $et_id ) {
		$skipped++;
		echo "skipped  (exists) et {$item['et']['q']}\n";
	}

	foreach ( array( 'et', 'ru', 'en' ) as $lang ) {
		if ( 'et' === $lang && $et_id ) {
			$group[ $lang ] = $et_id;
			continue;
		}

		// Reuse an existing translation when one is already linked.
		if ( $et_id && function_exists( 'pll_get_post' ) ) {
			$existing = (int) pll_get_post( $et_id, $lang );
			if ( $existing && get_post_type( $existing ) === 'mooblipoisid_faq' ) {
				$group[ $lang ] = $existing;
				continue;
			}
		}

		$group[ $lang ] = mp_faq_create( $item[ $lang ]['q'], $item[ $lang ]['a'], $lang, $item['order'] );
		$created++;
	}

	pll_save_post_translations( $group );
	echo "linked translations: " . wp_json_encode( pll_get_post_translations( $group['et'] ) ) . "\n";
}

echo "done: {$created} created, {$skipped} skipped (already existed).\n";

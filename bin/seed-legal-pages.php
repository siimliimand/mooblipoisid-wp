<?php
/**
 * Seed the legal/information pages in all three languages and point the
 * footer legal bar at them.
 *
 * Pages (translation groups):
 *   - Terms of Commission:  Komisjonitingimused / Условия заказа / Terms of Commission
 *   - Material Provenance:  Materjalide päritolu / Происхождение материалов / Material Provenance
 *   - Privacy Policy:       Privaatsuspoliitika / Политика конфиденциальности / Privacy Policy
 *
 * Idempotent: re-running updates content and re-links translations instead
 * of creating duplicates. Editors edit the pages afterwards in wp-admin;
 * do not re-run the seed after manual edits unless overwriting is intended.
 *
 * Usage: ddev wp eval-file bin/seed-legal-pages.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'init' );

if ( ! function_exists( 'pll_set_post_language' ) ) {
	echo "Polylang not active, aborting.\n";
	exit( 1 );
}

/**
 * Gutenberg helpers.
 */
function mp_legal_p( $text ) {
	return "<!-- wp:paragraph --><p>{$text}</p><!-- /wp:paragraph -->";
}
function mp_legal_h( $text ) {
	return "<!-- wp:heading --><h2 class=\"wp-block-heading\">{$text}</h2><!-- /wp:heading -->";
}
function mp_legal_ul( array $items ) {
	$inner = '';
	foreach ( $items as $item ) {
		$inner .= "<!-- wp:list-item --><li>{$item}</li><!-- /wp:list-item -->";
	}
	return "<!-- wp:list --><ul class=\"wp-block-list\">{$inner}</ul><!-- /wp:list -->";
}
function mp_legal_ol( array $items ) {
	$inner = '';
	foreach ( $items as $item ) {
		$inner .= "<!-- wp:list-item --><li>{$item}</li><!-- /wp:list-item -->";
	}
	return "<!-- wp:list {\"ordered\":true} --><ol class=\"wp-block-list\">{$inner}</ol><!-- /wp:list -->";
}

/**
 * Create or update one page and return its ID.
 */
function mp_legal_upsert( $slugs, $lang, $title, $content ) {
	$post_id = 0;
	foreach ( (array) $slugs as $slug ) {
		$found = get_page_by_path( $slug );
		if ( $found ) {
			$post_id = $found->ID;
			break;
		}
	}

	if ( $post_id ) {
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_title'   => $title,
				'post_name'    => $slugs[0],
				'post_content' => $content,
				'post_status'  => 'publish',
			)
		);
		echo "updated  #{$post_id} {$lang} {$title}\n";
	} else {
		$post_id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slugs[0],
				'post_content' => $content,
				'post_status'  => 'publish',
				'post_type'    => 'page',
			)
		);
		echo "created  #{$post_id} {$lang} {$title}\n";
	}

	pll_set_post_language( $post_id, $lang );
	return $post_id;
}

/* =========================================================================
 * 1. Terms of Commission — Komisjonitingimused / Условия заказа / Terms
 * ========================================================================= */

$terms_et = implode(
	"\n",
	array(
		mp_legal_p( 'Need tingimused kehtivad Mööblipoisid eritellimusel valmistatavate mööbliesemete ja sisustuslahenduste kohta. Tellimuse kirjalik kinnitamine (allkiri või e-kiri) tähendab nõustumist käesolevate tingimustega. Erapoolsetele klientidele kehtivad lisaks võlalepingu seaduse (VÕS) tarbijakaitsesätted.' ),
		mp_legal_h( 'Tellimuse käik' ),
		mp_legal_p( 'Iga projekt algab vestlusest ja ruumiga tutvumisest. Protsess on läbipaistev ja jagatud selgeteks sammudeks:' ),
		mp_legal_ol(
			array(
				'Brief ja mõõdistamine — kaardistame vajadused, mõõdud ja ruumi eripärad.',
				'Kontseptsioon ja pakkumine — esitame visuaalse lahenduse, materjalivaliku, hinna ja tähtaja.',
				'Kinnitamine — tellimus kinnitatakse kirjalikult koos ettemaksuga.',
				'Tootmine — valmistame mööbli oma töökojas.',
				'Paigaldus ja üleandmine — transpordime, paigaldame ja anname kasutusjuhised üle.',
			)
		),
		mp_legal_h( 'Hind ja maksmine' ),
		mp_legal_p( 'Kõik hinnad on eurodes ja sisaldavad käibemaksu, kui pakkumises pole märgitud teisiti. Tavapärane maksegraafik on 50% ettemaks tellimuse kinnitamisel ja 50% enne paigaldust või üleandmist. Kui pakkumises on kirjas, võib arveldus toimuda etappide kaupa.' ),
		mp_legal_h( 'Tähtajad' ),
		mp_legal_p( 'Tüüpiline valmimisaeg on 4–8 nädalat alates tellimuse kirjalikust kinnitamisest, ettemakse laekumisest ja materjalivaliku heakskiitmisest. Erivajadused ja keerukamad lahendused kokkuleppel. Tähtaegu võivad mõjutada materjalide kättesaadavus ja kliendipoolse tagasiside tempo — hoiame teid edenemisest kogu aeg kursi.' ),
		mp_legal_h( 'Muudatused ja tellimuse tühistamine' ),
		mp_legal_p( 'Kõik tellimuse muudatused esitage kirjalikult. Muudatuse mõju hinnale ja tähtajale kinnitame enne rakendamist. Kuna esemed valmistatakse eritellimusel, ei ole tellimust pärast tootmise algust võimalik kulutaseta tühistada: esitatavad kulud hõlmavad tehtud tööd ja spetsiaalselt tellitud materjale.' ),
		mp_legal_h( 'Garantii' ),
		mp_legal_p( 'Anname mööbliesemetele 2-aastase garantii tootmis- ja paigaldusvigade eest vastavalt võlalepingu seadusele. Tahke puidu looduslikud omadused — toonivariatsioonid, aastarõngaste mustrid ja loomulik voolimine niiskusrežiimi muutudes — ei ole vead.' ),
		mp_legal_p( 'Garantii ei kehti, kui eset on kasutatud vastupidiselt kasutusjuhendile, hooldatud ebakorrektselt või kahjustatud mehaaniliselt või transportimisel pärast üleandmist.' ),
		mp_legal_h( 'Hooldus' ),
		mp_legal_p( 'Iga mööbliese kaasas hooldusjuhend. Õige hooldus pikendab mööbli eluiga ja säilitab garantii kehtivuse.' ),
		mp_legal_h( 'Kohaldatav õigus' ),
		mp_legal_p( 'Lepingu suhtel kehtib Eesti Vabariigi õigus. Vaidlused lahendatakse esmalt läbirääkimiste teel; kokkuleppe puudumisel Harju Maakohtus, tarbijate puhul vastavalt võlalepingu seadusele pädevas kohtus.' ),
	)
);

$terms_ru = implode(
	"\n",
	array(
		mp_legal_p( 'Настоящие условия распространяются на мебель и решения для интерьера, изготавливаемые Mööblipoisid на заказ. Письменное подтверждение заказа (подпись или электронное письмо) означает согласие с этими условиями. Для потребителей дополнительно применяются положения Закона Эстонии об обязательственном праве (VÕS) о защите прав потребителей.' ),
		mp_legal_h( 'Порядок заказа' ),
		mp_legal_p( 'Каждый проект начинается с обсуждения и знакомства с помещением. Процесс прозрачен и разделён на понятные этапы:' ),
		mp_legal_ol(
			array(
				'Бриф и обмеры — фиксируем потребности, размеры и особенности помещения.',
				'Концепция и предложение — представляем визуальное решение, выбор материалов, цену и срок.',
				'Подтверждение — заказ подтверждается письменно с предоплатой.',
				'Изготовление — производим мебель в собственной мастерской.',
				'Монтаж и сдача — доставляем, устанавливаем и передаём инструкции по уходу.',
			)
		),
		mp_legal_h( 'Цена и оплата' ),
		mp_legal_p( 'Все цены указаны в евро и включают НДС, если в предложении не указано иное. Стандартный график оплаты: 50% предоплата при подтверждении заказа и 50% до монтажа или передачи. Если это предусмотрено предложением, выставление счетов может происходить по этапам.' ),
		mp_legal_h( 'Сроки' ),
		mp_legal_p( 'Обычный срок изготовления — 4–8 недель с момента письменного подтверждения заказа, поступления предоплаты и утверждения выбора материалов. Особые требования и более сложные решения — по договорённости. На сроки могут влиять доступность материалов и скорость обратной связи со стороны клиента — мы держим вас в курсе на протяжении всего проекта.' ),
		mp_legal_h( 'Изменения и отмена заказа' ),
		mp_legal_p( 'Все изменения заказа направляйте письменно. Влияние изменения на цену и срок мы подтверждаем до его реализации. Поскольку изделия изготавливаются на заказ, после начала производства отмена невозможна без возмещения затрат: подлежат оплате выполненные работы и специально заказанные для проекта материалы.' ),
		mp_legal_h( 'Гарантия' ),
		mp_legal_p( 'Мы предоставляем 2 года гарантии на изготовленную мебель в части производственных и монтажных дефектов в соответствии с Законом об обязательственном праве. Естественные свойства массива дерева — вариации оттенка, рисунок годовых колец и естественная деформация при изменении влажности — не являются дефектами.' ),
		mp_legal_p( 'Гарантия не действует, если изделие использовалось вопреки инструкции, обслуживалось ненадлежащим образом или повреждено механически либо при транспортировке после передачи.' ),
		mp_legal_h( 'Уход' ),
		mp_legal_p( 'К каждому изделию прилагается инструкция по уходу. Правильный уход продлевает срок службы мебели и сохраняет действие гарантии.' ),
		mp_legal_h( 'Применимое право' ),
		mp_legal_p( 'К договору применяется право Эстонской Республики. Споры решаются прежде всего путём переговоров; при отсутствии согласия — в суде Харьюмаа (Harju Maakohus), для потребителей — в компетентном суде в соответствии с Законом об обязательственном праве.' ),
	)
);

$terms_en = implode(
	"\n",
	array(
		mp_legal_p( 'These terms apply to furniture and interior solutions made to order by Mööblipoisid. Written confirmation of an order (signature or e-mail) means acceptance of these terms. For consumers, the consumer protection provisions of the Estonian Law of Obligations Act (VÕS) also apply.' ),
		mp_legal_h( 'How an order works' ),
		mp_legal_p( 'Every project starts with a conversation and a look at the space. The process is transparent and split into clear steps:' ),
		mp_legal_ol(
			array(
				'Brief and site measure — we map out needs, dimensions and the particularities of the space.',
				'Concept and offer — we present a visual concept, material choices, price and lead time.',
				'Confirmation — the order is confirmed in writing together with the deposit.',
				'Production — we build the furniture in our own workshop.',
				'Installation and handover — we transport, install and hand over care instructions.',
			)
		),
		mp_legal_h( 'Price and payment' ),
		mp_legal_p( 'All prices are in euros and include VAT unless the offer states otherwise. The standard payment schedule is 50% deposit upon order confirmation and 50% before installation or handover. Where stated in the offer, invoicing may be split per project stage.' ),
		mp_legal_h( 'Lead times' ),
		mp_legal_p( 'A typical lead time is 4–8 weeks from written order confirmation, receipt of the deposit and approval of the material selection. Special requirements and more complex solutions are agreed individually. Lead times may be affected by material availability and the pace of client feedback — we keep you posted throughout the project.' ),
		mp_legal_h( 'Changes and cancellation' ),
		mp_legal_p( 'Please submit all order changes in writing. We confirm the effect of a change on price and lead time before implementing it. Because every piece is made to order, an order cannot be cancelled after production has started without covering the costs: work already performed and materials ordered specifically for the project.' ),
		mp_legal_h( 'Warranty' ),
		mp_legal_p( 'We provide a 2-year warranty on our furniture against manufacturing and installation defects under the Estonian Law of Obligations Act. Natural characteristics of solid wood — tone variation, grain patterns and natural movement as humidity changes — are not defects.' ),
		mp_legal_p( 'The warranty does not cover use contrary to the instructions, improper care, or mechanical damage or damage during transport after handover.' ),
		mp_legal_h( 'Care' ),
		mp_legal_p( 'Care instructions are provided with every piece. Proper care extends the life of the furniture and keeps the warranty valid.' ),
		mp_legal_h( 'Governing law' ),
		mp_legal_p( 'The law of the Republic of Estonia applies to the contract. Disputes are resolved primarily by negotiation; failing agreement, in Harju County Court, and for consumers in the court competent under the Law of Obligations Act.' ),
	)
);

/* =========================================================================
 * 2. Material Provenance — Materjalide päritolu / Происхождение материалов
 * ========================================================================= */

$materials_et = implode(
	"\n",
	array(
		mp_legal_p( 'Iga mööbliese algab materjalist. Valime materjalid vastutustundlikult ja jälgitavalt, eelistades Euroopa päritolu ning pika elueaga lahendusi.' ),
		mp_legal_h( 'Puit ja plaadimaterjalid' ),
		mp_legal_p( 'Kasutame tahaspuitu ja plaadimaterjale, mis pärinevad vastutustundlikult majandatud metsadest — võimalusel FSC- või PEFC-sertifitseeritud allikatest. Meie põhimaterjalid on Põhja- ja Baltimaade tamm, saar, kask ja mänd. Plaadid (sh vineer ja MDF) hankime Euroopa tootjatelt madala formaldehüüdi emissiooniga (E1 klass).' ),
		mp_legal_h( 'Viimistlus ja liimid' ),
		mp_legal_p( 'Õlid, vahad, lakid ja liimid pärinevad Euroopa tootjatelt ja vastavad EL-i keemiaõiguse (REACH, CLP) nõuetele. Eelistame madala lenduvate orgaaniliste ühendite (LOÜ) sisaldusega viimistluslahendusi, et hoida siseruumide õhkkond puhas.' ),
		mp_legal_h( 'Tarvikud' ),
		mp_legal_p( 'Hinged, suuliskapid, tõmme ja valgustus pärinevad tunnustatud Euroopa tootjatelt. Nii tagame, et varuosad on kättesaadavad ka aastate pärast ja mööbel on hooldatav mitu kümnendit.' ),
		mp_legal_h( 'Kohalik eelistamine' ),
		mp_legal_p( 'Ostame eelistatult Eesti ja Balti riikide tarnijatelt. Lühemad transporditeed tähendavad väiksemat keskkonnajalajälge ja toetavad kohalikku tööstust.' ),
		mp_legal_h( 'Jälgitavus' ),
		mp_legal_p( 'Säilitame tarnijate dokumentatsiooni ja jagame materjalide päritolu kohta teavet meeleldi. Kui teie projektil on erinõuded materjalidele, rääkige sellest juba briefi käigus.' ),
		mp_legal_h( 'Vastupidavus ja eluiga' ),
		mp_legal_p( 'Valime materjalid mitte ainult välimuse, vaid ka kasutuse põhjal: korralikult hooldatud tahaspuitmööbel kestab aastakümneid ja on remonditav, mitte asendatav.' ),
	)
);

$materials_ru = implode(
	"\n",
	array(
		mp_legal_p( 'Каждое изделие начинается с материала. Мы выбираем материалы ответственно и с прослеживаемостью, отдавая предпочтение европейскому происхождению и долговечным решениям.' ),
		mp_legal_h( 'Древесина и плитные материалы' ),
		mp_legal_p( 'Мы используем массив дерева и плитные материалы из лесов с ответственным хозяйствованием — по возможности из FSC- или PEFC-сертифицированных источников. Наши основные породы — северный и балтийский дуб, ясень, берёза и сосна. Плиты (включая фанеру и MDF) закупаем у европейских производителей с низким уровнем выделения формальдегида (класс E1).' ),
		mp_legal_h( 'Отделка и клеи' ),
		mp_legal_p( 'Масла, воски, лаки и клеи поставляют европейские производители; они соответствуют требованиям химического законодательства ЕС (REACH, CLP). Мы предпочитаем отделочные материалы с низким содержанием летучих органических соединений (ЛОС), чтобы воздух в помещениях оставался чистым.' ),
		mp_legal_h( 'Фурнитура' ),
		mp_legal_p( 'Петли, направляющие, ручки и освещение — от признанных европейских производителей. Так мы гарантируем, что запчасти будут доступны и через годы, а мебель можно обслуживать десятилетиями.' ),
		mp_legal_h( 'Локальные поставщики' ),
		mp_legal_p( 'Мы закупаем преимущественно у поставщиков из Эстонии и стран Балтии. Более короткие транспортные пути означают меньший экологический след и поддержку местной промышленности.' ),
		mp_legal_h( 'Прослеживаемость' ),
		mp_legal_p( 'Мы храним документацию поставщиков и охотно делимся информацией о происхождении материалов. Если у вашего проекта есть особые требования к материалам, обсудите их уже на этапе брифа.' ),
		mp_legal_h( 'Долговечность' ),
		mp_legal_p( 'Мы выбираем материалы не только по внешнему виду, но и по характеру эксплуатации: правильно обслуживаемая мебель из массива служит десятилетиями и подлежит ремонту, а не замене.' ),
	)
);

$materials_en = implode(
	"\n",
	array(
		mp_legal_p( 'Every piece of furniture starts with its materials. We choose them responsibly and traceably, favouring European sources and long-lived solutions.' ),
		mp_legal_h( 'Wood and panels' ),
		mp_legal_p( 'We use solid wood and panel materials from responsibly managed forests — FSC- or PEFC-certified sources wherever possible. Our core species are Nordic and Baltic oak, ash, birch and pine. Panels (including plywood and MDF) come from European manufacturers with low formaldehyde emissions (E1 class).' ),
		mp_legal_h( 'Finishes and adhesives' ),
		mp_legal_p( 'Oils, waxes, lacquers and adhesives come from European manufacturers and comply with EU chemicals legislation (REACH, CLP). We favour finishes with a low content of volatile organic compounds (VOCs) to keep indoor air clean.' ),
		mp_legal_h( 'Hardware' ),
		mp_legal_p( 'Hinges, drawer systems, pulls and lighting come from established European manufacturers. That way spare parts remain available years later and the furniture can be maintained for decades.' ),
		mp_legal_h( 'Local sourcing' ),
		mp_legal_p( 'We buy primarily from suppliers in Estonia and the Baltic states. Shorter transport routes mean a smaller environmental footprint and support for local industry.' ),
		mp_legal_h( 'Traceability' ),
		mp_legal_p( 'We keep supplier documentation and are happy to share information about the provenance of our materials. If your project has special material requirements, raise them during the brief.' ),
		mp_legal_h( 'Durability and lifespan' ),
		mp_legal_p( 'We choose materials not only for their looks but for how they are used: well-maintained solid wood furniture lasts for decades and can be repaired rather than replaced.' ),
	)
);

/* =========================================================================
 * 3. Privacy Policy — Privaatsuspoliitika / Политика конфиденциальности
 * ========================================================================= */

$privacy_et = implode(
	"\n",
	array(
		mp_legal_p( 'Käesolev privaatsuspoliitika kirjeldab, kuidas Mööblipoisid töötleb isikuandmeid saidi mooblipoisid.ee külastajate ja klientide kohta vastavalt Euroopa Parlamendi ja Nõukogu määrusele (EL) 2016/679 (GDPR) ja Eesti isikuandmete kaitse seadusele.' ),
		mp_legal_h( 'Andmetöötleja' ),
		mp_legal_p( 'Andmetöötleja on Mööblipoisid (edaspidi „meie”). Kontakt: marilyn@mooblipoisid.ee.' ),
		mp_legal_h( 'Milliseid andmeid kogume' ),
		mp_legal_ul(
			array(
				'Päringuvormist: nimi, ettevõte, e-posti aadress, telefon (valikuline), projektitüüp, objekti asukoht, projekti kirjeldus ja teie üleslaaditud failid (nt PDF, DWG, PNG, JPG).',
				'Tehnilised andmed: serveri logid saidi toimimise ja turvalisuse tagamiseks.',
				'Me ei kasuta reklaamijälgimist ega müü andmeid.',
			)
		),
		mp_legal_h( 'Eesmärk ja õiguslik alus' ),
		mp_legal_ul(
			array(
				'Teie päringute vastuvõtmine, vastamine ja pakkumiste koostamine — lepingueelsed meetmed või õigustatud huvi.',
				'Sõlmitud lepingu täitmine (tellimus, tootmine, paigaldus, arveldus) — leping.',
				'Raamatupidamiskohustuste täitmine — õiguslik kohustus.',
			)
		),
		mp_legal_h( 'Säilitamine' ),
		mp_legal_ul(
			array(
				'Päringud, mis ei vii lepinguni: kuni 2 aastat pärast viimast kontakti.',
				'Lepingupõhised andmed: kuni 7 aastat vastavalt raamatupidamise seadusandlusele.',
			)
		),
		mp_legal_h( 'Edastamine' ),
		mp_legal_p( 'Me ei edasta isikuandmeid kolmandatesse riikidesse ega müü neid. Andmeid võidakse töödelda meie volitatud teenusepakkujate poolt (nt veebimajutus ja e-posti teenus), kes tegutsevad EL/EMP piires ja meie juhiste kohaselt.' ),
		mp_legal_h( 'Teie õigused' ),
		mp_legal_p( 'Teil on õigus nõuda oma isikuandmetele ligipääsu, nende parandamist, kustutamist, töötlemise piiramist, ülekandmist ning töötlemisele vastuväite esitamist. Saatke palun taotlus aadressile marilyn@mooblipoisid.ee — vastame 30 päeva jooksul. Kui leiate, et teie õigusi on riivetud, saate kaebuse esitada Andmekaitse Inspektsioonile (www.aki.ee).' ),
		mp_legal_h( 'Turvalisus' ),
		mp_legal_p( 'Kaitseme isikuandmeid sobivate tehniliste ja organisatooriliste meetmetega. Päringuvormist saadud andmeid ja faile kasutatakse ainult teie projekti eesmärgil.' ),
		mp_legal_h( 'Muudatused' ),
		mp_legal_p( 'Võime käesolevat poliitikat uuendada. Kehtiv versioon on alati sellel lehel.' ),
	)
);

$privacy_ru = implode(
	"\n",
	array(
		mp_legal_p( 'Настоящая политика конфиденциальности описывает, как Mööblipoisid обрабатывает персональные данные посетителей сайта mooblipoisid.ee и клиентов в соответствии с Регламентом (ЕС) 2016/679 (GDPR) и Законом Эстонии о защите персональных данных.' ),
		mp_legal_h( 'Контролёр данных' ),
		mp_legal_p( 'Контролёром данных является Mööblipoisid (далее — «мы»). Контакт: marilyn@mooblipoisid.ee.' ),
		mp_legal_h( 'Какие данные мы собираем' ),
		mp_legal_ul(
			array(
				'Из формы запроса: имя, компания, адрес электронной почты, телефон (необязательно), тип проекта, местоположение объекта, описание проекта и загруженные вами файлы (например, PDF, DWG, PNG, JPG).',
				'Технические данные: журналы сервера для обеспечения работы и безопасности сайта.',
				'Мы не используем рекламные трекеры и не продаём данные.',
			)
		),
		mp_legal_h( 'Цели и правовые основания' ),
		mp_legal_ul(
			array(
				'Получение и рассмотрение ваших запросов, подготовка предложений — преддоговорные меры или законный интерес.',
				'Исполнение заключённого договора (заказ, производство, монтаж, выставление счетов) — договор.',
				'Выполнение бухгалтерских обязательств — юридическая обязанность.',
			)
		),
		mp_legal_h( 'Хранение' ),
		mp_legal_ul(
			array(
				'Запросы, не приведшие к договору: до 2 лет после последнего контакта.',
				'Данные по договору: до 7 лет в соответствии с законодательством о бухгалтерском учёте.',
			)
		),
		mp_legal_h( 'Передача данных' ),
		mp_legal_p( 'Мы не передаём персональные данные в третьи страны и не продаём их. Данные могут обрабатываться нашими уполномоченными поставщиками услуг (например, хостинг и электронная почта), которые действуют в пределах ЕС/ЕЭЗ и по нашим инструкциям.' ),
		mp_legal_h( 'Ваши права' ),
		mp_legal_p( 'Вы имеете право требовать доступ к своим персональным данным, их исправление, удаление, ограничение обработки, переносимость, а также возражать против обработки. Направьте запрос на marilyn@mooblipoisid.ee — мы ответим в течение 30 дней. Если вы считаете, что ваши права нарушены, вы можете подать жалобу в Инспекцию защиты данных (www.aki.ee).' ),
		mp_legal_h( 'Безопасность' ),
		mp_legal_p( 'Мы защищаем персональные данные соответствующими техническими и организационными мерами. Данные и файлы, полученные через форму запроса, используются только в целях вашего проекта.' ),
		mp_legal_h( 'Изменения' ),
		mp_legal_p( 'Мы можем обновлять настоящую политику. Актуальная версия всегда доступна на этой странице.' ),
	)
);

$privacy_en = implode(
	"\n",
	array(
		mp_legal_p( 'This privacy policy describes how Mööblipoisid processes personal data of visitors to the mooblipoisid.ee website and our clients in accordance with Regulation (EU) 2016/679 (GDPR) and the Estonian Personal Data Protection Act.' ),
		mp_legal_h( 'Data controller' ),
		mp_legal_p( 'The data controller is Mööblipoisid (“we”). Contact: marilyn@mooblipoisid.ee.' ),
		mp_legal_h( 'What data we collect' ),
		mp_legal_ul(
			array(
				'From the enquiry form: name, company, e-mail address, phone (optional), project type, site location, project description and the files you upload (e.g. PDF, DWG, PNG, JPG).',
				'Technical data: server logs to keep the site running and secure.',
				'We use no advertising trackers and do not sell data.',
			)
		),
		mp_legal_h( 'Purposes and legal basis' ),
		mp_legal_ul(
			array(
				'Receiving and responding to your enquiries and preparing offers — pre-contractual measures or legitimate interest.',
				'Performing an entered contract (order, production, installation, invoicing) — contract.',
				'Meeting accounting obligations — legal obligation.',
			)
		),
		mp_legal_h( 'Retention' ),
		mp_legal_ul(
			array(
				'Enquiries that do not lead to a contract: up to 2 years after the last contact.',
				'Contract-related data: up to 7 years under Estonian accounting legislation.',
			)
		),
		mp_legal_h( 'Sharing' ),
		mp_legal_p( 'We do not transfer personal data to third countries and do not sell it. Data may be processed by our authorised service providers (such as web hosting and e-mail), who operate within the EU/EEA and on our instructions.' ),
		mp_legal_h( 'Your rights' ),
		mp_legal_p( 'You may request access to your personal data and ask for their rectification, erasure, restriction of processing and portability, and object to processing. Please send your request to marilyn@mooblipoisid.ee — we will respond within 30 days. If you believe your rights have been violated, you may lodge a complaint with the Estonian Data Protection Inspectorate (www.aki.ee).' ),
		mp_legal_h( 'Security' ),
		mp_legal_p( 'We protect personal data with appropriate technical and organisational measures. Data and files received through the enquiry form are used only for the purposes of your project.' ),
		mp_legal_h( 'Changes' ),
		mp_legal_p( 'We may update this policy. The current version is always available on this page.' ),
	)
);

/* =========================================================================
 * Upsert pages + link translation groups.
 * ========================================================================= */

$groups = array(
	'terms'     => array(
		'et' => mp_legal_upsert( array( 'komisjonitingimused' ), 'et', 'Komisjonitingimused', $terms_et ),
		'ru' => mp_legal_upsert( array( 'usloviya-zakaza' ), 'ru', 'Условия заказа', $terms_ru ),
		'en' => mp_legal_upsert( array( 'terms-of-commission' ), 'en', 'Terms of Commission', $terms_en ),
	),
	'materials' => array(
		'et' => mp_legal_upsert( array( 'materjalide-paritolu' ), 'et', 'Materjalide päritolu', $materials_et ),
		'ru' => mp_legal_upsert( array( 'proiskhozhdenie-materialov' ), 'ru', 'Происхождение материалов', $materials_ru ),
		'en' => mp_legal_upsert( array( 'material-provenance' ), 'en', 'Material Provenance', $materials_en ),
	),
	'privacy'   => array(
		// Existing (previously empty) drafts: legacy slugs kept as lookup hints.
		'et' => mp_legal_upsert( array( 'privaatsuspoliitika', 'andmekaitsetingimused' ), 'et', 'Privaatsuspoliitika', $privacy_et ),
		'ru' => mp_legal_upsert( array( 'politika-konfidencialnosti', 'andmekaitsetingimused-ru' ), 'ru', 'Политика конфиденциальности', $privacy_ru ),
		'en' => mp_legal_upsert( array( 'privacy-policy', 'andmekaitsetingimused-en' ), 'en', 'Privacy Policy', $privacy_en ),
	),
);

foreach ( $groups as $group_key => $ids ) {
	pll_save_post_translations( $ids );
	echo "linked translations [{$group_key}]: " . wp_json_encode( pll_get_post_translations( $ids['et'] ) ) . "\n";
}

/* =========================================================================
 * Footer legal bar: point the three links at the Estonian pages (root-
 * relative paths). The child theme filter swaps in the current language's
 * permalink on the front end.
 * ========================================================================= */

$legal = get_theme_mod( 'footer_html_content', '' );
if ( '' !== $legal ) {
	$legal_links = array(
		'Komisjonitingimused'   => '/komisjonitingimused/',
		'Materjalide päritolu'  => '/materjalide-paritolu/',
		'Privaatsuspoliitika'   => '/privaatsuspoliitika/',
	);
	foreach ( $legal_links as $label => $path ) {
		$legal = preg_replace(
			'#<a href="[^"]*">' . preg_quote( $label, '#' ) . '</a>#u',
			'<a href="' . $path . '">' . $label . '</a>',
			$legal
		);
	}
	set_theme_mod( 'footer_html_content', $legal );
	echo "footer legal links now point at: " . wp_json_encode( array_values( $legal_links ) ) . "\n";
}

/* =========================================================================
 * Housekeeping: trash the WordPress sample page (its own content asks for
 * it) so it does not linger in wp-admin.
 * ========================================================================= */

$sample = get_page_by_path( 'demoleht' );
if ( $sample && 'publish' === $sample->post_status ) {
	wp_trash_post( $sample->ID );
	echo "trashed sample page #{$sample->ID} (demoleht)\n";
}

echo "done.\n";

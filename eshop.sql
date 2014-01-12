-- Adminer 3.5.1 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  `parent` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `category` (`id`, `name`, `slug`, `priority`, `parent`) VALUES
(1,	'Stáže',	'staze',	4,	NULL),
(2,	'Veletrhy',	'veletrhy',	3,	NULL),
(3,	'Další',	'dalsi',	0,	NULL),
(4,	'Průvodce prváka/Diář studenta',	'pruvodce-prvaka-diar-studenta',	2,	NULL),
(5,	'Partnerství',	'partnerstvi',	1,	NULL);

DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `name` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `ic` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `dic` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `total` double NOT NULL,
  `status` enum('new','complete','deleted') COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `order` (`id`, `created`, `name`, `ic`, `dic`, `total`, `status`) VALUES
(1,	'2013-10-16 13:50:07',	'Firma plzen 01',	'12312312',	'CZ12312312',	21900,	'deleted'),
(4,	'2013-10-27 12:30:49',	'Firma Plzen 03',	'ic123',	'dic123',	34400,	'deleted'),
(12,	'2013-11-09 19:28:32',	'Firma',	'ico',	'asdfi',	65400,	'deleted'),
(19,	'2013-12-02 11:23:21',	'firma_vscht_01',	'12345678',	'CZ12345678',	27000,	'new'),
(20,	'2013-12-19 09:44:12',	'BRUSH SEM s.r.o.',	'25745735',	'CZ25745735',	21900,	'complete'),
(21,	'2014-01-08 10:54:52',	'Tvoje Máma',	'123456789',	'CZ123456789',	60000,	'new'),
(22,	'2014-01-08 10:58:21',	'Tvoje Máma',	'1236548654',	'16546486464',	120000,	'new');

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `configuration` varchar(500) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `price`, `quantity`, `configuration`) VALUES
(1,	1,	1,	21900.00,	1,	NULL),
(5,	4,	1,	26400.00,	1,	'1###26400###price'),
(6,	4,	2,	8000.00,	1,	'7###8000###price'),
(15,	12,	4,	39000.00,	1,	'12###39000###price'),
(16,	12,	1,	26400.00,	1,	'1###26400###price'),
(45,	19,	11,	23000.00,	1,	'17###23000###price'),
(46,	19,	15,	4000.00,	1,	'40###4000###price'),
(47,	20,	1,	21900.00,	1,	'2###21900###price'),
(48,	21,	6,	60000.00,	1,	NULL),
(49,	22,	6,	60000.00,	2,	NULL);

DROP TABLE IF EXISTS `picture`;
CREATE TABLE `picture` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_czech_ci NOT NULL,
  `promo` tinyint(1) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `picture_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `picture` (`id`, `name`, `promo`, `product_id`) VALUES
(1,	'p1200877.JPG',	0,	1),
(3,	'dscn2307.JPG',	0,	3),
(4,	'kat.png',	1,	4),
(6,	'partner.png',	0,	7),
(7,	'partner1.png',	0,	6),
(8,	'kata1.png',	0,	4),
(9,	'katb.png',	0,	4),
(12,	'pp2013-obalka.jpg',	0,	2),
(14,	'ia-logo.png',	0,	5),
(16,	'sponzor.jpg',	0,	8),
(17,	'316-2013-06-03.jpg',	0,	12),
(18,	'284-2013-06-03.jpg',	1,	12),
(19,	'160-2013-06-03.jpg',	0,	12),
(20,	'262-2013-06-03.jpg',	0,	12),
(21,	'pp-2013-web-stranka-001.jpg',	0,	13),
(22,	'pp-2013-web-stranka-017.jpg',	0,	13),
(23,	'pp-2013-web-stranka-016.jpg',	0,	13),
(24,	'pp-2013-web-stranka-002.jpg',	0,	13),
(25,	'pp-2013-web-stranka-003.jpg',	0,	13),
(26,	'ik-cz.png',	0,	14),
(27,	'dsc-0104.JPG',	0,	16),
(28,	'dsc-0124.JPG',	0,	16),
(29,	'dsc-0126.JPG',	0,	16),
(30,	'dsc-0152.JPG',	0,	16),
(31,	'dsc-0170.JPG',	1,	16),
(34,	'ikariera-poster.png',	0,	20),
(39,	'vodni-strana.jpg',	0,	15),
(40,	'a5.jpg',	0,	15),
(41,	'a6.jpg',	0,	15),
(42,	'a8.jpg',	0,	15),
(43,	'003-2011-12-04.JPG',	0,	11),
(44,	'003-2012-21-02.JPG',	1,	11),
(46,	'imgp9548.JPG',	0,	11),
(47,	'imgp9589.JPG',	0,	11),
(48,	'imgp9609.JPG',	0,	11),
(49,	'imgp9666.JPG',	0,	11),
(50,	'imgp9651.JPG',	0,	11),
(56,	'ds-oboje.jpg',	0,	17),
(61,	'ds-amper.jpg',	0,	17),
(62,	'korektura-brno-vut-final-korektura-19.jpg',	0,	17),
(63,	'ds-vut-zmrzka-1.jpg',	0,	17),
(64,	'ds-vut-2.jpg',	0,	17),
(65,	'p1210029.JPG',	0,	1),
(66,	'p1210056.JPG',	0,	1),
(67,	'p1200908.JPG',	0,	1),
(68,	'p1210151.JPG',	1,	1),
(69,	'131126-1334.jpg',	0,	23),
(70,	'131126-1037-3.jpg',	0,	23),
(71,	'131126-1018-3.jpg',	0,	23),
(73,	'131127-1506-6.jpg',	0,	23),
(74,	'131127-1740.jpg',	0,	23),
(75,	'131127-1847-2.jpg',	0,	23),
(76,	'ssss.jpg',	0,	23),
(77,	'131126-1018-3.jpg',	0,	28),
(78,	'131126-1037-3.jpg',	0,	28),
(79,	'131126-1334.jpg',	0,	28),
(80,	'131127-1505.jpg',	0,	28),
(81,	'131127-1740.jpg',	0,	28),
(82,	'131127-1847.jpg',	0,	28),
(83,	'131127-1506.jpg',	0,	28),
(84,	'ssss.jpg',	0,	28),
(85,	'131127-1505.jpg',	0,	23),
(86,	'image00020.jpg',	0,	31),
(87,	'veletrh-lc-liberec-1.JPG',	0,	36),
(88,	'veletrh-lc-liberec-2.JPG',	0,	36);

DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_czech_ci NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  `active` enum('y','x','n') COLLATE utf8_czech_ci NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `product` (`id`, `name`, `description`, `slug`, `price`, `priority`, `active`, `category_id`) VALUES
(1,	'Veletrh pracovních příležitostí ZČU Plzeň 2014',	'Již 19. ročník největšího veletrhu na západě Čech, který se koná přímo v prostorách univerzitního kampusu ZČU v areálu na Borech. Očekávaná návštěvnost je **4 000** studentů z Fakulty elektrotechnické, strojní a aplikovaných věd a dále pak i dalších fakult ZČU. **Vstup zdarma** a každý návštěvník obdrží veletržní tašku a brožuru Průvodce veletrhem. ',	'veletrh-pracovnich-prilezitosti-zcu-plzen-2014',	21900.00,	20,	'y',	2),
(2,	'Průvodce prváka po ZČU 2014/2015',	'Tato 60 stránková brožura je ideálním způsobem, jak oslovit všech **5 000 studentů** z devíti fakult, kteří právě zahájili své studium na VŠ. Chcete nalákat studenta do právě Vaší restaurace, obchodu či sportovního zařízení? Stačí vložit pozvánku nebo rovnou slevový kupon a sami uvidíte, zda se Vám tato investice vyplatí.',	'pruvodce-prvaka-po-zcu-2014-2015',	4000.00,	-1,	'y',	4),
(3,	'Zahraniční stážista',	'Přijměte do Vaší společnosti praktikanta ze zahraničí, který splňuje Vámi **definované podmínky.** Posilte tak dočasně Váš tým, vykryjte chybějící kapacity v době dovolených či získejte potřebného odborníka na konkrétní projekt. Díky **akreditaci od MŠMT** nevzniká mezi Vámi a studentem pracovně právní vztah a není třeba platit zákonná pojištění. IAESTE Vám pouze fakturuje příspěvek na úhradu pobytový nákladů ve výši **od 10 000 Kč** měsíčně.',	'zahranicni-stazista',	0.00,	50,	'x',	1),
(4,	'Katalog iKariéra 2014',	'Díky nákladu **30 000 kusů** a dvacetileté historii je tento katalog největší tištěnou publikací zaměřenou na studenty a absolventy technických vysokých škol po celé republice.\n\nUzavírka tohoto proejktu je **31. ledna 2014.**',	'katalog-ikariera-2014',	19000.00,	10,	'y',	3),
(5,	'Generální partnerství',	'Partnerství s IAESTE České republiky přináší řadu výhod. **Generálním partnerem** se může stát pouze **jedna společnost** a cena se odvíjí od počtu zájemců. \n\n**Generální partnerství zahrnuje:**\n- vše co obsahuje balíček Plus, tedy i balíček Standard, \n- PR článek v Průvodcích prváka a Diáři studenta, \n- kreativní strany ve všech personalistických publikacích IAESTE, \n- rady studentům v úvodu Katalogu iKariéra,\n- úvodní slovo v Katalogu iKariéra, \n- možnost uspořádat průzkum mezi studenty, \n- čtvrtá strana obálky Katalogu iKariéra\n',	'generalni-partnerstvi',	150000.00,	-32,	'x',	5),
(6,	'Partnerství Plus',	'Partnerství s IAESTE České republiky přináší řadu výhod. V balíčku Plus naleznete:\n- vše co obsahuje **balíček Standard,**\n- firemní prezentace na veletrzích, \n- hromadný mail na ikariéra.cz na 2 tisíce adres, \n- PR článek a banner na iaeste.cz, \n- loga na tištěných materiálech a hlavičkovém papíře, \n- možnost účasti na prémiových projektech, \n- **rozšířená expozice na veletrzích zdarma (vlastní stánek, dvojitý stánek ...),** \n- účast na interních akcích IAESTE ČR\n- loga na webech iaeste.cz a ikariéra.cz',	'partnerstvi-plus',	60000.00,	-31,	'y',	5),
(7,	'Partnerství Standard',	'Partnerství s IAESTE České republiky přináší řadu výhod. V balíčku Standard naleznete:\n- **možnost přednostního výběru stánků na veletrzích,** \n- loga na materiálech k veletrhům, \n- přístup k CV na ikariéra.cz po jeden týden, \n- PR článek na ikariéra.cz, \n- možnost přednášek, workshopů a 2x hromadný mail na členy IAESTE, \n- rozšířená prezentace v el. verzi Katalogu iKariéra',	'partnerstvi-standard',	30000.00,	-30,	'y',	5),
(8,	'Sponzorství',	'Máte zájem sponzorovat studentskou organizaci a podpořit tak **rozvoj jejích členů**? Nabízíme spolupráci a **propagaci** na **národních i mezinárodních konferencích**.',	'sponzorstvi',	0.00,	-50,	'x',	5),
(11,	'Veletrh iKariéra VŠCHT Praha',	'Již 20. ročník Veletrhu iKariéra, který proběhne přímo na půdě VŠCHT, s očekávanou účastí přes 1000 studentů ze všech fakult. Vstup je pro všechny návštěvníky veletrhu zdarma a při vstupu obdrží “veletržní tašku” a Průvodce veletrhem. Průvodce veletrhem jednak usnadní návštěvníkům orientaci na veletrhu a zároveň tvoří další komunikační kanál mezi studenty a firmami. ',	'veletrh-ikariera-vscht-praha',	18900.00,	20,	'y',	2),
(12,	'Veletrh iKariéra ČVUT Praha 2014',	'Veletrh pracovních příležitostí, který IAESTE letos už pořádá přímo na půdě ČVUT v Praze. Jedná se již o 20. ročník. Letos se veletrh koná na fakultách informačních technologií elektrotechnické, strojní, stavební a v Národní technické knihovně. Veletrh iKariéra na ČVUT navštíví cca 8000 studentů. Pro návštěvníky veletrhů je účast zdarma. Všichni dostanou tašku s propagačními materiály společností a brožuru Průvodce veletrhem, která usnadňuje studentům orientaci na akci.',	'veletrh-ikariera-cvut-praha-2014',	24900.00,	20,	'y',	2),
(13,	'Průvodce prváka ČVUT 2014/2015',	'Průvodce prváka je útlá brožura formátu A5, která pomáhá v orientaci v prvních dnech ve škole. Jsou v ní veškeré informace, které student potřebuje ať už ohledně studia nebo zábavy mimo akademickou půdu. Obdrží ho každý student prvního ročníku v rámci zápisu do studia, imatrikulace nebo zahájení akademického roku. Také je k dispozici na studijních odděleních jednotlivých fakult. Firma má možnost zde inzerovat ve formátech A5, A6 a A8.',	'pruvodce-prvaka-cvut-2014-2015',	3900.00,	0,	'y',	4),
(14,	'Jobportál iKariera.cz',	'www.ikariera.cz je webový portál zaměřený na studenty technického směru z celé České republiky obsahující databázi více jak **14 tisíc registrovaných uživatelů**. Umožňuje zaměstnavatelům zveřejňovat nabídky práce, brigád a bakalářských či diplomových prací. Základní registrace a inzerování nabídek práce a brigád je **zdarma!**',	'jobportal-ikariera-cz',	2900.00,	-10,	'y',	3),
(15,	'Průvodce prváka VŠCHT Praha 2014/2015',	'',	'pruvodce-prvaka-vscht-praha-2014-2015',	2000.00,	-2,	'y',	4),
(16,	'Veletrh iKariéra Brno 2014',	'Veletrh pracovních příležitostí iKariéra v Brně je exkluzivní projekt studentské organizace IAESTE, který se letos bude konat již po dvacáté na akademické půdě Fakulty podnikatelské Vysokého učení technického v Brně. Pro návštěvníky veletrhu je vstup zdarma, což se odráží na vysoké návštěvnosti. Každoročně veletrh navštíví přes 4000 studentů a absolventů různých technických fakult nejen z VUT v Brně. \n\nPři vstupu na veletrh obdrží každý účastník veletržní tašku s brožurou Průvodce veletrhem. Součástí veletrhu je i bohatý doprovodný program v podobě prezentací společností a soutěží o zajímavé ceny.',	'veletrh-ikariera-brno-2014',	21900.00,	20,	'y',	2),
(17,	'Diář studenta 2014/2015 v Brně',	'Diář studenta je informativní brožura pro studenty všech ročníků, kterou IAESTE v Brně vydává pro studenty VUT (v nákladu 7000 ks) a MU (3000 ks) zcela zdarma. Kromě důležitých informací o studentově domovské univerzitě obsahuje i všemožné rady, jak si nejlépe zpříjemnit studium v Brně. \n\nProstřednictvím Diáře studenta lze oslovovat 10 000 brněnských studentů každý den po celý akademický rok. Studentům je Diář studenta k dispozici na imatrikulacích, na půdě jednotlivých fakult či v areálů kolejí, kde ho obdrží přímo z ruky člena IAESTE. Inzerci je možné sjednat lokálně pouze v Brně nebo celorepublikově (Brno, Ostrava, Liberec, Zlín) při nákladu 23 000 ks. ',	'diar-studenta-2014-2015-v-brne',	8450.00,	-3,	'y',	4),
(20,	'BUS iKariéra',	'Program exkurzí menších skupin studentů (10-20) ve výrobních prostorách společností. Exkurze je spojena s aktivním workshopem připraveným zákazníkem.\n\nStudenti jsou oslovováni dle požadavků zákazníka (je možné zadat požadavek na obor studia, ročník studia a jiné).\n',	'bus-ikariera',	20000.00,	-11,	'y',	3),
(23,	'Local Engineering Competition',	'Local Engineering Competition (LEC) je soutěž studentů techniky pořádaná studentskými\norganizacemi ve spolupráci s univerzitou.\nKaždý rok pořádáme pro motivované studenty soutěž, ve které mají možnost se přiučit\nnovým dovednostem, vyzkoušet si řešení opravdových problémů a především použít\nsvé teoretické znalosti v praktických případech, protože na to často ve škole nebývá čas.\nDále je naším cílem více propojit firmy a studenty. Firmy mají možnost si otestovat znalosti\na dovednosti studentů a především hledat nové talenty.\nSoutěží se ve dvou kategoriích - Case Study a Team design.\n\nBalíček Diamond:\n• Účast na slavnostním zahájení i ukončení LEC za přítomnosti účastníků, organizátorů, a zástupce fakulty ČVUT v Praze\n• Možnost zadat téma pro case study nebo team design\n• Poskytnutí CV soutěžících\n• Prostor pro přednášku a prezentaci v rámci soutěže\n• Logo na materiálech LEC 2013 (propagační materiály, webové stránky) jako diamantový partner projektu\n• Umístění loga v prostorách slavnostního zahájení soutěže\n• Poskytnutí propagačních materiálů účastníkům\n• Logo na tričkách účastníků\n\nBalíček Gold:\n• Účast na slavnostním zahájení i ukončení LEC za přítomnosti\núčastníků, organizátorů, a zástupce fakulty ČVUT v Praze\n• Poskytnutí CV soutěžících\n• Logo na materiálech LEC 2013 jako hlavní partner projektu\n• Umístění loga v prostorách slavnostního zahájení soutěže\n• Poskytnutí propagačních materiálů účastníkům\n• Logo na tričkách účastníků\n\nBalíček Silver:\n• Logo na materiálech LEC 2013 jako hlavní partner projektu\n• Umístění loga v prostorách slavnostního zahájení soutěže\n• Poskytnutí propagačních materiálů účastníkům\n• Logo na tričkách účastníků',	'local-engineering-competition',	10000.00,	0,	'n',	3),
(28,	'Bridge Builder Contest',	'Bridge Builder Contest je praktická soutěž pro studenty středních škol stavebních, strojních a gymnázií. Úkolem je ze špejlí postavit model most, který bude následně posouzen odbornou porotou a také podroben destrukční zkoušce. Vítězem se stává tým, který postaví most s nejlepším poměrem váha/nosnost. Cílem soutěže je dát studentům možnost vyzkoušet si teoretické znalosti formou zábavy',	'bridge-builder-contest',	0.00,	0,	'n',	3),
(31,	'Veletrh iKariéra UTB Zlín',	'Veletrh pracovních příležitostí se i tento rok bude konat v prostorech Fakulty aplikované informatiky, která je jednou ze šesti fakult zdejší univerzity, Univerzity Tomáše Bati ve Zlíně. Studenti dostávají stejně jako v každém roce Průvodce veletrhem a Katalog iKariéra. Díky velkému množství firem, soutěží a programu je veletrh hojně navštěvován studenty.',	'veletrh-ikariera-utb-zlin',	18900.00,	0,	'x',	2),
(36,	'Veletrh pracovních příležitostí T-Fórum 2014',	'IAESTE LC LIBEREC pořádá již 19. ročník veletrhu pracovních příležitostí T-Fórum 2014 v areálu Technické univerzity v Liberci.\nOčekávaná návštěvnost studentů ze 6-ti  fakult TUL. \nVstup je zdarma a každý návštěvník obdrží veletržní brožuru Průvodce veletrhem.\n\n\n',	'veletrh-pracovnich-prilezitosti-t-forum-2014',	0.00,	0,	'x',	2);

DROP TABLE IF EXISTS `product_variant`;
CREATE TABLE `product_variant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variant_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `variants_id_products_id` (`variant_id`,`product_id`),
  KEY `products_id` (`product_id`),
  CONSTRAINT `product_variant_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `variant` (`id`),
  CONSTRAINT `product_variant_ibfk_4` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `product_variant` (`id`, `variant_id`, `product_id`) VALUES
(1,	1,	1),
(2,	2,	2),
(3,	3,	4),
(21,	5,	11),
(23,	11,	12),
(24,	12,	13),
(25,	14,	14),
(43,	15,	15),
(38,	22,	16),
(40,	23,	17),
(48,	30,	23),
(45,	30,	28),
(56,	34,	31);

DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` text COLLATE utf8_czech_ci NOT NULL,
  `value` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `setting` (`id`, `key`, `value`) VALUES
(1,	'dph',	'21'),
(2,	'show_empty_in_menu',	'1'),
(3,	'show_numbers_in_menu',	'0'),
(4,	'title_prefix',	'eShop IAESTE'),
(5,	'title_sufix',	''),
(6,	'title_separator',	'|'),
(7,	'items_per_page',	'18');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(150) COLLATE utf8_czech_ci NOT NULL,
  `role` enum('guest','waiting','approved','moderator','admin') COLLATE utf8_czech_ci NOT NULL,
  `company_name` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `tel` varchar(25) COLLATE utf8_czech_ci NOT NULL,
  `web` varchar(250) COLLATE utf8_czech_ci NOT NULL,
  `ic` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `dic` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `account` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `note` text COLLATE utf8_czech_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `user` (`id`, `username`, `password`, `role`, `company_name`, `email`, `tel`, `web`, `ic`, `dic`, `account`, `note`, `created`) VALUES
(48,	'fanda',	'$2a$07$felztuqnnvd1pv28pw07rOs9odkJxwdRAGQUnUEHfJDdaeEaAk7NO',	'admin',	'Fanda',	'frantisek.rajtmajer@iaeste.cz',	'',	'http://',	'',	'',	'',	'',	'2013-10-15 23:35:22'),
(49,	'cvut',	'$2a$07$7hlwie9456r8x1yg04wj5eLZCZS0azCTZR3I8ae6LzU2uTOFpP4n.',	'moderator',	'Tvoje Máma',	'martynenko.max@gmail.com',	'734233286',	'http://martymax.eu',	'123456789',	'CZ12345678',	'134645648/1234',	'',	'2013-10-15 23:47:29'),
(50,	'brno',	'$2a$07$3vmrpy870c2hyxilt70d6ud16kRCfyk/PbnvyuyVZln7sQKzpymc.',	'moderator',	'IAESTE LC Brno',	'peter.makuta@iaeste.cz',	'+420 777 784 118',	'http://www.iaeste.cz',	'71195653',	'CZ 71195653',	'1615668001/5500',	'',	'2013-10-15 23:47:41'),
(51,	'liberec',	'$2a$07$rzqrbslohyh4dsztz23bheLVoCMc1iq0YItPdlDEq6kZD0tB8.fqm',	'moderator',	'IAESTE LC Liberec',	'josef.blazek@iaeste.cz',	'604175113',	'http://liberec.iaeste.cz',	'70961816',	'CZ70961816',	'177446067/0300',	'',	'2013-10-15 23:47:52'),
(52,	'zlin',	'$2a$07$y7hrbobi13ob7zxwedftpe8GvMWW/0CCYxEZ3eQtr3/uWITeGa/zO',	'moderator',	'IAESTE',	'H_enrich@azet.sk',	'00420776460064',	'http://iaeste-zlin.cz',	'7118259',	'CZ71182594',	'246442220/0300',	'',	'2013-10-15 23:48:05'),
(53,	'ostrava',	'$2a$07$12olj2pnaj54dr6dfl2efe2Cb9YuawR9lJA.l4ur64FjY9AQvTu3y',	'moderator',	'IAESTE VŠB-TU Ostrava',	'jan.kaspar@iaeste.cz',	'773882804',	'http://vsb.iaeste.cz',	'123456',	'123456',	'123456',	'',	'2013-10-15 23:48:14'),
(54,	'vscht',	'$2a$07$fpdhyos7h2gi54117esjbeFiLAc8EF1rPELbtVNKPvSqpCxbNvRj2',	'moderator',	'IAESTE VŠCHT Praha',	'vscht@iaeste.cz',	'220 443 068',	'www.vscht.iaeste.cz',	'71227172',	'CZ71227172',	'1829914001/5500',	'',	'2013-10-15 23:48:21'),
(55,	'plzen',	'$2a$07$8g8hskvfkpy90w11c89ecevWb4XJVGUUvaRha7BRpgBoP3/lau5pm',	'moderator',	'IAESTE ZČU Plzeň',	'frantisek.rajtmajer@iaeste.cz',	'+420 721 472 453',	'http://zcu.iaeste.cz',	'ico',	'dico',	'ucet',	'',	'2013-10-15 23:48:28'),
(57,	'martin',	'$2a$07$9d2r1yygqrpq28jnnp4b6uGLqhdXHd5AqtHllBz1R9XC9R6invj7q',	'admin',	'Martin Zlámal',	'mrtnzlml@gmail.com',	'723764899',	'http://www.zeminen.cz/',	'',	'',	'',	'',	'2013-10-16 11:27:54'),
(59,	'firma_plzen_02',	'$2a$07$dj5tbqqhlfkvx43xuhul8uFxlQlMQcyWMu6p7Act7O6CJesgaupym',	'approved',	'Firma Plzen 02',	'frantisek.rajtmajer@gmail.com',	'tele',	'webe',	'ic',	'dic',	'cislo uctu',	'',	'2013-10-18 10:31:45'),
(68,	'firma_plzen_01',	'$2a$07$x28ymftirkjydwttb12m4eajBj6pJxtziyYV30g6N1pqCLz5t7yRu',	'approved',	'Firma Plzen 01',	'frantisek.rajtmajer@gmail.com',	'+420721472453',	'www.zcu.iaeste.cz',	'74512563',	'CZ74512563',	'123455512/0300',	'',	'2013-11-07 22:12:10'),
(70,	'firma_plzen_03',	'$2a$07$jodtnk70dsmu03osiwsyde57v4ey4GoLLy7JuyXxet/V74lhnJ9H2',	'approved',	'Firma Plzen 03',	'frantisek.rajtmajer@gmail.com',	'65432168465',	'web',	'ico',	'asdfi',	'dsfgihas6525',	'',	'2013-11-09 19:19:22'),
(71,	'ostrava_firm_01',	'$2a$07$1uijyjz29cudh5bbahbssu0Z2ncE6OImtRml/Xs1Fk1P5INKfyAV2',	'approved',	'Ostravská technická',	'jan@kaspy.cz',	'123456789',	'ostech.cz',	'65478913',	'12359874',	'741852936/0546',	'',	'2013-11-11 13:19:34'),
(76,	'firma_vscht_01',	'$2a$07$wtma8h86ve0f5cfwmw49se8Hfvrg.tEVGeToZv4U4M.n0qp8aYsYa',	'approved',	'firma',	'annis@seznam.cz',	'605542389',	'www.iaeste,cz',	'',	'',	'',	'',	'2013-12-01 19:58:50'),
(77,	'BRUSH SEM s.r.o.',	'$2a$07$vex3tnoka12ga2516937xezDt38OsbkRNm8WeO937WAQ6/Y.J/YCy',	'approved',	'BRUSH SEM s.r.o.',	'marta.rypplova@brush.eu',	'739248497',	'brush-sem.cz',	'25745735',	'CZ25745735',	'138325/5400',	'',	'2013-12-19 09:16:41'),
(78,	'firma_zlin_01',	'$2a$07$aubkk522ph5pwm9fjkejluEEpd1o5f5id8ODTBAFo.uMxCB.Z45mu',	'approved',	'firma_zlin_01',	'henrich.horvath@iaeste.cz',	'776460064',	'www.iaeste-zlin.cz',	'',	'',	'',	'',	'2014-01-04 21:30:58'),
(79,	'tvojemama',	'$2a$07$ou341tcyiwyo6vptjcilse9rVzyCDN.c/RAwDawB27UJrco1W/r0K',	'approved',	'Tvoje Máma',	'martynenko.max@gmail.com',	'734233275',	'martymax.eu',	'',	'',	'',	'',	'2014-01-08 10:52:19'),
(80,	'david.cerny@senman.cz',	'$2a$07$uusqgtqq9ng88rdziqmipOKT45GLxoX.MYHfE6/eQid7LBwnw40Jy',	'approved',	'Senman s.r.o.',	'david.cerny@senman.cz',	'+420723327901',	'www.senman.cz',	'',	'',	'',	'',	'2014-01-08 14:49:00'),
(81,	'firma_fikt_liberec_2014',	'$2a$07$xeq2r5tncnlu9pab6c48cuKUlMn/WVisbCUcJAPSAxMgsGV/EZCx2',	'approved',	'firma_fikt_liberec_2014',	'josef.blazek@iaeste.cz',	'412014',	'http://iaeste.cz',	'',	'',	'',	'',	'2014-01-08 18:50:52');

DROP TABLE IF EXISTS `variant`;
CREATE TABLE `variant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_lc` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `variant` (`id`, `name`) VALUES
(30,	'BBC Ostrava'),
(13,	'Den iKariéra ČVUT'),
(23,	'DS Brno'),
(14,	'iKariera.cz'),
(3,	'kik'),
(31,	'LEC Ostrava'),
(2,	'PP zcu plzen'),
(12,	'Průvodce prváka ČVUT'),
(15,	'Průvodce prváka VŠCHT Praha 2014/2015'),
(34,	'Veletrh UTB Zlín'),
(22,	'ViK'),
(11,	'ViK ČVUT 2014'),
(5,	'ViK VŠCHT'),
(1,	'vpp zcu plzen');

DROP TABLE IF EXISTS `variant_item`;
CREATE TABLE `variant_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `price` double NOT NULL,
  `price_status` enum('abs','price','rel') COLLATE utf8_czech_ci NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  `variant_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_variants_id` (`name`,`variant_id`),
  KEY `variants_id` (`variant_id`),
  CONSTRAINT `variant_item_ibfk_2` FOREIGN KEY (`variant_id`) REFERENCES `variant` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `variant_item` (`id`, `name`, `price`, `price_status`, `priority`, `variant_id`) VALUES
(1,	'Standardní účast s kreativní stranou v Průvodci veletrhem',	26400,	'price',	3,	1),
(2,	'Standardní účast',	21900,	'price',	4,	1),
(3,	'Nepřímá účast / kreativní strana v Průvodci veletrhem',	7500,	'price',	1,	1),
(4,	'Nepřímá účast / strukturovaná strana v Průvodci veletrhem',	5400,	'price',	2,	1),
(5,	'Nepřímá účast / vložení materiálů do tašek',	2900,	'price',	0,	1),
(6,	'Formát A5',	13000,	'price',	2,	2),
(7,	'Formát A6',	8000,	'price',	3,	2),
(8,	'Formát A8',	4000,	'price',	4,	2),
(9,	'Vnitřní přední či zadní strana obálky',	18000,	'price',	0,	2),
(10,	'A1, A2 strukturovaná barevná strana',	19000,	'price',	3,	3),
(11,	'B kreativní barevná strana',	29000,	'price',	2,	3),
(12,	'C1, C2 kombinace standardní a kreativní strany',	39000,	'price',	1,	3),
(13,	'D kombinace dvou kreativních stran',	49000,	'price',	0,	3),
(14,	'2. nebo 3. strana obálky',	49000,	'price',	-1,	3),
(16,	'Standardní účast',	18900,	'price',	7,	5),
(17,	'Standardní účast s kreativní stranou v Průvodci veletrhem',	23000,	'price',	6,	5),
(18,	'Nepřímá účast / strukturovaná strana v Průvodci veletrhem',	4900,	'price',	5,	5),
(19,	'Nepřímá účast / kreativní strana  v Průvodci veletrhem',	6900,	'price',	4,	5),
(20,	'Nepřímá účast / vložení materiálu do tašek',	2900,	'price',	3,	5),
(21,	'2. nebo 3. strana obálky Průvodce veletrhem',	9900,	'price',	2,	5),
(22,	'Strandardní účast',	24900,	'price',	4,	11),
(23,	'Strandardní účast s kreativní stranou v Průvodci veletrhem',	28900,	'price',	3,	11),
(24,	'Nepřímá účast / kreativní strana v Průvodci veletrhem',	9900,	'price',	1,	11),
(25,	'Nepřímá účast / strukturovaná strana v Průvodci veletrhem',	5900,	'price',	2,	11),
(27,	'2. nebo 3. strana obálky Průvodce veletrhem',	14900,	'price',	0,	11),
(28,	'Vnitřní přední či zadní strana obálky',	24900,	'price',	2,	12),
(29,	'Formát A5',	15900,	'price',	3,	12),
(30,	'Formát A6',	7900,	'price',	4,	12),
(31,	'2. nebo 3. strana obálky Průvodce veletrhem',	9900,	'price',	-1,	1),
(32,	'Formát A8',	3900,	'price',	5,	12),
(33,	'Cena pro partnery',	40000,	'price',	0,	13),
(34,	'Balíček Mini - 80 kreditů',	2900,	'price',	3,	14),
(35,	'Balíček Medium - 300 kreditů',	9890,	'price',	1,	14),
(36,	'Balíček Medium - 800 kreditů',	23980,	'price',	0,	14),
(38,	'Vnitřní přední či zadní strana obálky',	9000,	'price',	2,	15),
(39,	'Formát A5',	6000,	'price',	3,	15),
(40,	'Formát A6',	4000,	'price',	4,	15),
(43,	'Formát A8',	2000,	'price',	5,	15),
(44,	'Vnější zadní strana obálky',	15000,	'price',	1,	15),
(52,	'Standardní účast',	21900,	'price',	6,	22),
(53,	'Standardní účast s kreativní stranou v Průvodci veletrhem',	26400,	'price',	5,	22),
(54,	'Nepřímá účast / kreativní strana v Průvodci veletrhem',	7900,	'price',	3,	22),
(55,	'Nepřímá účast / strukturovaná strana v Průvodci veletrhem',	5900,	'price',	4,	22),
(56,	'Nepřímá účast / vložení materiálů do tašek',	2900,	'price',	2,	22),
(57,	'2. nebo 3. strana obálky Průvodce veletrhem',	12900,	'price',	1,	22),
(58,	'Celorepubliková inzerce formát A6 (VUT, MU, VŠB-TU, TUL, UTB)',	45000,	'price',	0,	23),
(59,	'Inzerce formát A6',	16900,	'price',	4,	23),
(61,	'Inzerce formát 1/2 A6',	8450,	'price',	5,	23),
(62,	'3. strana obálky',	55000,	'price',	2,	23),
(71,	'Mediální partner',	0,	'price',	0,	30),
(72,	'Generální partner soutěže',	0,	'price',	0,	30),
(73,	'Balíček Diamond',	30000,	'price',	0,	31),
(74,	'Balíček Gold',	20000,	'price',	0,	31),
(75,	'Balíček Silver',	10000,	'price',	0,	31),
(76,	'Partner soutěže',	0,	'price',	0,	30),
(77,	'4. strana obálky',	120000,	'price',	2,	23),
(78,	'',	18900,	'price',	0,	34),
(81,	'Jaro 2014 - jednodenní varianta',	18900,	'price',	0,	34),
(82,	'Jaro + podzim - dvoudenní varianta',	24900,	'price',	1,	34);

DROP TABLE IF EXISTS `variants_items_individual`;
CREATE TABLE `variants_items_individual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variants_id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `price` double NOT NULL,
  `price_status` enum('abs','rel') COLLATE utf8_czech_ci NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `variants_id` (`variants_id`),
  CONSTRAINT `variants_items_individual_ibfk_1` FOREIGN KEY (`variants_id`) REFERENCES `variant` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2014-01-12 23:32:18

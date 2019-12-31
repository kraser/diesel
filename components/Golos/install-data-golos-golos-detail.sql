SET NAMES utf8;

TRUNCATE `bm_golos`;

INSERT INTO `bm_golos` (`id`,`name`,`enabled`) VALUES (1,'Любимое место',0);
INSERT INTO `bm_golos` (`id`,`name`,`enabled`) VALUES (2,'Стало ли жить лучше?',0);
INSERT INTO `bm_golos` (`id`,`name`,`enabled`) VALUES (3,'Что Вы сделаете, если найдете на улице чужие документы/перчатки/очки?',0);
INSERT INTO `bm_golos` (`id`,`name`,`enabled`) VALUES (4,'Нужен ли нам кинотеатр?',0);
INSERT INTO `bm_golos` (`id`,`name`,`enabled`) VALUES (5,'Ваше материальное положение',1);
INSERT INTO `bm_golos` (`id`,`name`,`enabled`) VALUES (6,'Референдум',1);

INSERT INTO `bm_golos_detail` (`golos_id`,`order`,`quest`,`answers`) VALUES (6,1,'Да',78);
INSERT INTO `bm_golos_detail` (`golos_id`,`order`,`quest`,`answers`) VALUES (6,2,'Да-Да',58);
INSERT INTO `bm_golos_detail` (`golos_id`,`order`,`quest`,`answers`) VALUES (6,3,'Да-Нет',115);
INSERT INTO `bm_golos_detail` (`golos_id`,`order`,`quest`,`answers`) VALUES (6,4,'Да-Нет-Наверное',888);
INSERT INTO `bm_golos_detail` (`golos_id`,`order`,`quest`,`answers`) VALUES (6,5,'Нет',666);
INSERT INTO `bm_golos_detail` (`golos_id`,`order`,`quest`,`answers`) VALUES (6,6,'Нет-Нет',335);
INSERT INTO `bm_golos_detail` (`golos_id`,`order`,`quest`,`answers`) VALUES (5,2,'Ээээ...',333);
INSERT INTO `bm_golos_detail` (`golos_id`,`order`,`quest`,`answers`) VALUES (5,1,'Завтра будет лучше',33);
INSERT INTO `bm_golos_detail` (`golos_id`,`order`,`quest`,`answers`) VALUES (5,3,'Чем приставать, помогли б материально...',555);


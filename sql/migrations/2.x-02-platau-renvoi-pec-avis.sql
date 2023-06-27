SET NAMES 'utf8';

CREATE TABLE IF NOT EXISTS `platauconsultation` (
    `ID_PLATAU` char(11),
    `STATUT_AVIS` varchar(50) DEFAULT NULL,
    `DATE_AVIS` date DEFAULT NULL,
    `STATUT_PEC` varchar(50) DEFAULT NULL,
    `DATE_PEC` date DEFAULT NULL,
    PRIMARY KEY (`ID_PLATAU`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
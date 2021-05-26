SET NAMES 'utf8';
-- -----------------------------------------------------
-- Table`piecejointelie`
-- -----------------------------------------------------
CREATE TABLE `piecejointelie` (
  `ID_PIECEJOINTE` bigint(20)  NOT NULL,
  `ID_FILS_PIECEJOINTE` bigint(20)  NOT NULL,
  PRIMARY KEY (`ID_PIECEJOINTE`,`ID_FILS_PIECEJOINTE`),
  KEY `fk_piecejointelie_piecejointe1_idx` (`ID_FILS_PIECEJOINTE`),
  CONSTRAINT `fk_piecejointelie_piecejointe1` FOREIGN KEY (`ID_PIECEJOINTE`) REFERENCES `piecejointe` (`ID_PIECEJOINTE`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_piecejointelie_piecejointe2` FOREIGN KEY (`ID_FILS_PIECEJOINTE`) REFERENCES `piecejointe` (`ID_PIECEJOINTE`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
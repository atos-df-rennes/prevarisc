insert into listetypechamprubrique values (NULL,'Parent');

alter table champ add column ID_PARENT bigint(20);

alter table champ 
	ADD CONSTRAINT fk_ID_PARENT_CHAMP 
    foreign key (ID_PARENT) 
    references champ(ID_CHAMP);

INSERT IGNORE INTO `capsulerubrique` VALUES
(1, 'descriptifEtablissement', 'Descriptif de l''établissement');
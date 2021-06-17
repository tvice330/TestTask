select * from data,link,info where link.info_id = info.id and link.data_id = data.id

CREATE TABLE `info` (
`id` int(11) NOT NULL auto_increment,
`name` varchar(255) default NULL,
`desc` text default NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf-8;

CREATE TABLE `data` (
`id` int(11) NOT NULL auto_increment,
`date` date default NULL,
`value` INT(11) default NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf-8;

CREATE TABLE `link` (
`data_id` int(11) NOT NULL,
`info_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf-8;

Оптимизация таблиц:
(Тип ENGINE предлагаю InnoDB, она в некоторіх моментах  работает медленнее чем MyISAM, но поддерживает внешние ключи 
-это способ связать записи в двух таблицах по определенным полям так, что при обновлении поля в родительской автоматически 
происходит определенное изменение поля в дочерней 
(дочернюю и родительскую выбираешь при создании ключа; точнее, создаешь ключ в дочерней, который ссылается на родительскую)
+Транзакция (Transaction) — блок операторов SQL , который в случае ошибки в одном запросе, возвращается к предыдущему состоянию 
(Rollback), и только в случае выполнения всех запросов подтверждается (Commit))

CREATE TABLE link (
data_id int(11) NOT NULL,
info_id int(11) NOT NULL,
   PRIMARY KEY (data_id , info_id ),
 FOREIGN KEY (data_id) REFERENCES data,
    FOREIGN KEY (info_id ) REFERENCES info,
    ) ENGINE=InnoDB DEFAULT CHARSET=utf-8;

CREATE TABLE data (
    id int(11) NOT NULL AUTO_INCREMENT,
    date date,
   value int(11),
    PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf-8;
CREATE TABLE info (
id int(11) NOT NULL AUTO_INCREMENT,
name varchar(255),
desc text ,
PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf-8;

Оптимизация запроса:

SELECT * FROM link l INNER JOIN data d on l.data_id = d.id INNER JOIN info i on l.info_id = i.id
<?php

/**
 * Написать класс Init, от которого нельзя сделать наследника, состоящий из
 * трёх методов:
 * * create() — доступен только для методов класса, создает таблицу test,
 *   содержащую 5 полей:
 *   * id — целое, автоинкрементарное
 *   * script_name — строковое, длиной 25 символов
 *   * start_time — целое
 *   * end_time — целое
 *   * result — один вариант из  'illegal', 'failed', 'success'
 * * fill() — доступен только для методов класса, заполняет таблицу случайными
 *   данными
 * * get() — доступен извне класса, выбирает данные из таблицы test по критерию:
 *   'success'
 *
 * В конструкторе выполняются методы create() и fill().
 */

final class Init
{
    private $dbh;
    private $tableName = 'test';

    private function create()
    {
        try {
            $this->dbh->exec(
                "CREATE TABLE IF NOT EXISTS $this->tableName (
                    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                    script_name VARCHAR(25) NOT NULL,
                    start_time INT UNSIGNED NOT NULL,
                    end_time INT UNSIGNED NOT NULL,
                    result ENUM(
                         'illegal', 'failed', 'success'
                    ) NOT NULL
                );"
            );
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    private function fill()
    {
        $scriptName = substr(
            str_shuffle(
                '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
            ),
            0,
            25
        );
        $startTime = mt_rand();
        $endTime = mt_rand();
        $result = mt_rand(1, 3);

        try {
            $this->dbh->exec(
                "INSERT INTO $this->tableName (
                    script_name,
                    start_time,
                    end_time,
                    result
                ) VALUES (
                    '$scriptName',
                    $startTime,
                    $endTime,
                    $result
                );"
            );
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function __construct()
    {
        $host = 'localhost';
        $dbName = 'tasks';
        $user = 'root';
        $pass = 'root';

        try {
            $this->dbh = new PDO("mysql:host=$host", $user, $pass);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->dbh->exec(
                "CREATE DATABASE IF NOT EXISTS $dbName
                DEFAULT CHARACTER SET utf8
                DEFAULT COLLATE utf8_general_ci;"
            );
            $this->dbh->exec("use $dbName;");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $this->create();
        $this->fill();
    }

    public function get()
    {
        try {
            $sth = $this->dbh->query(
                "SELECT * FROM $this->tableName
                WHERE result in (3);"
            );
            $sth->setFetchMode(PDO::FETCH_ASSOC);
            $result = $sth->fetchAll();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $result;
    }

    public function __destruct()
    {
        $this->dbh = null;
    }
}

$init = new Init();
echo '<pre>';
print_r($init->get());
echo '</pre>';
?>
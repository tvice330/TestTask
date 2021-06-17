<?php
/**
 * Создать скрипт, который в папке datafiles найдет все файлы, имена которых
 * состоят из цифр и букв латинского алфавита и имеют расширение ixt, и выведет
 * на экран имена этих файлов, упорядоченных по имени.
 *
 * Задание должно быть выполнено с использованием регулярных выражений.
 */
/**
    * Считываем директорию
    * 
    * Получаем объекты всех файлов и папок, находящихся
    * в директории "datafiles"
    * 
    */
$allFiles = scandir('datafiles');
    /*
        * Фильтруем элементы массива с помощью callback-функции
        * 
        * Имя файла должно содержать в себе только латинские буквы и цифры
        * и содержать расширение "ixt"
        */
$files = array_filter($allFiles, function ($file) {
    return preg_match('/^[0-9a-z]+\.ixt$/i', $file);
});
    /*
    * Перебераем цикл 
    */
foreach ($files as $file) {
    /*
     * Выводим имя файла
     */
    echo $file . '<br>';
}
?>
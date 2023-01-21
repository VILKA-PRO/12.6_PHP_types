<?php
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
    [
        'fullname' => 'Ницше Фридрих Ви́льгельм',
        'job' => 'философ',
    ],
    [
        'fullname' => 'Сидоров Антон Сергеевич',
        'job' => 'babysitter',
    ],
];

$fullFio = $example_persons_array[rand(0, count($example_persons_array) - 1)]['fullname'];

function getPartsFromFullname($fio)
{
    $prepArr = ['surname', 'name', 'patronomyc'];
    $exploaded = explode(" ", $fio);
    $fioArr = array_combine($prepArr, $exploaded);
    return $fioArr;
}


$gender = 'gender';
$maleRate = 0;
$femaleRate = 0;
$unknownRate = 0;

function getFullnameFromParts($splittedFioArr)
{
    $combinedFioStr = $splittedFioArr['surname'] . ' ' . $splittedFioArr['name'] .  ' ' . $splittedFioArr['patronomyc'];
    return $combinedFioStr;
}

function getShortName($fio)
{
    $fioArr = getPartsFromFullname($fio);
    $shortedSurname = mb_substr($fioArr['surname'], 0, 1);
    $secureName = $fioArr['name'] . " $shortedSurname.";
    return $secureName;
}


function getGenderFromName($fio)
{
    $splittedFio = getPartsFromFullname($fio);
    $genderSign = 0;
    $gender = '';

    if (mb_substr($splittedFio['patronomyc'], -3) == 'вна') {
        $genderSign -= 2;
    } else if (mb_substr($splittedFio['patronomyc'], -2) == 'ич') {
        $genderSign++;
    }

    if (mb_substr($splittedFio['surname'], -2) == 'ва') {
        $genderSign--;
    } else if (mb_substr($splittedFio['surname'], -1) == 'в') {
        $genderSign++;
    }

    if (mb_substr($splittedFio['name'], -1) == 'а') {
        $genderSign--;
    } else if (mb_substr($splittedFio['name'], -1) == 'й' || mb_substr($splittedFio['name'], -1) == 'н') {
        $genderSign++;
    }

    if ($genderSign > 0) {
        $gender = 'Мужчиной';
    } else if ($genderSign < 0) {
        $gender = 'Женщиной';
    } else {
        $gender = 'сложным для определения пола';
    }

    return $gender;
}


function getGenderDescription($personsArray)
{
    // Беру исходный массив и получаю новый только из имен. 
    $fio_arr = array_column($personsArray, 'fullname');
    foreach ($fio_arr as &$value) {
        // Заменяю имена на пол
        $value = getGenderFromName($value);
    }
    unset($value);

    // Вычисляю кол-во вхождений разного пола. С фильтрцие получалось больше кода. Решил так сделать.
    $number_of_people = count($fio_arr);
    $number_of_males = array_count_values($fio_arr)['Мужчиной'];
    $number_of_females = array_count_values($fio_arr)['Женщиной'];
    $number_of_unknown = array_count_values($fio_arr)['сложным для определения пола'];

    // Вычисляю отношение и записывыаю в глобальные переменные
    global $maleRate;
    global $femaleRate;
    global $unknownRate;

    $maleRate = round($number_of_males / $number_of_people * 100, 1);
    $femaleRate = round($number_of_females / $number_of_people * 100, 1);
    $unknownRate = (100 - ($maleRate + $femaleRate));
}


/* ======== определение «идеальной» пары ======== */

function getPerfectPartner($surname, $name, $patronomyc, $arr)
{
    // Завожу строки в массив для функции getFullnameFromParts
    $newPersonArr = [
        'surname' => $surname,
        'name' => $name,
        'patronomyc' => $patronomyc,
    ];
    $newPersonFio = getFullnameFromParts($newPersonArr);

    // Регистр каждого слова с заглавной    
    $newPersonFio = mb_convert_case($newPersonFio, MB_CASE_TITLE_SIMPLE);
    $newPersonGender = getGenderFromName($newPersonFio);
    $randPerson = $arr[rand(0, count($arr) - 1)]['fullname'];
    $randPersonGender = getGenderFromName($randPerson);

    if ($newPersonGender == 'сложным для определения пола') {
        echo
        <<<HEREDOCLETTER
        Итак, на арену любви выходит новичок: $newPersonFio и старожил: $randPerson<br><br>
        <span style='font-size:24px'>
        УПС... Извините, не могу определить пол новенького, а строить любовь при неоднозначных половых признаках, у нас запрещено</span></span>
        HEREDOCLETTER;
    } else {

        while ($randPersonGender == 'сложным для определения пола' || $newPersonGender == $randPersonGender) {
            $randPerson = $arr[rand(0, count($arr) - 1)]['fullname'];
            $randPersonGender = getGenderFromName($randPerson);
        }

        $randPercent = rand(50, 100);
        $newPersonFioShort = getShortName($newPersonFio);
        $randPersonShort = getShortName($randPerson);
        echo
        <<<HEREDOCLETTER
        Итак, на арену любви выходит новичок: $newPersonFio и старожил: $randPerson<br><br>
        <span style='font-size:24px'>
        $newPersonFioShort + $randPersonShort = <br><span style='font-size:34px;'>♡ Идельно на $randPercent% ♡</span></span>
        HEREDOCLETTER;
    }
}


?>

<!doctype html>
<html lang="ru">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">

    <title>12.6. Практика</title>
</head>

<body>


    <div class="container">
        <div class="row mt-5 mb-3">
            <div class="col-sm text-center">
                <h1>Модуль 12. Типы данных </h1>
                <br><br>
                <h2>Разбиение и объединение ФИО</h2>
                <p>Итак, мы имеем исходный массив <strong>$example_persons_array</strong> и берем из него случайного человека:
                </p>

                <?php

                echo "<p style='font-size:50px;'><b>$fullFio</b> </p>";
                echo "<br><p>Нежно раcчленяем его функцией <b>getPartsFromFullname</b>:<br>";
                echo "Фамилия: " . getPartsFromFullname($fullFio)['surname'] . ", имя: " . getPartsFromFullname($fullFio)['name'] . ", отчество: " . getPartsFromFullname($fullFio)['patronomyc'] . "</p> 
                <br><p> И также нежно собираем обратно функцией <b>getFullnameFromParts</b>:<br> <span style='font-size:24px'> <span style = 'text-decoration:underline'> " . getFullnameFromParts(getPartsFromFullname($fullFio)) . "</span> и да, этот человек оказался <span style='text-decoration:underline'>" . getGenderFromName($fullFio) .  "</span></span></p>
                <br><p> Для безопасников сокращаем фамилию и отрезаем лишнее функцией <b>getShortName</b>:<br> <span style='font-size:24px; text-decoration:underline'>" . getShortName($fullFio) . "</span>";
                ?>

            </div>
        </div>

        <div class="row">
            <div class="col-sm">



            </div>
        </div>

        <div class="row mt-5 mb-3">
            <div class="col-sm text-center">
                <h2>Определение возрастно-полового состава</h2>
                <?php
                getGenderDescription($example_persons_array);

                echo <<<HEREDOCLETTER
                <br><br><span style='font-size:24px'>
                Гендерный состав аудитории:</span><br>
                ---------------------------<br>
                Мужчины - $maleRate%<br>
                Женщины - $femaleRate%<br>
                Не удалось определить - $unknownRate%<br><br><br>
            HEREDOCLETTER;

                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm">

            </div>
        </div>

        <div class="row mt-5 mb-3">
            <div class="col-sm text-center">
                <h4>А теперь поиграем в любовь</h4>
                <?php
                // Вариант с неопределенным полом
                // getPerfectPartner('ниЦше', 'фРИдрих', 'Ви́ЛЬгельм', $example_persons_array)

                // Вариант с мужчиной
                getPerfectPartner('Великий', 'Аркадий', 'Новиков', $example_persons_array)

                ?>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-sm">

            </div>
        </div>

        <div class="row mt-5 mb-3">
            <div class="col-sm text-center">
                <p class="text-muted lead"><small> Designed by Ivan Us in SkillFactory</small></p>
            </div>
        </div>

    </div>





</body>

</html>
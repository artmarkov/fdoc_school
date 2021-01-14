<?php
/* @var $index int */
if ($index % 10 != 9) { // ЮЛ
    $company = $faker->company;
    $type = 'UL';
    $ogrn = $faker->numberBetween(1000000000000, 9999999999999);
    $inn = $faker->numberBetween(1000000000, 9999999999);
    $kpp = $faker->numberBetween(100000000, 999999999);
} else { // ИП
    $company = $faker->name;
    $type = 'IP';
    $ogrn = $faker->numberBetween(100000000000000, 999999999999999);
    $inn = $faker->numberBetween(100000000000, 999999999999);
    $kpp = '';
}

if ($index == 0) {
    return [
        'o_id' => 1000,
        'createDate' => '23-10-2018 11:04:13',
        'createUser' => 1000,
        'type' => 'UL',
        'name' => '"ОБЩЕСТВО С ОГРАНИЧЕННОЙ ОТВЕТСТВЕННОСТЬЮ ""ДЖИ ЭМ АВТО"""',
        'briefname' => 'Росстандарт',
        'firmname' => '',
        'ogrn' => '1234577777777',
        'inn' => '1245677778',
        'address' => 'г. Москва, проезд Китайгородский, д.77',
        'address_legal' => '',
        'kpp' => '',
        'phone' => '',
        'email' => 'gost@qwerty.ru',
        'revocamp_type' => '2',
        'l_snils' => '',
        'l_thirdname' => '',
        'l_surname' => '',
        'l_name' => '',
        'r_thirdname' => '',
        'r_surname' => '',
        'r_name' => '',
    ];
}
return [
    'o_id' => 1000 + (int)$index,
    'createDate' => date('d-m-Y H:i:s'),
    'createUser' => 1000,
    'type' => $type,
    'name' => $company,
    'briefname' => $company,
    'firmname' => '',
    'ogrn' => $ogrn,
    'address' => $faker->address,
    'address_legal' => $faker->address,
    'inn' => $inn,
    'kpp' => $kpp,
    'phone' => $faker->phoneNumber,
    'email' => $faker->companyEmail,
    'l_snils' => '',
    'l_thirdname' => '',
    'l_surname' => $faker->lastName,
    'l_name' => $faker->firstName,
    'r_thirdname' => '',
    'r_surname' => $faker->lastName,
    'r_name' => $faker->firstName,
    'revocamp_type' => '2',
];


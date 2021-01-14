<?php

namespace main\cron\tasks;

/**
 * Рассылка уведомлений об окончании ЭЦП
 */
class UsersHandler implements TaskInterface
{

    public function run()
    {
//        $p1 = new db_ViewObject('user_sort');
//        $p1->setFetchColumns('o_id');
//        $r = $p1->fetchData();
//        foreach ($r as $value) {
//            $user = ObjectFactory::user($value['o_id']);
//            $certdate = $user->getval('certFingerprintEndDate');
//            $dates = (!empty($certdate)) ? (strtotime($certdate) - strtotime(date('d-m-Y'))) / 86400 : '';
//            if (($dates == 5 or $dates == 30) and ($user->getval('email') != '')) {
//                $this->sendMailecp($dates, $user);
//            }
//        }
    }

//    protected function sendMailecp($dates, $user)
//    {
//        $headers = "MIME-Version: 1.0\r\n";
//        $headers .= "Content-Type: text/html;charset=utf-8 \r\n";
//        mail($user->getval('email'), iconv('windows-1251', 'utf-8', 'Уведомление о сроке окончания цифровой подписи'),
//            iconv('windows-1251', 'utf-8', 'Внимание! до срока окончания ЭЦП осталось ' . $dates . ' дней. Укажите данные новой электронной подписи.'), $headers);
//    }
}

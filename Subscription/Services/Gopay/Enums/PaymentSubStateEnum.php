<?php

namespace Services\Gopay\Enums;

enum PaymentSubStateEnum: string
{
    case _101 = '_101';
    case _102 = '_102';
    case _3001 = '_3001';
    case _3002 = '_3002';
    case _3003 = '_3003';
    case _5001 = '_5001';
    case _5002 = '_5002';
    case _5003 = '_5003';
    case _5004 = '_5004';
    case _5005 = '_5005';
    case _5006 = '_5006';
    case _5007 = '_5007';
    case _5008 = '_5008';
    case _5009 = '_5009';
    case _5010 = '_5010';
    case _5011 = '_5011';
    case _5012 = '_5012';
    case _5013 = '_5013';
    case _5014 = '_5014';
    case _5015 = '_5015';
    case _5016 = '_5016';
    case _5017 = '_5017';
    case _5018 = '_5018';
    case _5019 = '_5019';
    case _5020 = '_5020';
    case _5021 = '_5021';
    case _5022 = '_5022';
    case _5023 = '_5023';
    case _5024 = '_5024';
    case _5025 = '_5025';
    case _5026 = '_5026';
    case _5027 = '_5027';
    case _5028 = '_5028';
    case _5029 = '_5029';
    case _5030 = '_5030';
    case _5031 = '_5031';
    case _5033 = '_5033';
    case _5035 = '_5035';
    case _5036 = '_5036';
    case _5037 = '_5037';
    case _5038 = '_5038';
    case _5039 = '_5039';
    case _5040 = '_5040';
    case _5041 = '_5041';
    case _5042 = '_5042';
    case _5043 = '_5043';
    case _5044 = '_5044';
    case _5045 = '_5045';
    case _5046 = '_5046';
    case _5047 = '_5047';
    case _5048 = '_5048';
    case _5049 = '_5049';
    case _6502 = '_6502';
    case _6504 = '_6504';

    public function label(): string
    {
        return match ($this) {
            self::_101 => 'Čekáme na provedení online platby',
            self::_102 => 'Čekáme na provedení offline platby',
            self::_3001 => 'Bankovní platba potvrzena avízem',
            self::_3002 => 'Bankovní platba potvrzena výpisem',
            self::_3003 => 'Bankovní platba nebyla potvrzena',
            self::_5001 => 'Schváleno s nulovou částkou',
            self::_5002 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu dosažení limitů na platební kartě',
            self::_5003 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu problémů na straně vydavatele platební karty',
            self::_5004 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu problému na straně vydavatele platební karty',
            self::_5005 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu zablokované platební karty',
            self::_5006 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu nedostatku peněžních prostředků na platební kartě',
            self::_5007 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu expirované platební karty',
            self::_5008 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu zamítnutí CVV/CVC kódu',
            self::_5009 => 'Zamítnutí platby v systému 3D Secure banky zákazníka',
            self::_5010 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu problémů na platební kartě',
            self::_5011 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu problémů na účtu platební karty',
            self::_5012 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu technických problémů v autorizačním centru banky zákazníka',
            self::_5013 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu chybného zadání čísla platební karty',
            self::_5014 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu problémů na platební kartě',
            self::_5015 => 'Zamítnutí platby v systému 3D Secure banky zákazníka',
            self::_5016 => 'Zamítnutí platby v autorizačním centru banky zákazníka, platba nebyla povolena na platební kartě zákazníka',
            self::_5017 => 'Zamítnutí platby v systému 3D Secure banky zákazníka',
            self::_5018 => 'Zamítnutí platby v systému 3D Secure banky zákazníka',
            self::_5019 => 'Zamítnutí platby v systému 3D Secure banky zákazníka',
            self::_5020 => 'Neznámá konfigurace',
            self::_5021 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu dosažení nastavených limitů na platební kartě',
            self::_5022 => 'Nastal technický problém spojený s autorizačním centrem banky zákazníka',
            self::_5023 => 'Platba nebyla provedena',
            self::_5024 => 'Platba nebyla provedena. Platební údaje nebyly zadány v časovém limitu na platební bráně',
            self::_5025 => 'Platba nebyla provedena. Konkrétní důvod zamítnutí je sdělen přímo zákazníkovi',
            self::_5026 => 'Platba nebyla provedena. Součet kreditovaných částek překročil uhrazenou částku',
            self::_5027 => 'Platba nebyla provedena. Uživatel není oprávněn k provedení operace',
            self::_5028 => 'Platba nebyla provedena. Částka k úhradě překročila autorizovanou částku',
            self::_5029 => 'Platba zatím nebyla provedena',
            self::_5030 => 'Platba nebyla provedena z důvodu opakovaného zadání platby',
            self::_5031 => 'Při platbě nastal technický problém na straně banky',
            self::_5033 => 'SMS se nepodařilo doručit',
            self::_5035 => 'Platební karta je vydaná v regionu, ve kterém nejsou podporovány platby kartou',
            self::_5036 => 'Zamítnutí platby v autorizačním centru banky zákazníka z důvodu problémů na účtu platební karty',
            self::_5037 => 'Držitel platební karty zrušil platbu',
            self::_5038 => 'Platba nebyla provedena',
            self::_5039 => 'Platba byla zamítnuta v autorizačním centru banky zákazníka z důvodu zablokované platební karty',
            self::_5040 => 'Duplicitni reversal transakce',
            self::_5041 => 'Duplicitní transakce',
            self::_5042 => 'Bankovní platba byla zamítnuta',
            self::_5043 => 'Platba zrušena uživatelem',
            self::_5044 => 'SMS byla odeslána. Zatím se ji nepodařilo doručit',
            self::_5045 => 'Platba byla přijata. Platba bude připsána po zpracování v síti Bitcoin',
            self::_5046 => 'Platba nebyla uhrazena v plné výši',
            self::_5047 => 'Platba byla provedena po splatnosti',
            self::_5048 => 'Zákazník neudělil souhlas s provedením PSD2 platby',
            self::_5049 => '_5049',
            self::_6502 => 'Zamítnutí platby v systému 3D Secure banky zákazníka',
            self::_6504 => 'Zamítnutí platby v systému 3D Secure banky zákazníka',
        };
    }
}

<?php

namespace backend\components;

class SinhalaFontConverter
{
    /*
     * NOTE:
     * This is the conversion place.
     * You must adjust the DLSarala mapping according to your actual DLSarala keyboard/font map.
     *
     * Flow:
     * Unicode Sinhala -> DLSarala encoded text before DB save.
     * DLSarala encoded text -> Unicode Sinhala before showing in form.
     */

    public static function unicodeToDLSarala($text)
    {
        if ($text === null || trim($text) === '') {
            return $text;
        }

        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        /*
         * IMPORTANT:
         * Put longer combinations first.
         * Example:
         * කා should convert before ක.
         */

        $map = [
            // Sinhala independent vowels
            'අ' => 'w',
            'ආ' => 'wd',
            'ඇ' => 'we',
            'ඈ' => 'wE',
            'ඉ' => 'b',
            'ඊ' => 'B',
            'උ' => 'W',
            'ඌ' => 'W!',
            'එ' => 't',
            'ඒ' => 'ta',
            'ඓ' => 'ft',
            'ඔ' => 'T',
            'ඕ' => 'Ta',
            'ඖ' => 'T!',

            // Consonants with vowels - common examples
            'කා' => 'ld',
            'කැ' => 'le',
            'කෑ' => 'lE',
            'කි' => 'ls',
            'කී' => 'lS',
            'කු' => 'lq',
            'කූ' => 'lQ',
            'කෙ' => 'fl',
            'කේ' => 'fla',
            'කො' => 'fld',
            'කෝ' => 'flda',
            'ක්' => 'la',
            'ක' => 'l',

            'ගා' => '.d',
            'ගැ' => '.e',
            'ගෑ' => '.E',
            'ගි' => '.s',
            'ගී' => '.S',
            'ගු' => '.q',
            'ගූ' => '.Q',
            'ගෙ' => 'f.',
            'ගේ' => 'f.a',
            'ගො' => 'f.d',
            'ගෝ' => 'f.da',
            'ග්' => '.a',
            'ග' => '.',

            'චා' => 'pd',
            'චැ' => 'pe',
            'චෑ' => 'pE',
            'චි' => 'ps',
            'චී' => 'pS',
            'චු' => 'pq',
            'චූ' => 'pQ',
            'චෙ' => 'fp',
            'චේ' => 'fpa',
            'චො' => 'fpd',
            'චෝ' => 'fpda',
            'ච්' => 'pa',
            'ච' => 'p',

            'ටා' => 'gd',
            'ටැ' => 'ge',
            'ටෑ' => 'gE',
            'ටි' => 'gs',
            'ටී' => 'gS',
            'ටු' => 'gq',
            'ටූ' => 'gQ',
            'ටෙ' => 'fg',
            'ටේ' => 'fga',
            'ටො' => 'fgd',
            'ටෝ' => 'fgda',
            'ට්' => 'ga',
            'ට' => 'g',

            'ඩා' => 'vd',
            'ඩැ' => 've',
            'ඩෑ' => 'vE',
            'ඩි' => 'vs',
            'ඩී' => 'vS',
            'ඩු' => 'vq',
            'ඩූ' => 'vQ',
            'ඩෙ' => 'fv',
            'ඩේ' => 'fva',
            'ඩො' => 'fvd',
            'ඩෝ' => 'fvda',
            'ඩ්' => 'va',
            'ඩ' => 'v',

            'තා' => ';d',
            'තැ' => ';e',
            'තෑ' => ';E',
            'ති' => ';s',
            'තී' => ';S',
            'තු' => ';q',
            'තූ' => ';Q',
            'තෙ' => 'f;',
            'තේ' => 'f;a',
            'තො' => 'f;d',
            'තෝ' => 'f;da',
            'ත්' => ';a',
            'ත' => ';',

            'දා' => 'od',
            'දැ' => 'oe',
            'දෑ' => 'oE',
            'දි' => 'os',
            'දී' => 'oS',
            'දු' => 'oq',
            'දූ' => 'oQ',
            'දෙ' => 'fo',
            'දේ' => 'foa',
            'දො' => 'fod',
            'දෝ' => 'foda',
            'ද්' => 'oa',
            'ද' => 'o',

            'නා' => 'kd',
            'නැ' => 'ke',
            'නෑ' => 'kE',
            'නි' => 'ks',
            'නී' => 'kS',
            'නු' => 'kq',
            'නූ' => 'kQ',
            'නෙ' => 'fk',
            'නේ' => 'fka',
            'නො' => 'fkd',
            'නෝ' => 'fkda',
            'න්' => 'ka',
            'න' => 'k',

            'පා' => 'md',
            'පැ' => 'me',
            'පෑ' => 'mE',
            'පි' => 'ms',
            'පී' => 'mS',
            'පු' => 'mq',
            'පූ' => 'mQ',
            'පෙ' => 'fm',
            'පේ' => 'fma',
            'පො' => 'fmd',
            'පෝ' => 'fmda',
            'ප්' => 'ma',
            'ප' => 'm',

            'බා' => 'nd',
            'බැ' => 'ne',
            'බෑ' => 'nE',
            'බි' => 'ns',
            'බී' => 'nS',
            'බු' => 'nq',
            'බූ' => 'nQ',
            'බෙ' => 'fn',
            'බේ' => 'fna',
            'බො' => 'fnd',
            'බෝ' => 'fnda',
            'බ්' => 'na',
            'බ' => 'n',

            'මා' => 'ud',
            'මැ' => 'ue',
            'මෑ' => 'uE',
            'මි' => 'us',
            'මී' => 'uS',
            'මු' => 'uq',
            'මූ' => 'uQ',
            'මෙ' => 'fu',
            'මේ' => 'fua',
            'මො' => 'fud',
            'මෝ' => 'fuda',
            'ම්' => 'ua',
            'ම' => 'u',

            'යා' => 'hd',
            'යැ' => 'he',
            'යෑ' => 'hE',
            'යි' => 'hs',
            'යී' => 'hS',
            'යු' => 'hq',
            'යූ' => 'hQ',
            'යෙ' => 'fh',
            'යේ' => 'fha',
            'යො' => 'fhd',
            'යෝ' => 'fhda',
            'ය්' => 'ha',
            'ය' => 'h',

            'රා' => '/d',
            'රැ' => '/e',
            'රෑ' => '/E',
            'රි' => '/s',
            'රී' => '/S',
            'රු' => '/q',
            'රූ' => '/Q',
            'රෙ' => 'f/',
            'රේ' => 'f/a',
            'රො' => 'f/d',
            'රෝ' => 'f/da',
            'ර්' => '/a',
            'ර' => '/',

            'ලා' => ',d',
            'ලැ' => ',e',
            'ලෑ' => ',E',
            'ලි' => ',s',
            'ලී' => ',S',
            'ලු' => ',q',
            'ලූ' => ',Q',
            'ලෙ' => 'f,',
            'ලේ' => 'f,a',
            'ලො' => 'f,d',
            'ලෝ' => 'f,da',
            'ල්' => ',a',
            'ල' => ',',

            'වා' => 'jd',
            'වැ' => 'je',
            'වෑ' => 'jE',
            'වි' => 'js',
            'වී' => 'jS',
            'වු' => 'jq',
            'වූ' => 'jQ',
            'වෙ' => 'fj',
            'වේ' => 'fja',
            'වො' => 'fjd',
            'වෝ' => 'fjda',
            'ව්' => 'ja',
            'ව' => 'j',

            'සා' => 'id',
            'සැ' => 'ie',
            'සෑ' => 'iE',
            'සි' => 'is',
            'සී' => 'iS',
            'සු' => 'iq',
            'සූ' => 'iQ',
            'සෙ' => 'fi',
            'සේ' => 'fia',
            'සො' => 'fid',
            'සෝ' => 'fida',
            'ස්' => 'ia',
            'ස' => 'i',

            'හා' => 'yd',
            'හැ' => 'ye',
            'හෑ' => 'yE',
            'හි' => 'ys',
            'හී' => 'yS',
            'හු' => 'yq',
            'හූ' => 'yQ',
            'හෙ' => 'fy',
            'හේ' => 'fya',
            'හො' => 'fyd',
            'හෝ' => 'fyda',
            'හ්' => 'ya',
            'හ' => 'y',
        ];

        uksort($map, function ($a, $b) {
            return mb_strlen($b, 'UTF-8') - mb_strlen($a, 'UTF-8');
        });

        return str_replace(array_keys($map), array_values($map), $text);
    }

    public static function dlsaralaToUnicode($text)
    {
        if ($text === null || trim($text) === '') {
            return $text;
        }

        $map = [
            // Reverse of above map
            'flda' => 'කෝ',
            'fld' => 'කො',
            'fla' => 'කේ',
            'fl' => 'කෙ',
            'ld' => 'කා',
            'le' => 'කැ',
            'lE' => 'කෑ',
            'ls' => 'කි',
            'lS' => 'කී',
            'lq' => 'කු',
            'lQ' => 'කූ',
            'la' => 'ක්',
            'l' => 'ක',

            'f.da' => 'ගෝ',
            'f.d' => 'ගො',
            'f.a' => 'ගේ',
            'f.' => 'ගෙ',
            '.d' => 'ගා',
            '.e' => 'ගැ',
            '.E' => 'ගෑ',
            '.s' => 'ගි',
            '.S' => 'ගී',
            '.q' => 'ගු',
            '.Q' => 'ගූ',
            '.a' => 'ග්',
            '.' => 'ග',

            'fmda' => 'පෝ',
            'fmd' => 'පො',
            'fma' => 'පේ',
            'fm' => 'පෙ',
            'md' => 'පා',
            'me' => 'පැ',
            'mE' => 'පෑ',
            'ms' => 'පි',
            'mS' => 'පී',
            'mq' => 'පු',
            'mQ' => 'පූ',
            'ma' => 'ප්',
            'm' => 'ප',

            'fnda' => 'බෝ',
            'fnd' => 'බො',
            'fna' => 'බේ',
            'fn' => 'බෙ',
            'nd' => 'බා',
            'ne' => 'බැ',
            'nE' => 'බෑ',
            'ns' => 'බි',
            'nS' => 'බී',
            'nq' => 'බු',
            'nQ' => 'බූ',
            'na' => 'බ්',
            'n' => 'බ',

            'fuda' => 'මෝ',
            'fud' => 'මො',
            'fua' => 'මේ',
            'fu' => 'මෙ',
            'ud' => 'මා',
            'ue' => 'මැ',
            'uE' => 'මෑ',
            'us' => 'මි',
            'uS' => 'මී',
            'uq' => 'මු',
            'uQ' => 'මූ',
            'ua' => 'ම්',
            'u' => 'ම',

            'fida' => 'සෝ',
            'fid' => 'සො',
            'fia' => 'සේ',
            'fi' => 'සෙ',
            'id' => 'සා',
            'ie' => 'සැ',
            'iE' => 'සෑ',
            'is' => 'සි',
            'iS' => 'සී',
            'iq' => 'සු',
            'iQ' => 'සූ',
            'ia' => 'ස්',
            'i' => 'ස',

            'fja' => 'වේ',
            'fjda' => 'වෝ',
            'fjd' => 'වො',
            'fj' => 'වෙ',
            'jd' => 'වා',
            'je' => 'වැ',
            'jE' => 'වෑ',
            'js' => 'වි',
            'jS' => 'වී',
            'jq' => 'වු',
            'jQ' => 'වූ',
            'ja' => 'ව්',
            'j' => 'ව',
        ];

        uksort($map, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        return str_replace(array_keys($map), array_values($map), $text);
    }
}
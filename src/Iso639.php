<?php

declare(strict_types=1);

// SPDX-License-Identifier: AGPL-3.0-or-later

namespace Gecka\SpamFilter\Backgrounds;

/**
 * Language code conversions between the sources and the table names.
 *
 * Leipzig names corpora with ISO 639-3 codes (fra, eng, deu), FrequencyWords
 * with ISO 639-1 codes (fr, en, de) and a few regional variants. Tables are
 * named with the two-letter code when the language has one, with the
 * three-letter code otherwise.
 *
 * @author Laurent Dinclaux <laurent@gecka.nc>
 * @copyright Gecka <contact@gecka.nc>
 * @license AGPL-3.0-or-later
 */
final class Iso639
{
    /** @var array<string, string> ISO 639-3 => ISO 639-1 */
    private const TWO_LETTER = [
        'aar' => 'aa', 'abk' => 'ab', 'afr' => 'af', 'aka' => 'ak', 'amh' => 'am', 'ara' => 'ar', 'arg' => 'an',
        'asm' => 'as', 'ava' => 'av', 'aym' => 'ay', 'aze' => 'az', 'bak' => 'ba', 'bam' => 'bm', 'bel' => 'be',
        'ben' => 'bn', 'bis' => 'bi', 'bod' => 'bo', 'bos' => 'bs', 'bre' => 'br', 'bul' => 'bg', 'cat' => 'ca',
        'ces' => 'cs', 'cha' => 'ch', 'che' => 'ce', 'chv' => 'cv', 'cor' => 'kw', 'cos' => 'co', 'cre' => 'cr',
        'cym' => 'cy', 'dan' => 'da', 'deu' => 'de', 'div' => 'dv', 'dzo' => 'dz', 'ell' => 'el', 'eng' => 'en',
        'epo' => 'eo', 'est' => 'et', 'eus' => 'eu', 'ewe' => 'ee', 'fao' => 'fo', 'fas' => 'fa', 'fij' => 'fj',
        'fin' => 'fi', 'fra' => 'fr', 'fry' => 'fy', 'ful' => 'ff', 'gla' => 'gd', 'gle' => 'ga', 'glg' => 'gl',
        'glv' => 'gv', 'grn' => 'gn', 'guj' => 'gu', 'hat' => 'ht', 'hau' => 'ha', 'heb' => 'he', 'her' => 'hz',
        'hin' => 'hi', 'hmo' => 'ho', 'hrv' => 'hr', 'hun' => 'hu', 'hye' => 'hy', 'ibo' => 'ig', 'ido' => 'io',
        'iii' => 'ii', 'iku' => 'iu', 'ile' => 'ie', 'ina' => 'ia', 'ind' => 'id', 'ipk' => 'ik', 'isl' => 'is',
        'ita' => 'it', 'jav' => 'jv', 'jpn' => 'ja', 'kal' => 'kl', 'kan' => 'kn', 'kas' => 'ks', 'kat' => 'ka',
        'kau' => 'kr', 'kaz' => 'kk', 'khm' => 'km', 'kik' => 'ki', 'kin' => 'rw', 'kir' => 'ky', 'kom' => 'kv',
        'kon' => 'kg', 'kor' => 'ko', 'kua' => 'kj', 'kur' => 'ku', 'lao' => 'lo', 'lat' => 'la', 'lav' => 'lv',
        'lim' => 'li', 'lin' => 'ln', 'lit' => 'lt', 'ltz' => 'lb', 'lub' => 'lu', 'lug' => 'lg', 'mah' => 'mh',
        'mal' => 'ml', 'mar' => 'mr', 'mkd' => 'mk', 'mlg' => 'mg', 'mlt' => 'mt', 'mon' => 'mn', 'mri' => 'mi',
        'msa' => 'ms', 'mya' => 'my', 'nau' => 'na', 'nav' => 'nv', 'nbl' => 'nr', 'nde' => 'nd', 'ndo' => 'ng',
        'nep' => 'ne', 'nld' => 'nl', 'nno' => 'nn', 'nob' => 'nb', 'nor' => 'no', 'nya' => 'ny', 'oci' => 'oc',
        'oji' => 'oj', 'ori' => 'or', 'orm' => 'om', 'oss' => 'os', 'pan' => 'pa', 'pli' => 'pi', 'pol' => 'pl',
        'por' => 'pt', 'pus' => 'ps', 'que' => 'qu', 'roh' => 'rm', 'ron' => 'ro', 'run' => 'rn', 'rus' => 'ru',
        'sag' => 'sg', 'san' => 'sa', 'sin' => 'si', 'slk' => 'sk', 'slv' => 'sl', 'sme' => 'se', 'smo' => 'sm',
        'sna' => 'sn', 'snd' => 'sd', 'som' => 'so', 'sot' => 'st', 'spa' => 'es', 'sqi' => 'sq', 'srd' => 'sc',
        'srp' => 'sr', 'ssw' => 'ss', 'sun' => 'su', 'swa' => 'sw', 'swe' => 'sv', 'tah' => 'ty', 'tam' => 'ta',
        'tat' => 'tt', 'tel' => 'te', 'tgk' => 'tg', 'tgl' => 'tl', 'tha' => 'th', 'tir' => 'ti', 'ton' => 'to',
        'tsn' => 'tn', 'tso' => 'ts', 'tuk' => 'tk', 'tur' => 'tr', 'twi' => 'tw', 'uig' => 'ug', 'ukr' => 'uk',
        'urd' => 'ur', 'uzb' => 'uz', 'ven' => 've', 'vie' => 'vi', 'vol' => 'vo', 'wln' => 'wa', 'wol' => 'wo',
        'xho' => 'xh', 'yid' => 'yi', 'yor' => 'yo', 'zha' => 'za', 'zho' => 'zh', 'zul' => 'zu',
        // Macrolanguage members Leipzig uses in place of the macrolanguage
        'cmn' => 'zh', 'ekk' => 'et', 'lvs' => 'lv', 'zsm' => 'ms', 'pes' => 'fa', 'npi' => 'ne', 'ory' => 'or',
        'plt' => 'mg', 'swh' => 'sw', 'uzn' => 'uz', 'azj' => 'az', 'khk' => 'mn', 'ydd' => 'yi', 'als' => 'sq',
        'hbs' => 'sh', 'arb' => 'ar', 'ckb' => 'ku', 'kmr' => 'ku', 'nds' => 'nds',
    ];

    /** @var array<string, string> Table code => FrequencyWords directory, when the two differ */
    private const FREQUENCY_WORDS = [
        'zh' => 'zh_cn',
        'nb' => 'no',
        'nn' => 'no',
        'sh' => 'sr',
    ];

    /**
     * The code a table is named with: two letters when the language has an
     * ISO 639-1 code, the Leipzig three-letter code otherwise.
     */
    public static function tableCode(string $iso3): string
    {
        return self::TWO_LETTER[$iso3] ?? $iso3;
    }

    /**
     * The FrequencyWords directory holding the word list for a table code.
     *
     * @param list<string> $available Directories present in FrequencyWords
     */
    public static function frequencyWords(string $tableCode, array $available): ?string
    {
        $candidate = self::FREQUENCY_WORDS[$tableCode] ?? $tableCode;

        return in_array($candidate, $available, true) ? $candidate : null;
    }
}

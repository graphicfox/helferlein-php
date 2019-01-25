<?php
/**
 * User: Martin Neundorfer
 * Date: 20.01.2019
 * Time: 23:12
 * Vendor: LABOR.digital
 */

namespace Labor\Helferlein\Php;


class Inflector
{
	/**
	 * Default map of accented and special characters to ASCII characters
	 *
	 * @var array
	 */
	const TRANSLITERATIONS = array(
		'/À|Á|Â|Ã|Å|Ǻ|Ā|Ă|Ą|Ǎ/'           => 'A',
		'/Æ|Ǽ/'                           => 'AE',
		'/Ä/'                             => 'Ae',
		'/Ç|Ć|Ĉ|Ċ|Č/'                     => 'C',
		'/Ð|Ď|Đ/'                         => 'D',
		'/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/'             => 'E',
		'/Ĝ|Ğ|Ġ|Ģ|Ґ/'                     => 'G',
		'/Ĥ|Ħ/'                           => 'H',
		'/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ|І/'         => 'I',
		'/Ĳ/'                             => 'IJ',
		'/Ĵ/'                             => 'J',
		'/Ķ/'                             => 'K',
		'/Ĺ|Ļ|Ľ|Ŀ|Ł/'                     => 'L',
		'/Ñ|Ń|Ņ|Ň/'                       => 'N',
		'/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/'         => 'O',
		'/Œ/'                             => 'OE',
		'/Ö/'                             => 'Oe',
		'/Ŕ|Ŗ|Ř/'                         => 'R',
		'/Ś|Ŝ|Ş|Ș|Š/'                     => 'S',
		'/ẞ/'                             => 'SS',
		'/Ţ|Ț|Ť|Ŧ/'                       => 'T',
		'/Þ/'                             => 'TH',
		'/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
		'/Ü/'                             => 'Ue',
		'/Ŵ/'                             => 'W',
		'/Ý|Ÿ|Ŷ/'                         => 'Y',
		'/Є/'                             => 'Ye',
		'/Ї/'                             => 'Yi',
		'/Ź|Ż|Ž/'                         => 'Z',
		'/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/'         => 'a',
		'/ä|æ|ǽ/'                         => 'ae',
		'/ç|ć|ĉ|ċ|č/'                     => 'c',
		'/ð|ď|đ/'                         => 'd',
		'/è|é|ê|ë|ē|ĕ|ė|ę|ě/'             => 'e',
		'/ƒ/'                             => 'f',
		'/ĝ|ğ|ġ|ģ|ґ/'                     => 'g',
		'/ĥ|ħ/'                           => 'h',
		'/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı|і/'         => 'i',
		'/ĳ/'                             => 'ij',
		'/ĵ/'                             => 'j',
		'/ķ/'                             => 'k',
		'/ĺ|ļ|ľ|ŀ|ł/'                     => 'l',
		'/ñ|ń|ņ|ň|ŉ/'                     => 'n',
		'/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/'       => 'o',
		'/ö|œ/'                           => 'oe',
		'/ŕ|ŗ|ř/'                         => 'r',
		'/ś|ŝ|ş|ș|š|ſ/'                   => 's',
		'/ß/'                             => 'ss',
		'/ţ|ț|ť|ŧ/'                       => 't',
		'/þ/'                             => 'th',
		'/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
		'/ü/'                             => 'ue',
		'/ŵ/'                             => 'w',
		'/ý|ÿ|ŷ/'                         => 'y',
		'/є/'                             => 'ye',
		'/ї/'                             => 'yi',
		'/ź|ż|ž/'                         => 'z',
	);
	
	/**
	 * A list of filenames we know and recognize as such
	 */
	const FILE_EXTENSIONS = '|3dm|3ds|3g2|3gp|7z|accdb|ai|aif|apk|app|asf|asp|aspx|avi|bak|bat|bin|bmp|c|cab|cbr|cer|cfg|cfm|cgi|class|com|cpl|cpp|crdownload|crx|cs|csr|css|csv|cue|cur|dat|db|dbf|dds|deb|dem|deskthemepack|dll|dmg|dmp|doc|docx|drv|dtd|dwg|dxf|eps|exe|fla|flv|fnt|fon|gadget|gam|ged|gif|gpx|gz|h|hqx|htm|html|icns|ico|ics|iff|indd|ini|iso|jar|java|jpg|jpeg|js|jsp|key|keychain|kml|kmz|lnk|log|lua|m|m3u|m4a|m4v|max|mdb|mdf|mid|mim|mov|mp3|mp4|mpa|mpg|msg|msi|nes|obj|odt|otf|pages|part|pct|pdb|pdf|php|pkg|pl|plugin|png|pps|ppt|pptx|prf|ps|psd|pspimage|py|rar|rm|rom|rpm|rss|rtf|sav|sdf|sh|sitx|sln|sql|srt|svg|swf|swift|sys|tar|tar.gz|tax2016|tex|tga|thm|tif|tiff|tmp|toast|torrent|ttf|txt|uue|vb|vcd|vcf|vcxproj|vob|wav|wma|wmv|wpd|wps|wsf|xcodeproj|xhtml|xlr|xls|xlsx|xml|yuv|zip|zipx|';
	
	/**
	 * Converts a "Given string" to "Given-string" or
	 * "another.String-you wouldWant" to "another-String-you-would-Want".
	 * But in addition to that, it will convert "Annahäuser_Römertopf.jpg" into "Annahaeuser-Roemertopf-jpg"
	 *
	 * NOTE: Yes, this is a shameless copy of
	 * http://book.cakephp.org/2.0/en/core-utility-libraries/inflector.html#Inflector::slug
	 * But it works remarkably well!
	 *
	 * @param string $string The string to inflect
	 *
	 * @return string
	 */
	public static function toSlug(string $string): string
	{
		$map = self::TRANSLITERATIONS + array(
				'/[^\s\p{Zs}\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
				'/[\s\p{Zs}]+/mu'                                     => '-',
				sprintf('/^[%s]+|[%s]+$/', '\\-', '\\-')              => '',
			);
		return (string)preg_replace(array_keys($map), array_values($map), $string);
	}
}
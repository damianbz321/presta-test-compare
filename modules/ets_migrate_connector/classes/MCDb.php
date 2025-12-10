<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!function_exists('bqSQL')) {
    require_once(_PS_MODULE_DIR_ . 'ets_migrate_connector/backward_compatibility/alias.php');
}
if (version_compare(_PS_VERSION_, '1.5.0.0', '<')) {
    require_once _PS_MODULE_DIR_ . 'ets_migrate_connector/classes/DbQuery.php';
}
require_once _PS_MODULE_DIR_ . 'ets_migrate_connector/classes/MCDefines.php';

class MCDb
{
    static $_INSTANCE;
    static $image_field_unique = '_img_exist';
    static $file_field_unique = '_file_exist';

    private $ignore_cart = 0;
    private $multi_shops = array();
    private $images = array();
    private $files = array();
    private $ps_version;

    /**
     * @return mixed
     */
    public function getPsVersion()
    {
        return $this->ps_version;
    }

    /**
     * @param mixed $ps_version
     */
    public function setPsVersion($ps_version)
    {
        $this->ps_version = $ps_version;
    }

    public function __construct()
    {
    }

    public static function getInstance()
    {
        if (!self::$_INSTANCE) {
            self::$_INSTANCE = new MCDb();
        }
        return self::$_INSTANCE;
    }

    public function fetchGroups($tables, $offset = 0, $limit = 0)
    {
        if (!is_array($tables)) {
            $tables = array($tables);
        }
        if (count($tables) > 0) {
            $count = 0;
            $result = array();
            while ($count < $limit && count($tables) > 0) {
                $table = array_shift($tables);
                if (trim($table) !== '') {
                    $res = $this->fetch($table, 0, $offset, $limit - $count, 1);
                    $count += is_array($res) ? count($res) : 0;
                    $result[$table] = array(
                        'data' => $res,
                        'end' => $count < $limit ? 1 : 0,
                    );
                    $offset = 0;
                }
            }
            if (!count($result)) {
                $this->toOutput(array(
                    'limit' => $limit
                ), false);
            }
            $this->toOutput($result, false);
        } else {
            $this->toOutput(array(
                'empty' => true,
            ), false);
        }
    }

    public function fetch($table, $nb_record = 0, $offset = 0, $limit = 0, $ret = 0, $fields = array())
    {
        if ($this->tableExist($table)) {

            $schema = $this->getSchema($table);
            $struct_images = $struct_files = array();
            $has_break = true;

            $qd = new DbQuery();
            $qd
                ->from($table, 'a');
            if ($nb_record) {
                $qd->select('COUNT(*)');
            } else {
                $qd->select('a.*');
                if (in_array($table, $this->images)
                    && ($struct_images = MCDefines::getInstance()->getStructImages($table))
                    && is_array($struct_images)
                    && count($struct_images) > 0
                ) {
                    foreach ($struct_images as $struct) {
                        if (is_array($struct)
                            && count($struct) > 0
                        ) {
                            if (isset($struct['field']) && trim($struct['field']) !== '') {
                                $qd->select('NULL as `' . pSQL($struct['field'] . self::$image_field_unique) . '`');
                            } else {
                                $qd->select('NULL as `' . pSQL('id_' . $table . self::$image_field_unique) . '`');
                            }
                        }
                    }
                    $has_break = false;
                }
                if (in_array($table, $this->files)
                    && ($struct_files = MCDefines::getInstance()->getStructFiles($table))
                    && is_array($struct_files)
                    && count($struct_files) > 0
                ) {
                    foreach ($struct_files as $struct) {
                        if (is_array($struct)
                            && count($struct) > 0
                        ) {
                            if (isset($struct['field']) && trim($struct['field']) !== '') {
                                $qd->select('NULL as `' . pSQL($struct['field'] . self::$file_field_unique) . '`');
                            } else {
                                $qd->select('NULL as `' . pSQL('id_' . $table . self::$file_field_unique) . '`');
                            }
                        }
                    }
                    $has_break = false;
                }
                if (trim($table) == 'currency'
                    && version_compare(_PS_VERSION_, '1.7.6.0', '>')
                    && version_compare($this->ps_version, '1.7.6.0', '<=')
                ) {
                    $qd
                        ->select('cl.name, cl.symbol, cl.symbol as `sign`')
                        ->leftJoin('currency_lang', 'cl', 'a.id_currency = cl.id_currency AND cl.id_lang=' . (int)Configuration::get('PS_LANG_DEFAULT'));
                }
            }
            // Check multi-shop:
            if (is_array($this->multi_shops)
                && count($this->multi_shops) > 0
                && isset($schema['id_shop'])
            ) {
                if (!$nb_record) {
                    $qd
                        ->select('IF(s.id_shop is NOT NULL, s.id_shop, a.id_shop) `id_shop`');
                }
                $qd
                    ->leftJoin('shop', 's', 's.id_shop = a.id_shop')
                    ->where('a.id_shop = 0 OR s.id_shop IN (' . implode(',', $this->multi_shops) . ')');
            }
            if (isset($this->multi_shops) && count($this->multi_shops) > 0 && $this->tableExist($table.'_shop')) {
                $qd
                    ->leftJoin($table.'_shop', 'ss', 'ss.id_'.$table.' = a.id_'.$table)
                    ->where('ss.id_shop IN (' . implode(',', $this->multi_shops) . ')');
            }
            // Get iso_code lang:
            if (isset($schema['id_lang'])) {
                if (!$nb_record) {
                    $qd
                        ->select('l.iso_code');
                }
                $qd
                    ->leftJoin('lang', 'l', 'a.id_lang = l.id_lang');
            }
            // Ignore cart:
            if (trim($table) == 'cart'
                && $this->ignore_cart > 0
            ) {
                $qd
                    ->leftJoin('cart_product', 'cp', 'cp.id_cart = a.id_cart')
                    ->where('cp.id_cart is NOT NULL')
                    ->where('cp.id_product > 0');
            }
            if (is_array($fields) && count($fields) > 0) {
                foreach ($fields as $field) {
                    $qd
                        ->where(trim($field));
                }
            }
            // Return number of record:
            if ($nb_record) {
                return $this->toOutput(array(
                    'nb' => Db::getInstance()->getValue($qd)
                ), $ret);
            }

            $qd
                ->limit($limit, $offset);
            $data = Db::getInstance()->executeS($qd);

            if (is_array($data) && count($data) > 0 && !$has_break) {
                foreach ($data as &$row) {
                    // Images:
                    if (in_array($table, $this->images)
                        && is_array($struct_images)
                        && count($struct_images) > 0
                    ) {
                        foreach ($struct_images as $struct) {
                            if (is_array($struct)
                                && count($struct) > 0
                            ) {
                                $path_image = isset($struct['path']) && trim($struct['path']) !== '' ? rtrim($struct['path'], '/') . '/' : '';
                                $field = isset($struct['field']) && trim($struct['field']) !== '' ? trim($struct['field']) : 'id_' . $table;
                                $extension = isset($struct['ext']) && trim($struct['ext']) !== '' ? trim($struct['ext']) : '';
                                if (isset($row[$field])
                                    && trim($row[$field]) !== ''
                                ) {
                                    if (trim($table) !== 'image') {
                                        if (@file_exists($path_image . $row[$field] . $extension)) {
                                            $row[$field . self::$image_field_unique] = 0;
                                        }
                                    } else {
                                        $path_create = $this->getImgFolderStatic($row[$field]);
                                        if (@file_exists($path_image . $path_create . $row[$field] . $extension)) {
                                            $row[$field . self::$image_field_unique] = 1;
                                        } elseif (@file_exists($path_image . $row['id_product'] . '-' . $row[$field] . $extension)) {
                                            $row[$field . self::$image_field_unique] = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // Attachments & Files:
                    if (in_array($table, $this->files)
                        && is_array($struct_files)
                        && count($struct_files) > 0
                    ) {
                        foreach ($struct_files as $struct) {
                            if (is_array($struct)
                                && count($struct) > 0
                            ) {
                                $path_file = isset($struct['path']) && trim($struct['path']) !== '' ? rtrim($struct['path'], '/') . '/' : '';
                                $field = isset($struct['field']) && trim($struct['field']) !== '' ? trim($struct['field']) : 'id_' . trim($table);
                                $extension = isset($struct['ext']) && trim($struct['ext']) !== '' ? trim($struct['ext']) : '';
                                if (isset($row[$field])
                                    && trim($row[$field]) !== ''
                                    && (trim($table) !== 'customized_data' || isset($row['type']) && (int)$row['type'] === 0)
                                    && @file_exists($path_file . $row[$field] . $extension)
                                ) {
                                    $row[$field . self::$file_field_unique] = 0;
                                }
                            }
                        }
                    }
                }
            }

            return $this->toOutput($data, $ret);
        }
        if ($nb_record) {
            return $this->toOutput(array(
                'nb' => 0
            ), $ret);
        }

        return array();
    }

    public function downloadFile($filename, $field, $entity)
    {
        $struct_file = MCDefines::getInstance()->getStructFiles($entity);
        foreach ($struct_file as $struct) {
            if (isset($struct['path'])
                && trim($struct['path']) !== ''
                && (isset($struct['field']) && trim($field) == trim($struct['field']) || !isset($struct['field']) && trim($field) == 'id_' . trim($entity))
            ) {
                if (trim($filename) == '' ||
                    !Validate::isFileName($filename)
                ) {
                    echo $this->l('**(error)**: File name invalid');
                    exit;
                }
                $file = rtrim($struct['path'], '/') . '/' . $filename;
                if (!is_file($file)) {
                    echo $this->l('**(error)**: File type file invalid');
                    exit;
                }
                /* Detect mime content type */
                $mimeType = false;
                if (function_exists('finfo_open')) {
                    $finfo = @finfo_open(FILEINFO_MIME);
                    $mimeType = @finfo_file($finfo, $file);
                    @finfo_close($finfo);
                } elseif (function_exists('mime_content_type')) {
                    $mimeType = @mime_content_type($file);
                } elseif (function_exists('exec')) {
                    $mimeType = trim(@exec('file -b --mime-type ' . escapeshellarg($file)));
                    if (!$mimeType) {
                        $mimeType = trim(@exec('file --mime ' . escapeshellarg($file)));
                    }
                    if (!$mimeType) {
                        $mimeType = trim(@exec('file -bi ' . escapeshellarg($file)));
                    }
                }
                if (empty($mimeType)) {
                    $bName = basename($filename);
                    $bName = explode('.', $bName);
                    $bName = strtolower($bName[count($bName) - 1]);

                    $mimeTypes = array(
                        'ez' => 'application/andrew-inset',
                        'hqx' => 'application/mac-binhex40',
                        'cpt' => 'application/mac-compactpro',
                        'doc' => 'application/msword',
                        'oda' => 'application/oda',
                        'pdf' => 'application/pdf',
                        'ai' => 'application/postscript',
                        'eps' => 'application/postscript',
                        'ps' => 'application/postscript',
                        'smi' => 'application/smil',
                        'smil' => 'application/smil',
                        'wbxml' => 'application/vnd.wap.wbxml',
                        'wmlc' => 'application/vnd.wap.wmlc',
                        'wmlsc' => 'application/vnd.wap.wmlscriptc',
                        'bcpio' => 'application/x-bcpio',
                        'vcd' => 'application/x-cdlink',
                        'pgn' => 'application/x-chess-pgn',
                        'cpio' => 'application/x-cpio',
                        'csh' => 'application/x-csh',
                        'dcr' => 'application/x-director',
                        'dir' => 'application/x-director',
                        'dxr' => 'application/x-director',
                        'dvi' => 'application/x-dvi',
                        'spl' => 'application/x-futuresplash',
                        'gtar' => 'application/x-gtar',
                        'hdf' => 'application/x-hdf',
                        'js' => 'application/x-javascript',
                        'skp' => 'application/x-koan',
                        'skd' => 'application/x-koan',
                        'skt' => 'application/x-koan',
                        'skm' => 'application/x-koan',
                        'latex' => 'application/x-latex',
                        'nc' => 'application/x-netcdf',
                        'cdf' => 'application/x-netcdf',
                        'sh' => 'application/x-sh',
                        'shar' => 'application/x-shar',
                        'swf' => 'application/x-shockwave-flash',
                        'sit' => 'application/x-stuffit',
                        'sv4cpio' => 'application/x-sv4cpio',
                        'sv4crc' => 'application/x-sv4crc',
                        'tar' => 'application/x-tar',
                        'tcl' => 'application/x-tcl',
                        'tex' => 'application/x-tex',
                        'texinfo' => 'application/x-texinfo',
                        'texi' => 'application/x-texinfo',
                        't' => 'application/x-troff',
                        'tr' => 'application/x-troff',
                        'roff' => 'application/x-troff',
                        'man' => 'application/x-troff-man',
                        'me' => 'application/x-troff-me',
                        'ms' => 'application/x-troff-ms',
                        'ustar' => 'application/x-ustar',
                        'src' => 'application/x-wais-source',
                        'xhtml' => 'application/xhtml+xml',
                        'xht' => 'application/xhtml+xml',
                        'zip' => 'application/zip',
                        'au' => 'audio/basic',
                        'snd' => 'audio/basic',
                        'mid' => 'audio/midi',
                        'midi' => 'audio/midi',
                        'kar' => 'audio/midi',
                        'mpga' => 'audio/mpeg',
                        'mp2' => 'audio/mpeg',
                        'mp3' => 'audio/mpeg',
                        'aif' => 'audio/x-aiff',
                        'aiff' => 'audio/x-aiff',
                        'aifc' => 'audio/x-aiff',
                        'm3u' => 'audio/x-mpegurl',
                        'ram' => 'audio/x-pn-realaudio',
                        'rm' => 'audio/x-pn-realaudio',
                        'rpm' => 'audio/x-pn-realaudio-plugin',
                        'ra' => 'audio/x-realaudio',
                        'wav' => 'audio/x-wav',
                        'pdb' => 'chemical/x-pdb',
                        'xyz' => 'chemical/x-xyz',
                        'bmp' => 'image/bmp',
                        'gif' => 'image/gif',
                        'ief' => 'image/ief',
                        'jpeg' => 'image/jpeg',
                        'jpg' => 'image/jpeg',
                        'jpe' => 'image/jpeg',
                        'png' => 'image/png',
                        'tiff' => 'image/tiff',
                        'tif' => 'image/tif',
                        'djvu' => 'image/vnd.djvu',
                        'djv' => 'image/vnd.djvu',
                        'wbmp' => 'image/vnd.wap.wbmp',
                        'ras' => 'image/x-cmu-raster',
                        'pnm' => 'image/x-portable-anymap',
                        'pbm' => 'image/x-portable-bitmap',
                        'pgm' => 'image/x-portable-graymap',
                        'ppm' => 'image/x-portable-pixmap',
                        'rgb' => 'image/x-rgb',
                        'xbm' => 'image/x-xbitmap',
                        'xpm' => 'image/x-xpixmap',
                        'xwd' => 'image/x-windowdump',
                        'igs' => 'model/iges',
                        'iges' => 'model/iges',
                        'msh' => 'model/mesh',
                        'mesh' => 'model/mesh',
                        'silo' => 'model/mesh',
                        'wrl' => 'model/vrml',
                        'vrml' => 'model/vrml',
                        'css' => 'text/css',
                        'html' => 'text/html',
                        'htm' => 'text/html',
                        'asc' => 'text/plain',
                        'txt' => 'text/plain',
                        'rtx' => 'text/richtext',
                        'rtf' => 'text/rtf',
                        'sgml' => 'text/sgml',
                        'sgm' => 'text/sgml',
                        'tsv' => 'text/tab-seperated-values',
                        'wml' => 'text/vnd.wap.wml',
                        'wmls' => 'text/vnd.wap.wmlscript',
                        'etx' => 'text/x-setext',
                        'xml' => 'text/xml',
                        'xsl' => 'text/xml',
                        'mpeg' => 'video/mpeg',
                        'mpg' => 'video/mpeg',
                        'mpe' => 'video/mpeg',
                        'qt' => 'video/quicktime',
                        'mov' => 'video/quicktime',
                        'mxu' => 'video/vnd.mpegurl',
                        'avi' => 'video/x-msvideo',
                        'movie' => 'video/x-sgi-movie',
                        'ice' => 'x-conference-xcooltalk',
                    );

                    if (isset($mimeTypes[$bName])) {
                        $mimeType = $mimeTypes[$bName];
                    } else {
                        $mimeType = 'application/octet-stream';
                    }
                }
                header('Content-Transfer-Encoding: binary');
                header('Content-Type: ' . $mimeType);
                header('Content-Length: ' . filesize($file));
                header('Content-Disposition: attachment; filename="' . $filename . '"');

                //prevents max execution timeout, when reading large files
                @set_time_limit(0);
                $fp = fopen($file, 'rb');

                if ($fp && is_resource($fp)) {
                    while (!feof($fp)) {
                        echo fgets($fp, 16384);
                    }
                }

                exit;
            }
        }
    }

    public function tableExist($table)
    {
        return Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . bqSQL($table) . '"');
    }

    public function columnExist($table, $column)
    {
        return self::tableExist($table) && Db::getInstance()->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . bqSQL($table) . '` LIKE \'' . pSQL($column) . '\'');
    }

    public function toOutput($data, $ret = 0)
    {
        if ($ret) {
            return $data;
        } else {
            ob_end_clean();
            header('Content-Type: application/json');
            die($this->safeJsonEncode($data));
        }
    }

    public function safeJsonEncode($value)
    {
        $encoded = json_encode($value);
        if (function_exists('json_last_error')) {
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    return $encoded;
                case JSON_ERROR_DEPTH:
                    return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
                case JSON_ERROR_STATE_MISMATCH:
                    return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
                case JSON_ERROR_CTRL_CHAR:
                    return 'Unexpected control character found';
                case JSON_ERROR_SYNTAX:
                    return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
                case JSON_ERROR_UTF8:
                    $value['json_error_utf8'] = 1;
                    $clean = $this->encodeUtf8ize($value);
                    return $this->safeJsonEncode($clean);
                default:
                    return 'Unknown error'; // or trigger_error() or throw new Exception()
            }
        } else
            return $encoded;
    }

    public function encodeUtf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = $this->encodeUtf8ize($value);
            }
        } else if (is_string($mixed)) {
            return mb_convert_encoding($mixed, 'UTF-8', 'auto');
        }
        return $mixed;
    }

    public function getSchema($table)
    {
        // Get struct:
        $schema = Db::getInstance()->executeS('DESCRIBE `' . _DB_PREFIX_ . bqSQL($table) . '`');
        $fields = array();
        if ($schema) {
            foreach ($schema as $field) {
                $fields[$field['Field']] = $field;
            }
        }

        return $fields;
    }

    public function getInfos($tables)
    {
        if (!is_array($tables)) {
            $tables = json_decode($tables, true);
        }
        $infos = array(
            'ps_version' => _PS_VERSION_,
            'cookie_key' => _COOKIE_KEY_,
            'ps_root_dir' => _PS_ROOT_DIR_,
            'images' => 0,
            'files' => 0,
            'lang_default' => Configuration::get('PS_LANG_DEFAULT'),
            'ps_customer_group' => Configuration::get('PS_CUSTOMER_GROUP'),
            'ps_unidentified_group' => Configuration::get('PS_UNIDENTIFIED_GROUP'),
            'ps_guest_group' => Configuration::get('PS_GUEST_GROUP'),
        );
        if ($tables) {
            foreach ($tables as $parent_table => &$item) {
                // table parent
                if (isset($item['parent'])
                    && is_array($item['parent'])
                    && count($item['parent']) > 0
                ) {
                    $item['nb'] = 0;
                    foreach ($item['parent'] as $parent) {
                        $res = $parent != 'minor_data' ? $this->fetch($parent, 1, 0, 0, 1) : false;
                        $item['nb'] += ($res && isset($res['nb']) ? (int)$res['nb'] : 0);
                    }
                } else {
                    $res = $parent_table != 'minor_data' ? $this->fetch($parent_table, 1, 0, 0, 1) : false;
                    $item['nb'] = ($res && isset($res['nb']) ? (int)$res['nb'] : 0);
                }
                if (isset($item['parent'])) {
                    unset($item['parent']);
                }
                // Images:
                if (isset($item['images'])
                    && is_array($images = $item['images'])
                    && count($images) > 0
                ) {
                    foreach ($images as $table => $struct) {
                        if (is_array($struct)
                            && count($struct) > 0
                        ) {
                            $fields = array();
                            foreach ($struct as $st) {
                                if (isset($st['field']) && trim($st['field']) !== '') {
                                    if (!MCDb::getInstance()->columnExist($table, trim($st['field']))) {
                                        $fields = array();
                                        break;
                                    }
                                    $fields[] = trim($st['field']) . ' is NOT NULL';
                                } else {
                                    $fields[] = 'id_' . trim($table) . '>0';
                                }
                            }
                            if (count($fields) > 0) {
                                $res = $this->fetch($table, 1, 0, 0, 1, array(implode(' OR ', $fields)));
                                $infos['images'] += isset($res['nb']) ? (int)$res['nb'] : 0;
                            }
                        }

                    }
                    unset($item['images']);
                }
                // Attachments & Files
                if (isset($item['files'])
                    && is_array($files = $item['files'])
                    && count($files) > 0
                ) {
                    foreach ($files as $table => $struct) {
                        if (is_array($struct)
                            && count($struct) > 0
                        ) {
                            $fields = array();
                            foreach ($struct as $st) {
                                if (isset($st['field']) && trim($st['field']) !== '') {
                                    if (!MCDb::getInstance()->columnExist($table, trim($st['field']))) {
                                        $fields = array();
                                        break;
                                    }
                                    $fields[] = trim($st['field']) . ' is NOT NULL';
                                } else {
                                    $fields[] = 'id_' . trim($table) . '>0';
                                }
                            }
                            if (count($fields) > 0) {
                                $res = $this->fetch($table, 1, 0, 0, 1, array(implode(' OR ', $fields), trim($table) === 'customized_data' ? 'type=0' : ''));
                                $infos['files'] += isset($res['nb']) ? (int)$res['nb'] : 0;
                            }
                        }
                    }
                    unset($item['files']);
                }
                // table group
                if (isset($item['tables']) && is_array(($group_tables = $item['tables'])) && count($group_tables) > 0) {
                    $item['nb_group_table'] = 0;
                    foreach ($group_tables as $table) {
                        if (trim($table) !== '') {
                            $res = $this->fetch($table, 1, 0, 0, 1);
                            if (isset($res['nb']) && (int)$res['nb'] > 0) {
                                $item['nb_group_table'] += (int)$res['nb'];
                            }
                        }
                    }
                }
                // Clean name & tables
                if (isset($item['tables'])) {
                    unset($item['tables']);
                }
                if (isset($item['name'])) {
                    unset($item['name']);
                }
            }
        }
        $infos['nb'] = $tables;
        $infos['source_shops'] = Shop::getShops(false);
        $infos['languages'] = Language::getLanguages(false);

        $this->toOutput($infos);
    }

    public static function getImgFolderStatic($idImage)
    {
        if (!is_numeric($idImage)) {
            return false;
        }
        $folders = str_split((string)$idImage);

        return implode('/', $folders) . '/';
    }

    /**
     * @param int $ignore_cart
     */
    public function setIgnoreCart($ignore_cart)
    {
        $this->ignore_cart = $ignore_cart;
    }

    /**
     * @param array $multi_shops
     */
    public function setMultiShops($multi_shops)
    {
        $this->multi_shops = $multi_shops;
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param array $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param array $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }
}
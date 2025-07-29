<?php namespace Application\Block\CouponPromoBlock;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\File\File;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{

    protected $btTable = 'btCouponPromoBlock';
    protected $btExportTables = ['btCouponPromoBlock', 'btCouponPromoBlockEntries'];
    protected $btExportFileColumns = ['image'];
    protected $btInterfaceWidth = '1000';
    protected $btInterfaceHeight = '650';
    protected $btWrapperClass = 'ccm-ui';
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputLifetime = 0;

    protected $btDefaultSet = ''; // basic, navigation, form, express, social, multimedia

    protected $settings;

    protected $image_defaultRepeatableThumbnailWidth = 480;
    protected $image_defaultRepeatableThumbnailHeight = 270;
    protected $image_defaultRepeatableFullscreenWidth = 1920;
    protected $image_defaultRepeatableFullscreenHeight = 1080;

    private $uniqueID;

    public function getBlockTypeName() {
        return t('Coupon Promo Block');
    }

    public function getBlockTypeDescription() {
        return t('Coupon Item ภายใน 1 Block จะสามารถเพิ่มคูปองได้หลายรายการ');
    }

    public function getSearchableContent() {

        $content = [];

        $entries = $this->getEntries('edit');
        foreach ($entries as $entry) {
            $content[] = $entry['titleBadge'];
            $content[] = $entry['mainText'];
            $content[] = $entry['buttonText'];
        }

        return implode(' ', $content);

    }

    public function on_start() {

        // Unique identifier
        $this->uniqueID = $this->app->make('helper/validation/identifier')->getString(18);
        $this->set('uniqueID', $this->uniqueID);

        // Settings tab
        $this->settings = is_array($this->settings) ? $this->settings : json_decode($this->settings, true);
        $this->set('settings', $this->settings);

        // Entry / IsImageRight (isImageRight) options
        $entry_isImageRight_options        = [];
        $entry_isImageRight_options[]      = '----';
        $entry_isImageRight_options['no']  = t('No');
        $entry_isImageRight_options['yes'] = t('Yes');

        $this->set('entry_isImageRight_options', $entry_isImageRight_options);

    }

    public function add() {

        $this->addEdit();
        $this->set('entries', []);

    }

    public function edit() {

        $this->addEdit();

        // Get entries
        $entries = $this->getEntries('edit');
        $this->set('entries', $entries);

    }

    public function addEdit() {

        // Load assets for repeatable entries
        $this->requireAsset('core/file-manager');

        // Get entry column names
        $entryColumnNames = $this->getEntryColumnNames();

        // Image (image) - Fields that don't exist in database, but are required in repeatable entry (image)
        $entryColumnNames[] = 'image_show_additional_fields';
        $entryColumnNames[] = 'image_override_dimensions';
        $entryColumnNames[] = 'image_custom_width';
        $entryColumnNames[] = 'image_custom_height';
        $entryColumnNames[] = 'image_custom_crop';
        $entryColumnNames[] = 'image_override_fullscreen_dimensions';
        $entryColumnNames[] = 'image_custom_fullscreen_width';
        $entryColumnNames[] = 'image_custom_fullscreen_height';
        $entryColumnNames[] = 'image_custom_fullscreen_crop';

        $this->set('entryColumnNames', $entryColumnNames);

        // Load form.css
        $al = AssetList::getInstance();
        $al->register('css', 'coupon-promo-block/form', 'blocks/coupon_promo_block/css_files/form.css', [], false);
        $this->requireAsset('css', 'coupon-promo-block/form');

        // External link protocols
        $externalLinkProtocols = [
            'http://'  => 'http://',
            'https://' => 'https://',
            'BASE_URL' => 'BASE_URL',
            'CURRENT_PAGE' => 'CURRENT_PAGE',
            'other'    => '----'
        ];
        $this->set('externalLinkProtocols', $externalLinkProtocols);

        // Make $app available in view
        $this->set('app', $this->app);

    }

    public function view() {

        // Make $app available in view
        $this->set('app', $this->app);

        $this->set('image_defaultRepeatableThumbnailWidth', $this->image_defaultRepeatableThumbnailWidth);
        $this->set('image_defaultRepeatableThumbnailHeight', $this->image_defaultRepeatableThumbnailHeight);

        $this->set('image_defaultRepeatableFullscreenWidth', $this->image_defaultRepeatableFullscreenWidth);
        $this->set('image_defaultRepeatableFullscreenHeight', $this->image_defaultRepeatableFullscreenHeight);

        // Get entries
        $entries = $this->getEntries();
        $entries = $this->prepareEntriesForView($entries);
        $this->set('entries', $entries);

    }

    public function save($args) {

        // Settings
        if (isset($args['settings']) and $args['settings']['image_custom_crop']==='1' and (empty($args['settings']['image_custom_width']) or empty($args['settings']['image_custom_height']))) {
            $args['settings']['image_custom_crop'] = 0; // Crop should be disabled if width or height is missing
        }
        if (isset($args['settings']) and $args['settings']['image_custom_fullscreen_crop']==='1' and (empty($args['settings']['image_custom_fullscreen_width']) or empty($args['settings']['image_custom_fullscreen_height']))) {
            $args['settings']['image_custom_fullscreen_crop'] = 0; // Crop should be disabled if width or height is missing
        }
        $args['settings'] = isset($args['settings']) ? json_encode($args['settings']) : null;

        parent::save($args);

        $db = $this->app->make('database')->connection();

        // Delete existing entries of current block's version
        $db->delete('btCouponPromoBlockEntries', ['bID' => $this->bID]);

        if (isset($args['entry']) AND is_array($args['entry']) AND count($args['entry'])) {

            $i = 1;

            foreach ($args['entry'] as $entry) {

                // Prepare data for insert
                $data = [];
                $data['position']           = $i;
                $data['bID']                = $this->bID;
                $data['titleBadge']         = trim($entry['titleBadge']);
                $data['mainText']           = LinkAbstractor::translateTo($entry['mainText']);
                $data['buttonText']         = trim($entry['buttonText']);
                $data['buttonURL']          = trim($entry['buttonURL']);
                $data['buttonURL_protocol'] = trim($entry['buttonURL_protocol']);
                $data['image']              = intval($entry['image']);
                $data['image_alt']          = trim($entry['image_alt']);
                $data['backgroundColor']    = !empty($entry['backgroundColor']) ? trim($entry['backgroundColor']) : null;
                $data['isImageRight']       = !empty($entry['isImageRight']) ? trim($entry['isImageRight']) : '';

                // Image (image) - Image
                $data['image_data'] = json_encode([
                    'show_additional_fields'         => intval($entry['image_show_additional_fields']),
                    'override_dimensions'            => !empty($entry['image_override_dimensions']) ? intval($entry['image_override_dimensions']) : 0,
                    'custom_width'                   => intval($entry['image_custom_width']),
                    'custom_height'                  => intval($entry['image_custom_height']),
                    'custom_crop'                    => ($entry['image_custom_crop']==='1' and (!(bool)$entry['image_custom_width'] or !(bool)$entry['image_custom_height'])) ? false : intval($entry['image_custom_crop']), // do not crop without width and height filled
                    'override_fullscreen_dimensions' => !empty($entry['image_override_fullscreen_dimensions']) ? intval($entry['image_override_fullscreen_dimensions']) : 0,
                    'custom_fullscreen_width'        => intval($entry['image_custom_fullscreen_width']),
                    'custom_fullscreen_height'       => intval($entry['image_custom_fullscreen_height']),
                    'custom_fullscreen_crop'         => ($entry['image_custom_fullscreen_crop']==='1' and (!(bool)$entry['image_custom_fullscreen_width'] or !(bool)$entry['image_custom_fullscreen_height'])) ? false : intval($entry['image_custom_fullscreen_crop']), // do not crop without width and height filled
                ]);

                $db->insert('btCouponPromoBlockEntries', $data);

                $i++;

            }

        }

    }

    public function duplicate($newBlockID) {

        parent::duplicate($newBlockID);

        $db = $this->app->make('database')->connection();

        // Get latest entry...
        $sql = '
            SELECT
                btCouponPromoBlockEntries.*
            FROM
                btCouponPromoBlockEntries
            WHERE
                btCouponPromoBlockEntries.bID = :bID
        ';
        $parameters = [];
        $parameters['bID'] = $this->bID;

        $entries = $db->fetchAll($sql, $parameters);

        // ... and copy it
        if (is_array($entries) AND count($entries)) {
            foreach ($entries as $entry) {
                $data = [];
                foreach ($entry as $columnName => $value) {
                    $data[$columnName] = $value;
                }
                unset($data['id']);
                $data['bID'] = $newBlockID;
                $db->insert('btCouponPromoBlockEntries', $data);
            }
        }

    }

    public function delete() {

    }

    public function validate($args) {

        $error = $this->app->make('helper/validation/error');

        return $error;

    }

    public function composer() {

        $al = AssetList::getInstance();
        $al->register('javascript', 'coupon-promo-block/auto-js', 'blocks/coupon_promo_block/auto.js', [], false);
        $this->requireAsset('javascript', 'coupon-promo-block/auto-js');

        $this->edit();

    }

    public function scrapbook() {

        $this->edit();

    }

    private function getEntries($outputMethod = 'view') {

        $db = $this->app->make('database')->connection();

        $sql = '
            SELECT
                btCouponPromoBlockEntries.*
            FROM
                btCouponPromoBlockEntries
            WHERE
                btCouponPromoBlockEntries.bID = :bID
            ORDER BY
                btCouponPromoBlockEntries.position ASC
        ';
        $parameters = [];
        $parameters['bID'] = $this->bID;

        $entries = $db->fetchAll($sql, $parameters);

        $modifiedEntries = [];

        foreach ($entries as $entry) {

            $entry['mainText'] = ($outputMethod=='edit') ? LinkAbstractor::translateFromEditMode($entry['mainText']) : LinkAbstractor::translateFrom($entry['mainText']);
            $entry['image'] = (is_object(File::getByID($entry['image']))) ? $entry['image'] : 0;
            // Image (image) - Image
            $imageArray = json_decode($entry['image_data'], true);
            $entry['image_show_additional_fields']         = $imageArray['show_additional_fields'] ?? '';
            $entry['image_override_dimensions']            = $imageArray['override_dimensions'] ?? '';
            $entry['image_custom_width']                   = $imageArray['custom_width'] ?? '';
            $entry['image_custom_height']                  = $imageArray['custom_height'] ?? '';
            $entry['image_custom_crop']                    = $imageArray['custom_crop'] ?? '';
            $entry['image_override_fullscreen_dimensions'] = $imageArray['override_fullscreen_dimensions'] ?? '';
            $entry['image_custom_fullscreen_width']        = $imageArray['custom_fullscreen_width'] ?? '';
            $entry['image_custom_fullscreen_height']       = $imageArray['custom_fullscreen_height'] ?? '';
            $entry['image_custom_fullscreen_crop']         = $imageArray['custom_fullscreen_crop'] ?? '';
            
            $modifiedEntries[] = $entry;

        }

        return $modifiedEntries;

    }

     private function getEntryColumnNames() {

        $db = $this->app->make('database')->connection();

        $columns = $db->getSchemaManager()->listTableColumns('btCouponPromoBlockEntries');

        $columnNames = [];

        foreach($columns as $column) {
            $columnNames[] = $column->getName();
        }

        $key1 = array_search('id', $columnNames);
        unset($columnNames[$key1]);
        $key2 = array_search('bID', $columnNames);
        unset($columnNames[$key2]);
        $key3 = array_search('position', $columnNames);
        unset($columnNames[$key3]);

        return $columnNames;

    }

    private function prepareForViewExternalLink($type, $fields) {

        $keys = array_keys($fields);
        $linkFieldName      = $keys[0];
        $protocolFieldName  = $keys[1];
        $endingFieldName    = $keys[2];
        $textFieldName      = $keys[3];
        $titleFieldName     = $keys[4];
        $newWindowFieldName = $keys[5];
        $noFollowFieldName  = $keys[6];

        $link      = $fields[$linkFieldName];
        $protocol  = $fields[$protocolFieldName];
        $ending    = $fields[$endingFieldName];
        $text      = $fields[$textFieldName];
        $title     = $fields[$titleFieldName];
        $newWindow = !empty($fields[$newWindowFieldName]) ? 'target="_blank"' : '';
        $noFollow  = !empty($fields[$noFollowFieldName]) ? 'rel="nofollow"' : '';

        if ($type == 'view') {

            // Fields from database
            $this->set($linkFieldName, $link);
            $this->set($protocolFieldName, $protocol);
            $this->set($endingFieldName, $ending);
            $this->set($textFieldName, $text);
            $this->set($titleFieldName, $title);
            $this->set($newWindowFieldName, $newWindow);
            $this->set($noFollowFieldName, $noFollow);

            // Additional data
            if (!empty($link) AND in_array($protocol, ['http://', 'https://'])) {
                $link = $protocol.$link;
            }
            if (!empty($link) AND $protocol=='BASE_URL') {
                $separator = '';
                if (substr($link, 0, 1)!='/') {
                    $separator = '/';
                }
                $link = BASE_URL.$separator.$link;
            }
            if (!empty($link) AND $protocol=='CURRENT_PAGE') {
                $separator = '';
                if (substr($link, 0, 1)!='/') {
                    $separator = '/';
                }
                $link = Page::getCurrentPage()->getCollectionLink().$separator.$link;
            }
            $this->set($linkFieldName.'_link', $link);
            $this->set($linkFieldName.'_link_type', 'external_link');

        } elseif ($type == 'entry') {

            $entry = [];

            // Fields from database
            $entry[$linkFieldName]      = $link;
            $entry[$protocolFieldName]  = $protocol;
            $entry[$endingFieldName]    = $ending;
            $entry[$textFieldName]      = $text;
            $entry[$titleFieldName]     = $title;
            $entry[$newWindowFieldName] = $newWindow;
            $entry[$noFollowFieldName]  = $noFollow;

            // Additional data
            if (!empty($link) AND in_array($protocol, ['http://', 'https://'])) {
                $link = $protocol.$link;
            }
            if (!empty($link) AND $protocol=='BASE_URL') {
                $separator = '';
                if (substr($link, 0, 1)!='/') {
                    $separator = '/';
                }
                $link = BASE_URL.$separator.$link;
            }
            if (!empty($link) AND $protocol=='CURRENT_PAGE') {
                $separator = '';
                if (substr($link, 0, 1)!='/') {
                    $separator = '/';
                }
                $link = Page::getCurrentPage()->getCollectionLink().$separator.$link;
            }
            $entry[$linkFieldName.'_link']      = $link;
            $entry[$linkFieldName.'_link_type'] = 'external_link';

            return $entry;

        }

    }

    private function prepareForViewImage($type, $fields, $options = []) {

        // Options
        if (!is_array($options)) {
            $options = [];
        }

        $defaultOptions = [];
        $defaultOptions['fullscreen']       = false;
        $defaultOptions['fullscreenWidth']  = 1920;
        $defaultOptions['fullscreenHeight'] = 1080;
        $defaultOptions['fullscreenCrop']   = false;

        $defaultOptions['thumbnail']        = false;
        $defaultOptions['thumbnailWidth']   = 480;
        $defaultOptions['thumbnailHeight']  = 270;
        $defaultOptions['thumbnailCrop']    = true;

        $options = array_merge($defaultOptions, $options);

        // Prepare links/images
        $keys = array_keys($fields);
        $fileIDFieldName = $keys[0];
        $altFieldName    = $keys[1];

        $fileID = $fields[$fileIDFieldName];
        $alt    = $fields[$altFieldName];

        $fileObject   = false;
        $filename     = '';
        $relativePath = '';
        $fileType     = '';

        $link   = '';
        $width  = '';
        $height = '';

        $fullscreenLink   = '';
        $fullscreenWidth  = $options['fullscreenWidth'];
        $fullscreenHeight = $options['thumbnailHeight'];

        $thumbnailLink   = '';
        $thumbnailWidth  = $options['thumbnailWidth'];
        $thumbnailHeight = $options['thumbnailHeight'];

        if (!empty($fileID)) {

            $fileObject = File::getByID($fileID);

            if (is_object($fileObject)) {

                $filename     = $fileObject->getFileName();
                $fileType     = $fileObject->getType();
                $relativePath = $fileObject->getRelativePath();

                if (empty($alt)) {
                    $alt = $fileObject->getTitle();
                    $removableExtensions = ['jpg', 'jpeg', 'png', 'tiff', 'svg', 'webp'];
                    $extension = strtolower(pathinfo($alt, PATHINFO_EXTENSION));
                    if (!empty($extension) and in_array($extension, $removableExtensions)) {
                        $alt = pathinfo($alt, PATHINFO_FILENAME); // Remove extension
                        $alt = preg_replace('/ - [0-9]*$/', '', $alt); // Remove counter at the end of file name, " - 001", " - 002" and so on.
                    }
                }

                // Original image
                $link   = $fileObject->getURL();
                $width  = $fileObject->canEdit() ? $fileObject->getAttribute('width') : $options['thumbnailWidth'];
                $height = $fileObject->canEdit() ? $fileObject->getAttribute('height') : $options['thumbnailHeight'];

                if ($fileObject->canEdit()) {

                    // Fullscreen image
                    if (!empty($options['fullscreen'])) {

                        $fullscreenWidth  = $options['fullscreenWidth'];
                        $fullscreenHeight = $options['fullscreenHeight'];
                        $fullscreenCrop   = $options['fullscreenCrop'];

                        if ($fileObject->canEdit() AND (($width > $fullscreenWidth AND $fullscreenWidth!=false) OR ($height > $fullscreenHeight AND $fullscreenHeight!=false))) {

                            $fullscreen       = $this->app->make('helper/image')->getThumbnail($fileObject, $fullscreenWidth, $fullscreenHeight, $fullscreenCrop);
                            $fullscreenLink   = $fullscreen->src;
                            $fullscreenWidth  = $fullscreen->width;
                            $fullscreenHeight = $fullscreen->height;

                        } else {

                            $fullscreenLink   = $link;
                            $fullscreenWidth  = $width;
                            $fullscreenHeight = $height;

                        }

                    }

                    // Thumbnail image
                    if (!empty($options['thumbnail'])) {

                        $thumbnailWidth  = $options['thumbnailWidth'];
                        $thumbnailHeight = $options['thumbnailHeight'];
                        $thumbnailCrop   = $options['thumbnailCrop'];

                        if ($fileObject->canEdit() AND (($width > $thumbnailWidth AND $thumbnailWidth!=false) OR ($height > $thumbnailHeight AND $thumbnailHeight!=false))) {

                            $thumbnail       = $this->app->make('helper/image')->getThumbnail($fileObject, $thumbnailWidth, $thumbnailHeight, $thumbnailCrop);
                            $thumbnailLink   = $thumbnail->src;
                            $thumbnailWidth  = $thumbnail->width;
                            $thumbnailHeight = $thumbnail->height;

                        } else {

                            $thumbnailLink   = $link;
                            $thumbnailWidth  = $width;
                            $thumbnailHeight = $height;

                        }

                    }

                }

            }

        }

        if ($type == 'view') {

            // Fields from database
            $this->set($fileIDFieldName, $fileID);
            $this->set($altFieldName, $alt);

            // Additional data
            $this->set($fileIDFieldName.'_object', $fileObject);
            $this->set($fileIDFieldName.'_filename', $filename);
            $this->set($fileIDFieldName.'_type', $fileType);
            $this->set($fileIDFieldName.'_relativePath', $relativePath);

            $this->set($fileIDFieldName.'_link', $link);
            $this->set($fileIDFieldName.'_width', $width);
            $this->set($fileIDFieldName.'_height', $height);

            $this->set($fileIDFieldName.'_fullscreenLink', $fullscreenLink);
            $this->set($fileIDFieldName.'_fullscreenWidth', $fullscreenWidth);
            $this->set($fileIDFieldName.'_fullscreenHeight', $fullscreenHeight);

            $this->set($fileIDFieldName.'_thumbnailLink', $thumbnailLink);
            $this->set($fileIDFieldName.'_thumbnailWidth', $thumbnailWidth);
            $this->set($fileIDFieldName.'_thumbnailHeight', $thumbnailHeight);

        } elseif ($type == 'entry') {

            $entry = [];

            // Fields from database
            $entry[$fileIDFieldName] = $fileID;
            $entry[$altFieldName]    = $alt;

            // Additional data
            // $entry[$fileIDFieldName.'_object']    = $fileObject;
            $entry[$fileIDFieldName.'_filename']     = $filename;
            $entry[$fileIDFieldName.'_type']         = $fileType;
            $entry[$fileIDFieldName.'_relativePath'] = $relativePath;

            $entry[$fileIDFieldName.'_link']   = $link;
            $entry[$fileIDFieldName.'_width']  = $width;
            $entry[$fileIDFieldName.'_height'] = $height;

            $entry[$fileIDFieldName.'_fullscreenLink']   = $fullscreenLink;
            $entry[$fileIDFieldName.'_fullscreenWidth']  = $fullscreenWidth;
            $entry[$fileIDFieldName.'_fullscreenHeight'] = $fullscreenHeight;

            $entry[$fileIDFieldName.'_thumbnailLink']   = $thumbnailLink;
            $entry[$fileIDFieldName.'_thumbnailWidth']  = $thumbnailWidth;
            $entry[$fileIDFieldName.'_thumbnailHeight'] = $thumbnailHeight;

            return $entry;

        }

    }

    private function prepareEntriesForView($entries) {

        $entriesForView = [];

        if (is_array($entries) AND count($entries)) {

            foreach ($entries as $key => $entry) {

                // Button URL (buttonURL) - External Link
                $modifiedEntry = $this->prepareForViewExternalLink('entry', [
                    'buttonURL'            => $entry['buttonURL'],
                    'buttonURL_protocol'   => $entry['buttonURL_protocol'],
                    'buttonURL_ending'     => false,
                    'buttonURL_text'       => false,
                    'buttonURL_title'      => false,
                    'buttonURL_new_window' => false,
                    'buttonURL_no_follow'  => false
                ]);
                $entry = array_merge($entry, $modifiedEntry);

                // Image (image) - Image
                $thumbnailWidth = $this->image_defaultRepeatableThumbnailWidth;
                if (!empty($this->settings['image_override_dimensions'])) {
                    $thumbnailWidth = !empty($this->settings['image_custom_width']) ? $this->settings['image_custom_width'] : false;
                }
                if (!empty($entry['image_override_dimensions'])) {
                    $thumbnailWidth = !empty($entry['image_custom_width']) ? $entry['image_custom_width'] : false;
                }
                $thumbnailHeight = $this->image_defaultRepeatableThumbnailHeight;
                if (!empty($this->settings['image_override_dimensions'])) {
                    $thumbnailHeight = !empty($this->settings['image_custom_height']) ? $this->settings['image_custom_height'] : false;
                }
                if (!empty($entry['image_override_dimensions'])) {
                    $thumbnailHeight = !empty($entry['image_custom_height']) ? $entry['image_custom_height'] : false;
                }
                $thumbnailCrop = true;
                if (!empty($this->settings['image_override_dimensions'])) {
                    $thumbnailCrop = !empty($this->settings['image_custom_crop']) ? $this->settings['image_custom_crop'] : false;
                }
                if (!empty($entry['image_override_dimensions'])) {
                    $thumbnailCrop = !empty($entry['image_custom_crop']) ? $entry['image_custom_crop'] : false;
                }
                $fullscreenWidth = $this->image_defaultRepeatableFullscreenWidth;
                if (!empty($this->settings['image_override_fullscreen_dimensions'])) {
                    $fullscreenWidth = !empty($this->settings['image_custom_fullscreen_width']) ? $this->settings['image_custom_fullscreen_width'] : false;
                }
                if (!empty($entry['image_override_fullscreen_dimensions'])) {
                    $fullscreenWidth = !empty($entry['image_custom_fullscreen_width']) ? $entry['image_custom_fullscreen_width'] : false;
                }
                $fullscreenHeight = $this->image_defaultRepeatableFullscreenHeight;
                if (!empty($this->settings['image_override_fullscreen_dimensions'])) {
                    $fullscreenHeight = !empty($this->settings['image_custom_fullscreen_height']) ? $this->settings['image_custom_fullscreen_height'] : false;
                }
                if (!empty($entry['image_override_fullscreen_dimensions'])) {
                    $fullscreenHeight = !empty($entry['image_custom_fullscreen_height']) ? $entry['image_custom_fullscreen_height'] : false;
                }
                $fullscreenCrop = false;
                if (!empty($this->settings['image_override_fullscreen_dimensions'])) {
                    $fullscreenCrop = !empty($this->settings['image_custom_fullscreen_crop']) ? $this->settings['image_custom_fullscreen_crop'] : false;
                }
                if (!empty($entry['image_override_fullscreen_dimensions'])) {
                    $fullscreenCrop = !empty($entry['image_custom_fullscreen_crop']) ? $entry['image_custom_fullscreen_crop'] : false;
                }
                $modifiedEntry = $this->prepareForViewImage('entry', [
                    'image'     => $entry['image'],
                    'image_alt' => $entry['image_alt']
                ], [
                    'thumbnail'       => true,
                    'thumbnailWidth'  => $thumbnailWidth,
                    'thumbnailHeight' => $thumbnailHeight,
                    'thumbnailCrop'   => $thumbnailCrop,

                    'fullscreen'        => true,
                    'fullscreenWidth'   => $fullscreenWidth,
                    'fullscreenHeight'  => $fullscreenHeight,
                    'fullscreenCrop'    => $fullscreenCrop
                ]);
                $entry = array_merge($entry, $modifiedEntry);

                $entriesForView[] = $entry;

            }

        }

        return $entriesForView;

    }

}
<?php namespace Application\Block\ProductSlider;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Block\BlockController;
use Concrete\Core\File\File;
use Concrete\Core\Page\Page;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{

    protected $btTable = 'btProductSlider';
    protected $btExportTables = ['btProductSlider', 'btProductSliderEntries'];
    protected $btExportPageColumns = ['slide_button_link'];
    protected $btExportFileColumns = ['slide_image'];
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

    protected $slide_image_defaultRepeatableThumbnailWidth = 480;
    protected $slide_image_defaultRepeatableThumbnailHeight = 270;
    protected $slide_image_defaultRepeatableFullscreenWidth = 1920;
    protected $slide_image_defaultRepeatableFullscreenHeight = 1080;

    private $uniqueID;

    public function getBlockTypeName() {
        return t('Product Slider Swiper');
    }

    public function getBlockTypeDescription() {
        return t('');
    }

    public function getSearchableContent() {

        $content = [];

        $entries = $this->getEntries('edit');
        foreach ($entries as $entry) {
            $content[] = $entry['slide_title'];
            $content[] = $entry['slide_subtitle'];
            $content[] = $entry['slide_button_text'];
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
        $this->requireAsset('core/sitemap');
        $this->requireAsset('core/file-manager');

        // Get entry column names
        $entryColumnNames = $this->getEntryColumnNames();

        // Slide Image (slide_image) - Fields that don't exist in database, but are required in repeatable entry (image)
        $entryColumnNames[] = 'slide_image_show_additional_fields';
        $entryColumnNames[] = 'slide_image_override_dimensions';
        $entryColumnNames[] = 'slide_image_custom_width';
        $entryColumnNames[] = 'slide_image_custom_height';
        $entryColumnNames[] = 'slide_image_custom_crop';
        $entryColumnNames[] = 'slide_image_override_fullscreen_dimensions';
        $entryColumnNames[] = 'slide_image_custom_fullscreen_width';
        $entryColumnNames[] = 'slide_image_custom_fullscreen_height';
        $entryColumnNames[] = 'slide_image_custom_fullscreen_crop';

        $this->set('entryColumnNames', $entryColumnNames);

        // Load form.css
        $al = AssetList::getInstance();
        $al->register('css', 'product-slider/form', 'blocks/product_slider/css_files/form.css', [], false);
        $this->requireAsset('css', 'product-slider/form');

        // Make $app available in view
        $this->set('app', $this->app);

    }

    public function view() {

        // Make $app available in view
        $this->set('app', $this->app);

        $this->set('slide_image_defaultRepeatableThumbnailWidth', $this->slide_image_defaultRepeatableThumbnailWidth);
        $this->set('slide_image_defaultRepeatableThumbnailHeight', $this->slide_image_defaultRepeatableThumbnailHeight);

        $this->set('slide_image_defaultRepeatableFullscreenWidth', $this->slide_image_defaultRepeatableFullscreenWidth);
        $this->set('slide_image_defaultRepeatableFullscreenHeight', $this->slide_image_defaultRepeatableFullscreenHeight);

        // Get entries
        $entries = $this->getEntries();
        $entries = $this->prepareEntriesForView($entries);
        $this->set('entries', $entries);

    }

    public function save($args) {

        // Settings
        if (isset($args['settings']) and $args['settings']['slide_image_custom_crop']==='1' and (empty($args['settings']['slide_image_custom_width']) or empty($args['settings']['slide_image_custom_height']))) {
            $args['settings']['slide_image_custom_crop'] = 0; // Crop should be disabled if width or height is missing
        }
        if (isset($args['settings']) and $args['settings']['slide_image_custom_fullscreen_crop']==='1' and (empty($args['settings']['slide_image_custom_fullscreen_width']) or empty($args['settings']['slide_image_custom_fullscreen_height']))) {
            $args['settings']['slide_image_custom_fullscreen_crop'] = 0; // Crop should be disabled if width or height is missing
        }
        $args['settings'] = isset($args['settings']) ? json_encode($args['settings']) : null;

        parent::save($args);

        $db = $this->app->make('database')->connection();

        // Delete existing entries of current block's version
        $db->delete('btProductSliderEntries', ['bID' => $this->bID]);

        if (isset($args['entry']) AND is_array($args['entry']) AND count($args['entry'])) {

            $i = 1;

            foreach ($args['entry'] as $entry) {

                // Prepare data for insert
                $data = [];
                $data['position']          = $i;
                $data['bID']               = $this->bID;
                $data['slide_title']       = trim($entry['slide_title']);
                $data['slide_subtitle']    = trim($entry['slide_subtitle']);
                $data['slide_button_text'] = trim($entry['slide_button_text']);
                $data['slide_button_link'] = intval($entry['slide_button_link']);
                $data['slide_image']       = intval($entry['slide_image']);
                $data['slide_image_alt']   = trim($entry['slide_image_alt']);

                // Slide Image (slide_image) - Image
                $data['slide_image_data'] = json_encode([
                    'show_additional_fields'         => intval($entry['slide_image_show_additional_fields']),
                    'override_dimensions'            => !empty($entry['slide_image_override_dimensions']) ? intval($entry['slide_image_override_dimensions']) : 0,
                    'custom_width'                   => intval($entry['slide_image_custom_width']),
                    'custom_height'                  => intval($entry['slide_image_custom_height']),
                    'custom_crop'                    => ($entry['slide_image_custom_crop']==='1' and (!(bool)$entry['slide_image_custom_width'] or !(bool)$entry['slide_image_custom_height'])) ? false : intval($entry['slide_image_custom_crop']), // do not crop without width and height filled
                    'override_fullscreen_dimensions' => !empty($entry['slide_image_override_fullscreen_dimensions']) ? intval($entry['slide_image_override_fullscreen_dimensions']) : 0,
                    'custom_fullscreen_width'        => intval($entry['slide_image_custom_fullscreen_width']),
                    'custom_fullscreen_height'       => intval($entry['slide_image_custom_fullscreen_height']),
                    'custom_fullscreen_crop'         => ($entry['slide_image_custom_fullscreen_crop']==='1' and (!(bool)$entry['slide_image_custom_fullscreen_width'] or !(bool)$entry['slide_image_custom_fullscreen_height'])) ? false : intval($entry['slide_image_custom_fullscreen_crop']), // do not crop without width and height filled
                ]);

                $db->insert('btProductSliderEntries', $data);

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
                btProductSliderEntries.*
            FROM
                btProductSliderEntries
            WHERE
                btProductSliderEntries.bID = :bID
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
                $db->insert('btProductSliderEntries', $data);
            }
        }

    }

    public function delete() {

    }

    public function validate($args) {

        $error = $this->app->make('helper/validation/error');

        // Repeatable entries
        if (isset($args['entry']) AND is_array($args['entry'])) {

            // Required fields in repeatable entries
            $requiredEntryFields = [];
            $requiredEntryFields['slide_title'] = t('Slide Title');

            foreach ($requiredEntryFields as $requiredEntryFieldHandle => $requiredEntryFieldLabel) {

                $emptyEntries = [];

                foreach ($args['entry'] as $entry) {

                    if (empty($entry[$requiredEntryFieldHandle])) {
                        $emptyEntries[] = $requiredEntryFieldHandle;
                    }

                }

                if (is_array($emptyEntries) AND count($emptyEntries) AND in_array($requiredEntryFieldHandle, $emptyEntries)) {
                    $error->add(t('Field "%s" is required in every entry.', $requiredEntryFieldLabel));
                }

            }

            // Required fields in repeatable entries - Links
            $requiredEntryLinkFields = [];

            foreach ($requiredEntryLinkFields as $requiredEntryLinkFieldHandle => $requiredEntryLinkFieldLabel) {

                $emptyEntries = [];

                foreach ($args['entry'] as $entry) {

                    $errorCounter = 0;
                    $errorCounter += empty($entry[$requiredEntryLinkFieldHandle.'_link_type']) ? 1 : 0;
                    $errorCounter += ($entry[$requiredEntryLinkFieldHandle.'_link_type']=='link_from_sitemap' AND empty($entry[$requiredEntryLinkFieldHandle.'_link_from_sitemap'])) ? 1 : 0;
                    $errorCounter += ($entry[$requiredEntryLinkFieldHandle.'_link_type']=='link_from_file_manager' AND empty($entry[$requiredEntryLinkFieldHandle.'_link_from_file_manager'])) ? 1 : 0;
                    $errorCounter += ($entry[$requiredEntryLinkFieldHandle.'_link_type']=='external_link' AND empty($entry[$requiredEntryLinkFieldHandle.'_external_link'])) ? 1 : 0;

                    if ($errorCounter > 0) {
                        $emptyEntries[] = $requiredEntryLinkFieldHandle;
                    }

                }

                if (is_array($emptyEntries) AND count($emptyEntries) AND in_array($requiredEntryLinkFieldHandle, $emptyEntries)) {
                    $error->add(t('Field "%s" is required in every entry.', $requiredEntryLinkFieldLabel));
                }

            }

        }

        return $error;

    }

    public function composer() {

        $al = AssetList::getInstance();
        $al->register('javascript', 'product-slider/auto-js', 'blocks/product_slider/auto.js', [], false);
        $this->requireAsset('javascript', 'product-slider/auto-js');

        $this->edit();

    }

    public function scrapbook() {

        $this->edit();

    }

    private function getEntries($outputMethod = 'view') {

        $db = $this->app->make('database')->connection();

        $sql = '
            SELECT
                btProductSliderEntries.*
            FROM
                btProductSliderEntries
            WHERE
                btProductSliderEntries.bID = :bID
            ORDER BY
                btProductSliderEntries.position ASC
        ';
        $parameters = [];
        $parameters['bID'] = $this->bID;

        $entries = $db->fetchAll($sql, $parameters);

        $modifiedEntries = [];

        foreach ($entries as $entry) {

            $entry['slide_image'] = (is_object(File::getByID($entry['slide_image']))) ? $entry['slide_image'] : 0;
            // Slide Image (slide_image) - Image
            $slide_imageArray = json_decode($entry['slide_image_data'], true);
            $entry['slide_image_show_additional_fields']         = $slide_imageArray['show_additional_fields'] ?? '';
            $entry['slide_image_override_dimensions']            = $slide_imageArray['override_dimensions'] ?? '';
            $entry['slide_image_custom_width']                   = $slide_imageArray['custom_width'] ?? '';
            $entry['slide_image_custom_height']                  = $slide_imageArray['custom_height'] ?? '';
            $entry['slide_image_custom_crop']                    = $slide_imageArray['custom_crop'] ?? '';
            $entry['slide_image_override_fullscreen_dimensions'] = $slide_imageArray['override_fullscreen_dimensions'] ?? '';
            $entry['slide_image_custom_fullscreen_width']        = $slide_imageArray['custom_fullscreen_width'] ?? '';
            $entry['slide_image_custom_fullscreen_height']       = $slide_imageArray['custom_fullscreen_height'] ?? '';
            $entry['slide_image_custom_fullscreen_crop']         = $slide_imageArray['custom_fullscreen_crop'] ?? '';
            
            $modifiedEntries[] = $entry;

        }

        return $modifiedEntries;

    }

     private function getEntryColumnNames() {

        $db = $this->app->make('database')->connection();

        $columns = $db->getSchemaManager()->listTableColumns('btProductSliderEntries');

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

    private function prepareForViewLinkFromSitemap($type, $fields) {

        $keys = array_keys($fields);
        $pageIDFieldName    = $keys[0];
        $endingFieldName    = $keys[1];
        $textFieldName      = $keys[2];
        $titleFieldName     = $keys[3];
        $newWindowFieldName = $keys[4];
        $noFollowFieldName  = $keys[5];

        $pageID    = $fields[$pageIDFieldName];
        $ending    = $fields[$endingFieldName];
        $text      = $fields[$textFieldName];
        $title     = $fields[$titleFieldName];
        $newWindow = !empty($fields[$newWindowFieldName]) ? 'target="_blank"' : '';
        $noFollow  = !empty($fields[$noFollowFieldName]) ? 'rel="nofollow"' : '';

        $pageObject = false;
        $name       = '';
        $link       = '';

        if (!empty($pageID)) {

            $pageObject = Page::getByID($pageID);

            if (!$pageObject->isError() AND !$pageObject->isInTrash()) {

                $link = $pageObject->getCollectionLink();
                $name = $pageObject->getCollectionName();

            }
        }

        if ($type == 'view') {

            // Fields from database
            $this->set($pageIDFieldName, $pageID);
            $this->set($endingFieldName, $ending);
            $this->set($textFieldName, $text);
            $this->set($titleFieldName, $title);
            $this->set($newWindowFieldName, $newWindow);
            $this->set($noFollowFieldName, $noFollow);

            // Additional data
            $this->set($pageIDFieldName.'_object', $pageObject);
            $this->set($pageIDFieldName.'_name', $name);
            $this->set($pageIDFieldName.'_link', $link);
            $this->set($pageIDFieldName.'_link_type', 'link_from_sitemap');

        } elseif ($type == 'entry') {

            $entry = [];

            // Fields from database
            $entry[$pageIDFieldName]    = $pageID;
            $entry[$endingFieldName]    = $ending;
            $entry[$textFieldName]      = $text;
            $entry[$titleFieldName]     = $title;
            $entry[$newWindowFieldName] = $newWindow;
            $entry[$noFollowFieldName]  = $noFollow;

            // Additional data
            // $entry[$pageIDFieldName.'_object']    = $pageObject;
            $entry[$pageIDFieldName.'_name']      = $name;
            $entry[$pageIDFieldName.'_link']      = $link;
            $entry[$pageIDFieldName.'_link_type'] = 'link_from_sitemap';

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

                // Slide Button Link (slide_button_link) - Link from Sitemap
                $modifiedEntry = $this->prepareForViewLinkFromSitemap('entry', [
                    'slide_button_link'            => $entry['slide_button_link'],
                    'slide_button_link_ending'     => false,
                    'slide_button_link_text'       => false,
                    'slide_button_link_title'      => false,
                    'slide_button_link_new_window' => false,
                    'slide_button_link_no_follow'  => false
                ]);
                $entry = array_merge($entry, $modifiedEntry);

                // Slide Image (slide_image) - Image
                $thumbnailWidth = $this->slide_image_defaultRepeatableThumbnailWidth;
                if (!empty($this->settings['slide_image_override_dimensions'])) {
                    $thumbnailWidth = !empty($this->settings['slide_image_custom_width']) ? $this->settings['slide_image_custom_width'] : false;
                }
                if (!empty($entry['slide_image_override_dimensions'])) {
                    $thumbnailWidth = !empty($entry['slide_image_custom_width']) ? $entry['slide_image_custom_width'] : false;
                }
                $thumbnailHeight = $this->slide_image_defaultRepeatableThumbnailHeight;
                if (!empty($this->settings['slide_image_override_dimensions'])) {
                    $thumbnailHeight = !empty($this->settings['slide_image_custom_height']) ? $this->settings['slide_image_custom_height'] : false;
                }
                if (!empty($entry['slide_image_override_dimensions'])) {
                    $thumbnailHeight = !empty($entry['slide_image_custom_height']) ? $entry['slide_image_custom_height'] : false;
                }
                $thumbnailCrop = true;
                if (!empty($this->settings['slide_image_override_dimensions'])) {
                    $thumbnailCrop = !empty($this->settings['slide_image_custom_crop']) ? $this->settings['slide_image_custom_crop'] : false;
                }
                if (!empty($entry['slide_image_override_dimensions'])) {
                    $thumbnailCrop = !empty($entry['slide_image_custom_crop']) ? $entry['slide_image_custom_crop'] : false;
                }
                $fullscreenWidth = $this->slide_image_defaultRepeatableFullscreenWidth;
                if (!empty($this->settings['slide_image_override_fullscreen_dimensions'])) {
                    $fullscreenWidth = !empty($this->settings['slide_image_custom_fullscreen_width']) ? $this->settings['slide_image_custom_fullscreen_width'] : false;
                }
                if (!empty($entry['slide_image_override_fullscreen_dimensions'])) {
                    $fullscreenWidth = !empty($entry['slide_image_custom_fullscreen_width']) ? $entry['slide_image_custom_fullscreen_width'] : false;
                }
                $fullscreenHeight = $this->slide_image_defaultRepeatableFullscreenHeight;
                if (!empty($this->settings['slide_image_override_fullscreen_dimensions'])) {
                    $fullscreenHeight = !empty($this->settings['slide_image_custom_fullscreen_height']) ? $this->settings['slide_image_custom_fullscreen_height'] : false;
                }
                if (!empty($entry['slide_image_override_fullscreen_dimensions'])) {
                    $fullscreenHeight = !empty($entry['slide_image_custom_fullscreen_height']) ? $entry['slide_image_custom_fullscreen_height'] : false;
                }
                $fullscreenCrop = false;
                if (!empty($this->settings['slide_image_override_fullscreen_dimensions'])) {
                    $fullscreenCrop = !empty($this->settings['slide_image_custom_fullscreen_crop']) ? $this->settings['slide_image_custom_fullscreen_crop'] : false;
                }
                if (!empty($entry['slide_image_override_fullscreen_dimensions'])) {
                    $fullscreenCrop = !empty($entry['slide_image_custom_fullscreen_crop']) ? $entry['slide_image_custom_fullscreen_crop'] : false;
                }
                $modifiedEntry = $this->prepareForViewImage('entry', [
                    'slide_image'     => $entry['slide_image'],
                    'slide_image_alt' => $entry['slide_image_alt']
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
<?php

namespace Application\Controller\PageType;

use Concrete\Core\Page\Controller\PageTypeController;
use Concrete\Core\Multilingual\Page\Section\Section;

use Core;
use Loader;
use Page;
use PageList;
use Config;
use UserInfo;
use CollectionAttributeKey; 
use File; 
use Concrete\Core\File\Service\File AS FileService;

use Concrete\Core\Attribute\Key\CollectionKey;
use Concrete\Core\Tree\Type\Topic as TopicTree;

use Concrete\Core\Permission\Checker;
// use Mpdf\Mpdf;

class PageGlobal extends PageTypeController
{
	# Default LANG
	public $languages = ['area' => '', 'path' => '/', 'class' => ''];

	public function getLanguages() {
		# get the current page
		$current_page = Page::getCurrentPage();
		# get the current lanuage
		$site_lang = Section::getBySectionOfSite($current_page);

		# set lanuage
		if(is_object($site_lang) && $site_lang->getLocale() == 'en_US'){
			$this->languages['area'] = 'EN';
			$this->languages['path'] = $site_lang->getCollectionLink();
			$this->languages['class'] = 'lang-en';
		} else if (is_object($site_lang)) {
			$local = explode('_', $site_lang->getLocale());
			$this->languages['area'] = $local[count($local) - 1];
			$this->languages['path'] = $site_lang->getCollectionLink();
			$this->languages['class'] = 'lang-'.strtolower($this->languages['area']);
		} else {
			$this->languages['area'] = 'EN';
			$this->languages['path'] = '/';
			$this->languages['class'] = 'lang-en';
		}
		return $this->languages;
	}

	# Get Language Area
	public function getLanguageArea() {
		return $this->languages['area'];
	}

	# Get Language Path
	public function getLanguagePath() {
		return $this->languages['path'];
	}

	# Get Language Class
	public function getLanguageClass() {
		return $this->languages['class'];
	}

	# Auto Redirect Inside Page
	public function autoRedirect() {
		# get the current page
		$current = Page::getCurrentPage();
		$rid = $current->getAttribute('redirect_page');

		if(!empty($rid)) {
			$page = Page::getByID($rid);
			if (is_object($page)) {
				$url = $page->getCollectionLink();
				header('Location: '. ($url));
				exit;
			}
		}
	}
	
	public function getOptionYears($start = 2012)
	{
		$year = date('Y');
		for ($i = $year; $start <= $i; $i--) {
			$option[$i] = $i;
		}
		return $option;
	}

	public function akArrayList($handle,$label=null){
		$akCollection = CollectionAttributeKey::getByHandle($handle);
		if($akCollection) :
			$options = $akCollection->getController()->getOptions();
			$akArray = array();
			if($label){
				$akArray[''] = $label;
			}
			if(count($options)) :
				foreach ($options as $option) :
					$value = $option->getSelectAttributeOptionValue();
					$id = $option->getSelectAttributeOptionID();
					$akArray[$value] = $value;
				endforeach;
			endif;

			return $akArray;
		endif;
	}

	public function xxx() {
		// return Section::getCurrentSection();
		// $c = Page::getCurrentPage();
		$ml = Section::getList();
		$languages = [];
		foreach ($ml as $m) {
			$pc = new Checker(Page::getByID($m->getCollectionID()));
			if ($pc->canRead()) {
					$mlAccessible[] = $m;
					// $languages[$m->getCollectionID()] = $m->getLanguageText($m->getLocale());
					$languages[] = [
						'id' => $m->getCollectionID(),
						'text' => $m->getLanguageText($m->getLocale()),
						'locale' => $m->getLocale(),
						'language' => $m->getLanguage(),
						'link' => $m->getCollectionLink(),
					];
			}
		}
		return $languages;
	}
	
	public function xx() {
		return Section::getDefaultSection();
	}

	public function x($id= 1) {
		// return Section::getCurrentSection();
		// $c = Page::getCurrentPage();
		// $ml = Section::getByID($id);
		$ml = Section::getCurrentSection();
		$languages[] = [
			'id' => $ml->getCollectionID(),
			'text' => $ml->getLanguageText($ml->getLocale()),
			'locale' => $ml->getLocale(),
			'language' => $ml->getLanguage(),
		];
		return $languages;
		// $languages = [];
		// foreach ($ml as $m) {
		// 	$pc = new Checker(Page::getByID($m->getCollectionID()));
		// 	if ($pc->canRead()) {
		// 			$mlAccessible[] = $m;
		// 			// $languages[$m->getCollectionID()] = $m->getLanguageText($m->getLocale());
		// 			$languages[] = [
		// 				'id' => $m->getCollectionID(),
		// 				'text' => $m->getLanguageText($m->getLocale()),
		// 				'locale' => $m->getLocale(),
		// 				'language' => $m->getLanguage(),
		// 			];
		// 	}
		// }
		// return $languages;
	
	}


	// public function generatePdf()
	// {
	// 	$mpdf = $this->app->make(\Application\Controller\SinglePage\Salesforce::class);
	// 	// Create an instance of mPDF
	// 	$mpdf = new Mpdf();

	// 	// Write some HTML content to the PDF
	// 	$mpdf->WriteHTML('<h1>Hello World</h1>');

	// 	// Output the PDF to the browser
	// 	$mpdf->Output();
	// }

	public function decryptAndDownload($fileID) {
        $db = \Database::connection();
        $metadata = $db->fetchAssoc('SELECT * FROM _KOSEncryptedFiles WHERE fileID = ?', [$fileID]);

        if ($metadata) {
            $file = File::getByID($fileID);
			$_file = Core::make('helper/file');
            $filePath = $file->getRelativePath();
            $fileURL = $file->getURL();
            // $encryptedData = $file->getContents();


			$enc = $_SERVER['DOCUMENT_ROOT'] . $filePath;



            // $encryptedData = $_file->getContents($fileURL);

			$encryptedData = file_get_contents($enc);
			
            $key = hex2bin($metadata['encryptionKey']);
            $iv = hex2bin($metadata['initializationVector']);
			// $key = $metadata['encryptionKey'];
            // $iv = $metadata['initializationVector'];

			//  $key = base64_decode($metadata['encryptionKey']);
            // $iv = base64_decode($metadata['initializationVector']);


            $originalFilename = $metadata['originalFilename'];


			// echo 'encryptionKey: ' . $metadata['encryptionKey'];
			// echo '<br>';
			// echo 'key: ' . $key;
			// echo '<br>';
			// echo 'initializationVector: ' . $metadata['initializationVector'];
			// echo '<br>';
			// echo 'iv: ' . $iv;
			// echo '<br>';
			// echo 'originalFilename: ' . $originalFilename;
			// echo '<br>';
            $decryptedData = openssl_decrypt($encryptedData, 'AES-256-CBC', $key, 0, $iv);

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $originalFilename . '"');
            echo $decryptedData;
        }
	}

	public function getTopicTreeByHandle ($handle = null) {
		$result = [];
		if (!is_null($handle)) {
			$ak = CollectionKey::getByHandle($handle);
			if (is_object($ak)) {
				$tree = TopicTree::getByID($ak->getController()->getTopicTreeID());
				$node = $tree->getRootTreeNodeObject();
				$node->populateChildren();
				foreach ($node->getChildNodes() as $val) {
					$result[] = [
						'id' => $val->getTreeNodeID(),
						'value' => $val->getTreeNodeDisplayName(),
					];
				}
			}
		}
		return $result;
	}

	public function convertDateTH ($date = null, $isShort = false) {
		$month = [
			'01' => 'มกราคม',
			'02' => 'กุมภาพันธ์',
			'03' => 'มีนาคม',
			'04' => 'เมษายน',
			'05' => 'พฤษภาคม',
			'06' => 'มิถุนายน',
			'07' => 'กรกฎาคม',
			'08' => 'สิงหาคม',
			'09' => 'กันยายน',
			'10' => 'ตุลาคม',
			'11' => 'พฤศจิกายน',
			'12' => 'ธันวาคม'
		];

		if ($isShort) {
			$month = [
				'01' => 'ม.ค.',
				'02' => 'ก.พ.',
				'03' => 'มี.ค.',
				'04' => 'เม.ย.',
				'05' => 'พ.ค.',
				'06' => 'มิ.ย.',
				'07' => 'ก.ค.',
				'08' => 'ส.ค.',
				'09' => 'ก.ย.',
				'10' => 'ต.ค.',
				'11' => 'พ.ย.',
				'12' => 'ธ.ค.'
			];
		}
		
		if (!is_null($date)) {
			return date_format($date, 'd') . ' ' . $month[date_format($date, 'm')] . ' ' . date_format($date, 'Y') + 543;
		}
		
		return $date;
	}
}

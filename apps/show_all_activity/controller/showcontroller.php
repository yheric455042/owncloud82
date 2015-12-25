<?php
namespace OCA\Show_All_Activity\Controller;

use OC\Files\View;
use OCA\Activity\Data;
use OCA\Activity\GroupHelper;
use OCA\Activity\Navigation;
use OCA\Activity\UserSettings;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\Files;
use OCP\Files\IMimeTypeDetector;
use OCP\IDateTimeFormatter;
use OCP\IPreview;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\Template;

class ShowController extends Controller{
    const DEFAULT_PAGE_SIZE = 30;

	const MAX_NUM_THUMBNAILS = 7;

	/** @var \OCA\Activity\Data */
	protected $data;

	/** @var \OCA\Activity\GroupHelper */
	protected $helper;

	/** @var \OCA\Activity\Navigation */
	protected $navigation;

	/** @var \OCA\Activity\UserSettings */
	protected $settings;

	/** @var IDateTimeFormatter */
	protected $dateTimeFormatter;

	/** @var IPreview */
	protected $preview;

	/** @var IURLGenerator */
	protected $urlGenerator;

	/** @var IMimeTypeDetector */
	protected $mimeTypeDetector;

	/** @var View */
	protected $view;


	/** @var String */
	protected $user;


   public function __construct($appName,
								IRequest $request,
								Data $data,
								GroupHelper $helper,
								Navigation $navigation,
								UserSettings $settings,
								IDateTimeFormatter $dateTimeFormatter,
								IPreview $preview,
								IURLGenerator $urlGenerator,
								IMimeTypeDetector $mimeTypeDetector,
								View $view,
                                $user) {
		parent::__construct($appName, $request);
		$this->data = $data;
		$this->helper = $helper;
		$this->navigation = $navigation;
		$this->settings = $settings;
		$this->dateTimeFormatter = $dateTimeFormatter;
		$this->preview = $preview;
		$this->urlGenerator = $urlGenerator;
		$this->mimeTypeDetector = $mimeTypeDetector;
		$this->view = $view;
        $this->user = $user;
	}

    public function fetch($page, $user, $filter = 'all', $objecttype = '', $objectid = 0) {
		$pageOffset = $page - 1;
		$filter = $this->data->validateFilter($filter);

		$activities = $this->data->read($this->helper, $this->settings, $pageOffset * self::DEFAULT_PAGE_SIZE, self::DEFAULT_PAGE_SIZE, $filter, $user, $objecttype, $objectid);

		$preparedActivities = [];
		foreach ($activities as $activity) {
			$activity['relativeTimestamp'] = (string) Template::relative_modified_date($activity['timestamp'], true);
			$activity['readableTimestamp'] = (string) $this->dateTimeFormatter->formatDate($activity['timestamp']);
			$activity['relativeDateTimestamp'] = (string) Template::relative_modified_date($activity['timestamp']);
			$activity['readableDateTimestamp'] = (string) $this->dateTimeFormatter->formatDateTime($activity['timestamp']);

			if (strpos($activity['subjectformatted']['markup']['trimmed'], '<a ') !== false) {
				// We do not link the subject as we create links for the parameters instead
				$activity['link'] = '';
			}

			$activity['previews'] = [];
			if ($activity['object_type'] === 'files' && !empty($activity['files'])) {
				foreach ($activity['files'] as $objectId => $objectName) {
					if (((int) $objectId) === 0 || $objectName === '') {
						// No file, no preview
						continue;
					}

					$activity['previews'][] = $this->getPreview($activity['affecteduser'], (int) $objectId, $objectName);

					if (sizeof($activity['previews']) >= self::MAX_NUM_THUMBNAILS) {
						// Don't want to clutter the page, so we stop after a few thumbnails
						break;
					}
				}
			} else if ($activity['object_type'] === 'files' && $activity['object_id']) {
				$activity['previews'][] = $this->getPreview($activity['affecteduser'], (int) $activity['object_id'], $activity['file']);
			}

			$preparedActivities[] = $activity;
		}
       
		return new JSONResponse($preparedActivities);
	}

	/**
	 * @param string $owner
	 * @param int $fileId
	 * @param string $filePath
	 * @return array
	 */
	protected function getPreview($owner, $fileId, $filePath) {
		$this->view->chroot('/' . $owner . '/files');
		$path = $this->view->getPath($fileId);

		if ($path === null || $path === '' || !$this->view->file_exists($path)) {
			return $this->getPreviewFromPath($filePath);
		}

		$is_dir = $this->view->is_dir($path);

		$preview = [
			'link'			=> $this->getPreviewLink($path, $is_dir),
			'source'		=> '',
			'isMimeTypeIcon' => true,
		];

		// show a preview image if the file still exists
		if ($is_dir) {
			$preview['source'] = $this->getPreviewPathFromMimeType('dir');
		} else {
			$fileInfo = $this->view->getFileInfo($path);
			if ($this->preview->isAvailable($fileInfo)) {
				$preview['isMimeTypeIcon'] = false;
				$preview['source'] = $this->urlGenerator->linkToRoute('core_ajax_preview', [
					'file' => $path,
					'c' => $this->view->getETag($path),
					'x' => 150,
					'y' => 150,
				]);
			} else {
				$preview['source'] = $this->getPreviewPathFromMimeType($fileInfo->getMimetype());
			}
		}

		return $preview;
	}

	/**
	 * @param string $filePath
	 * @return array
	 */
	protected function getPreviewFromPath($filePath) {
		$mimeType = $this->mimeTypeDetector->detectPath($filePath);
		$preview = [
			'link'			=> $this->getPreviewLink($filePath, false),
			'source'		=> $this->getPreviewPathFromMimeType($mimeType),
			'isMimeTypeIcon' => true,
		];

		return $preview;
	}

	/**
	 * @param string $mimeType
	 * @return string
	 */
	protected function getPreviewPathFromMimeType($mimeType) {
		$mimeTypeIcon = $this->mimeTypeDetector->mimeTypeIcon($mimeType);
		if (substr($mimeTypeIcon, -4) === '.png') {
			$mimeTypeIcon = substr($mimeTypeIcon, 0, -4) . '.svg';
		}

		return $mimeTypeIcon;
	}

	/**
	 * @param string $path
	 * @param bool $isDir
	 * @return string
	 */
	protected function getPreviewLink($path, $isDir) {
		if ($isDir) {
			return $this->urlGenerator->linkTo('files', 'index.php', array('dir' => $path));
		} else {
			$parentDir = (substr_count($path, '/') === 1) ? '/' : dirname($path);
			$fileName = basename($path);
			return $this->urlGenerator->linkTo('files', 'index.php', array(
				'dir' => $parentDir,
				'scrollto' => $fileName,
			));
		}
	}



}
?>

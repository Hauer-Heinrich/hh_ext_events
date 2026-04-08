<?php
declare(strict_types=1);
namespace HauerHeinrich\HhExtEvents\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/***
 *
 * This file is part of the "hh_ext_events" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2026 HH <web@hauer-heinrich._de>, Werbeagentur Hauer Heinrich
 *
 ***/

class GenericController extends ActionController {

    protected $contentData = [];

    public function initializeAction(): void {
        $currentContentObject = $this->request->getAttribute('currentContentObject');
        $this->contentData = $currentContentObject instanceof ContentObjectRenderer ? $currentContentObject->data : [];
        $this->convertIntegerSettingsToIntegerValues();
    }

    public function convertIntegerSettingsToIntegerValues() {
        foreach ($this->settings as &$value) {
            if(\is_numeric($value)) {
                $value = \intval($value);
            }
        }
    }
}

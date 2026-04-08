<?php
declare(strict_types=1);
namespace HauerHeinrich\HhExtEvents\Helper;

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

use \Psr\Http\Message\ServerRequestInterface;

final class RequestHelper {
    public function getRequestParam(
        ServerRequestInterface $request,
        array $settings,
        string $key,
        string $pluginSettingsKey = ''
    ) {
        $pluginSettingsKey = $pluginSettingsKey ?: $key;
        $queryParams = $request->getQueryParams();
        $postData = $request->getParsedBody()['tx_hhextevents'] ?? [];

        if ($request->hasArgument($key)) {
            return $request->getArgument($key);
        } elseif (!empty($postData[$key])) {
            return $postData[$key];
        } elseif (!empty($queryParams[$key])) {
            return $queryParams[$key];
        } elseif (!empty($settings['field'][$pluginSettingsKey])) {
            return $settings['field'][$pluginSettingsKey];
        }

        return null;
    }
}

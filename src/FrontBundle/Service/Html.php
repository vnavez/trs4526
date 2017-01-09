<?php

namespace FrontBundle\Service;

use FrontBundle\Exception\ApiException;

class Html
{

    /**
     * Get Torrent ID
     * @param $url
     * @return mixed
     * @throws ApiException
     */
    public function getTorrentId($url)
    {
        $content = $this->getPageHtml($url);
        if (!preg_match('#\/t\/([0-9]+)#', $content, $matches) || (!$matches && !isset($matches[1])))
            throw new ApiException('Unable to get torrent ID');

        if (preg_match('#hadopi#', $content))
            throw new ApiException('h@dopi');

        return $matches[1];
    }

    /**
     * Get Page HTML
     * @param $url
     * @return mixed
     * @throws ApiException
     */
    public function getPageHtml($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != 200)
            throw new ApiException('Error code while calling URL');

        return $result;
    }

}
<?php

namespace FrontBundle\Service;

use FrontBundle\Exception\ApiException;

class Api
{

    protected $_login;
    protected $_pass;
    protected $_token;

    /**
     * Auth Api
     * @param $login
     * @param $password
     * @return bool
     * @throws ApiException
     */
    public function auth($login, $password)
    {
        $this->_login = $login;
        $this->_pass = $password;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.t411.ai/auth');
        curl_setopt($ch, CURLOPT_POST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'username=' . $this->_login . '&password=' . $this->_pass);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != 200)
            throw new ApiException('Error code on auth');

        $data = json_decode($result);
        if (isset($data->error))
            throw new ApiException($data->error);

        if (!isset($data->token))
            throw new ApiException('No token');

        $this->_token = $data->token;
        return true;
    }

    /**
     * Return download Torrent
     * @param $id
     * @param $apiState
     * @param $cookie
     * @return mixed
     * @throws ApiException
     */
    public function download($id, $apiState, $cookie)
    {

        if (!$apiState) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.t411.ai/users/auth/');
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
            curl_setopt($ch, CURLOPT_POST, 2);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('x-requested-with: XMLHttpRequest'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'url=/&remember=1&login=' . $this->_login . '&password=' . $this->_pass);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
            curl_exec($ch);
            curl_close($ch);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.t411.ai/torrents/download/?id=' . $id);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
            $result = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);

            unset($cookie);
        } else {
            if (!$this->_token)
                throw new ApiException('Please auth before');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.t411.ai/torrents/download/' . $id);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . $this->_token]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);
        }

        if ($info['http_code'] != 200)
            throw new ApiException('Error code on auth');

        $data = json_decode($result);
        if (isset($data->error))
            throw new ApiException($data->error);

        return $result;
    }

    /**
     * @param $url
     * @return mixed
     */
    public function downloadOther($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * @param $id
     * @return mixed
     * @throws ApiException
     */
    public function getDetails($id)
    {
        if (!$this->_token)
            throw new ApiException('Please auth before');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.t411.ai/torrents/details/' . $id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . $this->_token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != 200)
            throw new ApiException('Error code on auth');

        $data = json_decode($result);
        if (isset($data->error))
            throw new ApiException($data->error);

        return $data;
    }

    /**
     * @param $search
     * @return mixed
     * @throws ApiException
     */
    public function search($search)
    {
        if (!$this->_token)
            throw new ApiException('Please auth before');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.t411.ai/torrents/search/' . $search . '?limit=10000&order=seeders');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . $this->_token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != 200)
            throw new ApiException('Error code on auth');

        $data = json_decode($result);
        if (isset($data->error))
            throw new ApiException($data->error);

        return $data;
    }

    /**
     * @return mixed
     * @throws ApiException
     */
    public function top100()
    {
        if (!$this->_token)
            throw new ApiException('Please auth before');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.t411.ai/torrents/top/100');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . $this->_token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != 200)
            throw new ApiException('Error code on auth');

        $data = json_decode($result);
        if (isset($data->error))
            throw new ApiException($data->error);

        return $data;
    }

    /**
     * @return mixed
     * @throws ApiException
     */
    public function today()
    {
        if (!$this->_token)
            throw new ApiException('Please auth before');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.t411.ai/torrents/top/today');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . $this->_token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != 200)
            throw new ApiException('Error code on auth');

        $data = json_decode($result);
        if (isset($data->error))
            throw new ApiException($data->error);

        return $data;
    }
}
<?php

namespace FrontBundle\Service;

use FrontBundle\Exception\ApiException;

class Api
{

    protected $_token;

    /**
     * Auth Api
     * @param $login
     * @param $password
     * @return bool
     * @throws ApiException
     */
    public function auth($login, $password) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.t411.ai/auth');
        curl_setopt($ch, CURLOPT_POST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$login.'&password='.$password);
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
     * @return mixed
     * @throws ApiException
     */
    public function download($id) {
        if (!$this->_token)
            throw new ApiException('Please auth before');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.t411.ai/torrents/download/'.$id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: '.$this->_token]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != 200)
            throw new ApiException('Error code on auth');

        $data = json_decode($result);
        if (isset($data->error))
            throw new ApiException($data->error);

        return $result;
    }

    /**
     * @param $id
     * @return mixed
     * @throws ApiException
     */
    public function getDetails($id) {
        if (!$this->_token)
            throw new ApiException('Please auth before');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.t411.ai/torrents/details/'.$id);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: '.$this->_token]);
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
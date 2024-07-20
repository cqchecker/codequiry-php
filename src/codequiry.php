<?php

class Codequiry {

    /**
     * @version 0.0.1
     */
    const VERSION  = "0.0.1";

    private static $API_ROOT_URL = "https://codequiry.com/api/v1/";
    private static $API_UPLOAD_PATH = "https://codequiry.com/api/v1/check/upload";
    private static $SOCKETS_BASE_URL = "https://api.codequiry.com/";
    private static $BASE_HEADERS = [
        'Content-Type: application/json'
    ];

    public function __construct($api_key) {
        self::$BASE_HEADERS[] = 'apikey:' . $api_key;
    }

    public function account() {
        return $this->post('account', null);
    }

    public function checks() {
        return $this->post('checks', null);
    }

    public function create_check($check_name, $lang) {
        $body = [
            'name' => $check_name,
            'language' => $lang
        ];
        return $this->post('check/create', $body);
    }

    public function start_check($check_id) {
        $body = [
            'check_id' => $check_id
        ];
        return $this->post('check/start', $body);
    }

    public function get_check($check_id) {
        $body = [
            'check_id' => $check_id
        ];
        return $this->post('check/get', $body);
    }

    public function get_overview($check_id) {
        $body = [
            'check_id' => $check_id
        ];
        return $this->post('check/overview', $body);
    }

    public function get_results($check_id, $sid) {
        $body = [
            'check_id' => $check_id,
            'submission_id' => $sid
        ];
        return $this->post('check/results', $body);
    }

    function check_listen($job_id) {
        $socket = stream_socket_client(self::$SOCKETS_BASE_URL);
        $data = ['jobid' => $job_id];
        if ($socket) {
            $sent = stream_socket_sendto($socket, json_encode(Array("job-check" => $data)));
            if ($sent > 0) {
                $server_response = fread($socket, 4096);
                echo $server_response;
            }
            stream_socket_shutdown($socket, STREAM_SHUT_RDWR);
        } else {
            echo 'Unable to establish socket connection to the server';
        }
    }

    function upload_file($check_id, $file_path) {
        $headers = array_filter(self::$BASE_HEADERS, function ($key) {
            return strpos($key, 'Content-Type') !== 0;
        }, ARRAY_FILTER_USE_KEY);
        $headers[] = "Content-Type:multipart/form-data";
        $file_contents = file_get_contents($file_path);
        $fields = [
            "check_id" => $check_id,
            "file" => $file
        ];
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => self::$API_UPLOAD_PATH,
            CURLOPT_HEADER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $result = "";
        if(!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            if ($info['http_code'] == 200)
                $result = "File uploaded successfully";
            else
                $result = $info;
        } else {
            $result = curl_error($ch);
        }
        curl_close($ch);

        return $result;
    }

    private function post($url, $body = null) {
        $url = strpos($url, 'http') === 0 ? $url : self::$API_ROOT_URL . $url;
        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => self::$BASE_HEADERS,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $opts);
        $content = curl_exec($ch);
        if ($content === false) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        $response["body"] = json_decode($content, true);
        $response["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $response;
    }
}

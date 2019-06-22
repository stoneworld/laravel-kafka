<?php

/** http://jypx.fjjsrckj.com
 * Class fjjsrckj
 */
class fjjsrckj
{
    // 验证码地址：
    const VAL_CODE = 'http://jypx.fjjsrckj.com/web/login/validateCode/getValidateCode?type=1&1561170776624';

    // 课程列表
    const COURSE_LIST = 'http://jypx.fjjsrckj.com/web/front/myClass/getMyCourseList?_q_=1561171614935,1561171614935&classId=4028fba0679bdcde0167f420871e5bb8&listType=1';

    // 用户信息
    const USER_INFO = 'http://jypx.fjjsrckj.com/web/login/login/getUserInfo.action?_q=Sat%20Jun%2022%202019%2011:24:42%20GMT+0800%20(%E4%B8%AD%E5%9B%BD%E6%A0%87%E5%87%86%E6%97%B6%E9%97%B4)';

    // 课程信息
    const COURSE_INFO = 'http://jypx.fjjsrckj.com/web/portal/play/getCourseInfo/TRAINING_CLASS?';

    // 播放参数信息
    const ANYTHING_URL = 'http://jypx.fjjsrckj.com/web/portal/play/getPlayParams/anything?';

    // 初始化播放器参数地址
    const INIT_URL = 'https://hwstudyv1.59iedu.com//api/LearningMarker/Initing';

    // 播放请求地址
    const TIMEING_URL = 'https://hwstudyv1.59iedu.com//api/LearningMarker/Timing';
    /**
     * @var curlRequest
     */
    private $curlRequest;

    public function __construct()
    {
        $this->curlRequest = new curlRequest();
    }

    public function login()
    {
        // TODO 自动登录待做
    }

    public function getCourseList()
    {
        return $this->getDataFromUrl(self::COURSE_LIST);
    }

    public function getDataFromUrl($url)
    {
        $cookie = [
            "Cookie: UM_distinctid=16b7e9b860386-00c5f68c8a89e1-37677e02-13c680-16b7e9b8604156; _365groups_ClientID=a45086b4-8023-4e97-80fa-3e5fc0312a2d; CNZZDATA1261677421=442356888-1561193775-http%253A%252F%252Fjypx.fjjsrckj.com%252F%7C1561210571; CNZZDATA5050436=cnzz_eid%3D1958639813-1561195423-http%253A%252F%252Fjypx.fjjsrckj.com%252F%26ntime%3D1561211650; JSESSIONID=D55265B2EFC5C3105BE93CF166057C8A"
        ];
        $response = $this->curlRequest->curlGet($url, $cookie);
        $response = json_decode($response, true);
        if (isset($response['code']) && $response['code'] == 200) return $response['info'];
        echo $url . '信息获取失败';
        var_dump($response);
        return false;
    }

    private function initPlayerAndFinishedOne($courseInfo, $userInfo, $anythingInfo)
    {
        $requestTime = sprintf("%d", round(microtime(true)*1000));
        $requestTime = (int)$requestTime + rand(1000, 8000);
        $head = [
            'appVersion' => '1.0.0',
            'osPlatform' => 'web',
            'requestTime' => $requestTime,
        ];
        $data['head'] = $head;
        $filterList = [];
        foreach ($courseInfo['lesson']['chapterList'][0]['courseWareList'] as $chapterList) {
            $list = [
                'courseId' => $courseInfo['lesson']['id'],
                'coursewareId' => $chapterList['id'],
                'filterType' => 0,
                'entityId' => $chapterList['mediaList'][0]['id'],
                'isFilter' => false,
            ];
            $filterList[] = $list;
        }
        $content = [];
        $content['markers'] = $anythingInfo['objectList'];
        $markers = [];
        foreach ($anythingInfo['objectList'] as $value) {
            $markers[] = [
                'type' => $value['key'],
                'objectId' => $value['value'],
            ];
        }
        $content['objectList'] = $markers;
        $content['guid'] = $anythingInfo['guid'];
        $content['plmId'] = $anythingInfo['platformId'];
        $content['pvmId'] = $anythingInfo['platformVersionId'];
        $content['prmId'] = $anythingInfo['projectId'];
        $content['subPrmId'] = $anythingInfo['subProjectId'];
        $content['unitId'] = $anythingInfo['unitId'];
        //$content['orgId'] = $anythingInfo['subProjectId'];
        $content['orgId'] = "-1";
        $content['dataProjectId'] = $anythingInfo['projectId'];;
        $content['dataPlatformVersionId'] = $anythingInfo['dataPlatformVersionId'];
        $content['originalAbilityId'] = $courseInfo['originalAbilityId'];
        $data['data'] = [
            'isWriteHistory' => true,
            'usrId' => $userInfo['userId'],
            'filterList' => $filterList,
            'courseId' => $courseInfo['lesson']['id'],
            'courseWareId' => $anythingInfo['courseWareId'],
            'multimediaId' => $anythingInfo['mediaId'],
            'type' => 'single',
            'token' => '',
            'context' => $content,
            'originalAbilityId' => $courseInfo['originalAbilityId'],

        ];
        $initUrl = self::INIT_URL;
        $initResponse = $this->curlRequest->curlPost($initUrl, $data, true);
        echo json_encode($data, true);
        $initResponse = json_decode($initResponse, true);
        echo __LINE__.'-------'.self::INIT_URL."\n";

        if ($initResponse['head']['code'] != 200) exit();
        $core = $initResponse['data']['core'];
        $timeingParam['head'] = $head;
        $timeingParam['data']['extend'] = $content;
        $timeingParam['data']['core'] = [
            'primaryKey' => $core['primaryKey'],
            'courseRecordId' => $core['courseRecordId'],
            'coursewareRecordId' => $core['coursewareRecordId'],
            'lessonId' => $core['lessonId'],
            'lessonLocation' => $core['lessonLocation'],
            'studyMode' => $core['studyMode'],
            'studyLastScale' => $core['studyLastScale'],
            'studyCurrentScale' => $core['studyCurrentScale'],
            'studySchedule' => 100.0,
            'timingMode' => $core['timingMode'],
            'studyStatus' => 1,
            'lessonStatus' => $core['lessonStatus'],
            'token' => $core['token'],
            'intervalTime' => rand(10, 100),
        ];
        echo "\n";
        echo "-------------------------------------timeing start------------------------\n";
        echo json_encode($timeingParam, true);
        echo "-------------------------------------timeing end------------------------\n";

        $timeResponse = $this->curlRequest->curlPost(self::TIMEING_URL, $timeingParam, true);
        if ($initResponse['head']['code'] != 200) exit();
        echo __LINE__.'-------'.self::TIMEING_URL."\n";
        var_dump($timeResponse);
        sleep(rand(3, 10));
    }


    public function run()
    {
        $courseList = $this->getCourseList();
        if (empty($courseList)) {
            echo '课程获取失败';
            exit();
        }
        foreach ($courseList as $value=>$course) {

            $userInfo = $this->getDataFromUrl(self::USER_INFO);

            $courseInfoParam = [
                'exts' => '{"learnType":"TRAINING_CLASS"}',
                'lessonId' => $course['courseId'],
                'mode' => 3,
                'trainClassId' => $course['schemeId'],
            ];
            $courseInfoUrl = self::COURSE_INFO . http_build_query($courseInfoParam);

            $courseInfo = $this->getDataFromUrl($courseInfoUrl);
            echo "----------------".$value.'-------------------';
            foreach ($courseInfo['lesson']['chapterList'][0]['courseWareList'] as $value) {
                $anythingInfoParam = $courseInfoParam;
                $anythingInfoParam['mediaId'] = $value['mediaList'][0]['id'];
                $anythingInfoParam['courseWareId'] = $value['id'];
                $anythingInfoUrl = self::ANYTHING_URL . http_build_query($anythingInfoParam);
                $anythingInfo = $this->getDataFromUrl($anythingInfoUrl);
                $initPlayer = $this->initPlayerAndFinishedOne($courseInfo, $userInfo, $anythingInfo);
            }


        }
        return true;
    }
}

$study = (new fjjsrckj())->run();


class curlRequest
{
    public function request($url, $requestType, $body = "", $extHeader = [])
    {
        $curl = curl_init();
        $header = array(
            "Postman-Token: c690327f-6904-41b7-bf3d-32ad2e238070",
            "cache-control: no-cache"
        );
        if (!empty($extHeader)) $header = array_merge($header, $extHeader);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $requestType,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if (empty($err)) return $response;
        return false;
    }

    /**
     * @param $url
     * @return bool|string
     */
    public function curlGet($url, $cookie)
    {
        return $this->request($url, 'GET', '', $cookie);
    }

    /**
     * @param $url string
     * @param $data []
     * @param bool $isJson
     * @return bool|string
     */
    public function curlPost($url, $data, $isJson = false)
    {
        $header = [];
        if ($isJson) {
            $data = json_encode($data, true);
            $header = [
                'Content-Type: application/json; charset=utf-8',
                'Content-Length:' . strlen($data),
            ];
        } else {
            $data = http_build_query($data);
        }
        return $this->request($url, 'POST', $data, $header);
    }
}
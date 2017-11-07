<?php

class OneSignalDriver
{

    private $apiId = "XXXXXXX";
    private $RESTAPIKey = "XXXXXXX";

    /**
     * returns embed code
     */
    public function getCode()
    {
        return '
            <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async="async"></script>
            <script>
              var OneSignal = window.OneSignal || [];
              OneSignal.push(["init", {
                appId: "'.$this->apiId.'",
                autoRegister: true,
                welcomeNotification: {
                           "title" : "site.com",
                           "message": "Благодарим ви за абонамента",
                   },
                httpPermissionRequest: {
                  enable: true
                },
                notifyButton: {
                          enable: true,
                          size: "small",
                          prenotify: true,
                          showCredit: false,
                          text: {
                                   "tip.state.unsubscribed": "Получaвайте известия от site.com",
                                   "tip.state.subscribed": "Абонирани сте за известия",
                                   "tip.state.blocked": "Блокирахте получаването на известия",
                                   "message.prenotify": "Натиснете тук за абонамент за известия",
                                   "message.action.subscribed": "Благодарим за абонамента!",
                                   "message.action.resubscribed": "Абонирани сте за известия",
                                   "message.action.unsubscribed": "Изпращането на известия е прекратено",
                                   "dialog.main.title": "Настройки на известията",
                                   "dialog.main.button.subscribe": "Абонирайте се",
                                   "dialog.main.button.unsubscribe": "Прекратяване на абонамента",
                                   "dialog.blocked.title": "Отблокиране на известията",
                                   "dialog.blocked.message": "Следвайте инструкциите, за да активирате известията:"
                           },
                }
              }]);
            </script>     
           ';
    }
    
    /**
     * send message
     */
    public function sendMessage($title, $url, $icon, $image = null, $segments = array('All'))
    {
        $content = array(
            "en" => $title,
        );


        $fields = array(
            'app_id' => $this->apiId,
            'included_segments' => $segments,
            'url' => $url,
            'chrome_web_icon' => $icon,
            'contents' => $content
        );

        //image
        if ($image) {
            $fields['chrome_web_image'] = $image;
        }

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $this->RESTAPIKey));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function testMessage()
    {
        $response = $this->sendMessage(
                "тест на кирилица", "http://site.com", "http://site.com/200x200.jpg", "http://site.com/500x500.jpg"
        );

        echo $response;
    }

}

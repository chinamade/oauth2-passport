判断当前有没有jwt-token
   没有：
        http://oauth-test.passport.com/oauth/authorize?response_type=code&client_id=888&state=
   是否登录：
        登录、注册
   请求生成code:
        http://oauth-test.passport.com/oauth/authorize?client_id=888&response_type=code&state=&logged_in_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE1NTM2OTA0MTQsInVpZCI6Miwicm9sZXMiOltdfQ.UfewxaazCn8o4WarjFqnyD44o-zp3ZiS_MkXLbOflJ
   返回应用code:
        xxx?state=&code=a83523a8907dcc990b039cd3ef84d2d7
    应用请求生成token:
        $response = $client->request(
            "http://oauth-test.passport.com/oauth/token",
            $uri,
            [
                'auth'        => [$appId, $appSecret],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code'       => $code,
                    'client_id'  => $appId,
                ],
            ]
        );

   有：
        判断token有效性
http://oauth-test.passport.com/oauth/server/me?appid=888&token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJ4eHgucGFzc3BvcnQuY29tIiwiaWF0IjoxNTUzNjkyMDY0LCJleHAiOjE1NTM3MzUyNzQsInVpZCI6MiwiYXBwaWQiOiI4ODgiLCJyb2xlcyI6W10sImlwIjoiIiwiY2hlY2tzdW0iOiJmYzA3ZjA4ZGJmMTI5MWU1In0.yjuv7TI-YEWUry-AWT3FIzpq6Ove9jiXygpSpIQrxCA
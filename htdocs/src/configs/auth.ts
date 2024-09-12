import os from 'os'

const hostname = os.hostname()

let APP_URL = '/api/'
let themeNameTemp = "单点科技"
let indexDashboardPath = "/dashboards/analytics"
let indexMenuspath = "auth/menus.php"

if(hostname == 'h5.xmjsxy.com')   {
  APP_URL = "https://h5.xmjsxy.com/api/"
  themeNameTemp = "厦门技师"
  indexDashboardPath = "/dashboards/xmjs_wygl"
  indexMenuspath = "auth/menus_xmjs.php"
}
else if(hostname == 'gdgx.dandian.net')   {
  APP_URL = "https://gdgx.dandian.net/api/"
  themeNameTemp = "广东高新"
  indexDashboardPath = "/dashboards/analytics"
  indexMenuspath = "auth/menus.php"
}
else if(hostname == 'dc.gdgxjx.cn')   {
  APP_URL = "http://dc.gdgxjx.cn:9999/api/"
  themeNameTemp = "广东高新"
  indexDashboardPath = "/dashboards/analytics"
  indexMenuspath = "auth/menus.php"
}
else if(hostname == 'localhost')   {
  APP_URL = "http://localhost/api/"
  themeNameTemp = "单点科技"
  indexDashboardPath = "/dashboards/analytics"
  indexMenuspath = "auth/menus.php"
}

export default {
  meEndpoint: APP_URL+'jwt.php?action=refresh',
  loginEndpoint: APP_URL+'jwt.php?action=login',
  logoutEndpoint: APP_URL+'jwt.php?action=logout',
  refreshEndpoint: APP_URL+'jwt.php?action=refresh',
  registerEndpoint: APP_URL+'jwt/register',
  storageTokenKeyName: 'accessToken',
  onTokenExpiration: 'refreshToken', // logout | refreshToken
  backEndApiHost: APP_URL,
  themeName: themeNameTemp,
  indexDashboardPath: indexDashboardPath,
  indexMenuspath: indexMenuspath,
  k: "fbae1da1c3f10b1ce0c75c8f5d3319d0"
}

import os from 'os'

const hostname = os.hostname()

let APP_URL = '/api/'
let themeNameTemp = "数据中心"
let indexDashboardPath = "/dashboards/analytics"
let indexMenuspath = "auth/menus.php"
let indexImageUrl = '/images/pages/auth-v2-login-illustration-light.png'
let logoUrl = '/images/pages/auth-v2-login-illustration-light.png'

if(hostname == 'localhost' || hostname == '127.0.0.1')   {
  APP_URL = "http://localhost:80/api/"
  themeNameTemp = "单点职校数据中心"
  indexDashboardPath = "/dashboards/analytics"
  indexMenuspath = "auth/menus.php"
  indexImageUrl = '/images/pages/auth-v2-login-illustration-light.png'
  logoUrl = '/images/pages/auth-v2-login-illustration-light.png'
}

if(hostname == '110.90.174.66' || 1)   {
  themeNameTemp = "福鼎职校"
  indexDashboardPath = "/dashboards/analytics"
  indexMenuspath = "auth/menus.php"
  indexImageUrl = '/images/school/fdzz/index.jpg'
  logoUrl = '/images/school/fdzz/logo.png'
}

APP_URL = "https://fdzz.dandian.net/api/"

export default {
  meEndpoint: APP_URL+'jwt.php?action=refresh',
  loginEndpoint: APP_URL+'jwt.php?action=login',
  logoutEndpoint: APP_URL+'jwt.php?action=logout',
  refreshEndpoint: APP_URL+'jwt.php?action=refresh',
  registerEndpoint: APP_URL+'jwt/register',
  storageTokenKeyName: 'accessToken',
  storageAccessKeyName: 'accessKey',
  onTokenExpiration: 'refreshToken', // logout | refreshToken
  backEndApiHost: APP_URL,
  themeName: themeNameTemp,
  indexDashboardPath: indexDashboardPath,
  indexMenuspath: indexMenuspath,
  indexImageUrl: indexImageUrl,
  logoUrl: logoUrl
}

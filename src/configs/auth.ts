
const APP_URL = 'http://localhost/api/'

export default {
  meEndpoint: APP_URL+'jwt.php?action=refresh',
  loginEndpoint: APP_URL+'jwt.php?action=login',
  logoutEndpoint: APP_URL+'jwt.php?action=logout',
  registerEndpoint: APP_URL+'jwt/register',
  storageTokenKeyName: 'accessToken',
  onTokenExpiration: 'refreshToken', // logout | refreshToken
  backEndApiHost: APP_URL
}

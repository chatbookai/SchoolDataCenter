// ** React Imports
import { createContext, useEffect, useState, ReactNode } from 'react'

// ** Next Import
import { useRouter } from 'next/router'

// ** Axios
import axios from 'axios'

// ** Config
import authConfig from 'src/configs/auth'
import { DecryptDataAES256GCM } from 'src/configs/functions'

// ** Types
import { AuthValuesType, RegisterParams, LoginParams, ErrCallbackType, UserDataType } from './types'

// ** Defaults
const defaultProvider: AuthValuesType = {
  user: null,
  loading: true,
  setUser: () => null,
  setLoading: () => Boolean,
  login: () => Promise.resolve(),
  logout: () => Promise.resolve(),
  register: () => Promise.resolve(),
  refresh: () => Promise.resolve()
}

const AuthContext = createContext(defaultProvider)

type Props = {
  children: ReactNode
}

const AuthProvider = ({ children }: Props) => {
  // ** States
  const [user, setUser] = useState<UserDataType | null>(defaultProvider.user)
  const [loading, setLoading] = useState<boolean>(defaultProvider.loading)

  // ** Hooks
  const router = useRouter()

  useEffect(() => {
    const initAuth = async (): Promise<void> => {
      const storedToken = window.localStorage.getItem(authConfig.storageTokenKeyName)!
      if (storedToken && storedToken!=undefined) {
        setLoading(true)
        await axios
          .get(authConfig.meEndpoint, {
            headers: {
              Authorization: storedToken
            }
          })
          .then(async response => {
            let dataJson: any = null
            const data = response.data
            if(data && data.isEncrypted == "1" && data.data)  {
                const i = data.data.slice(0, 32);
                const t = data.data.slice(-32);
                const e = data.data.slice(32, -32);
                const k = authConfig.k;
                const DecryptDataAES256GCMData = DecryptDataAES256GCM(e, i, t, k)
                try{
                    dataJson = JSON.parse(DecryptDataAES256GCMData)
                }
                catch(Error: any) {
                    console.log("DecryptDataAES256GCMData view_default Error", Error)
        
                    dataJson = data
                }
            }
            else {

                dataJson = data
            }
            setLoading(false)
            setUser({ ...dataJson.userData })
          })
          .catch(() => {
            localStorage.removeItem('userData')
            localStorage.removeItem('refreshToken')
            localStorage.removeItem(authConfig.storageTokenKeyName)
            localStorage.removeItem('GO_SYSTEM')
            setUser(null)
            setLoading(false)
            if (authConfig.onTokenExpiration === 'logout' && !router.pathname.includes('login')) {
              router.replace('/login')
            }
          })
      } else {
        setLoading(false)
      }
    }

    initAuth()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  const handleLogin = (params: LoginParams, errorCallback?: ErrCallbackType) => {
    axios
      .post(authConfig.loginEndpoint, params)
      .then(async response => {

        let dataJson: any = null
        const data = response.data
        if(data && data.isEncrypted == "1" && data.data)  {
            const i = data.data.slice(0, 32);
            const t = data.data.slice(-32);
            const e = data.data.slice(32, -32);
            const k = authConfig.k;
            const DecryptDataAES256GCMData = DecryptDataAES256GCM(e, i, t, k)
            try{
                dataJson = JSON.parse(DecryptDataAES256GCMData)
            }
            catch(Error: any) {
                console.log("DecryptDataAES256GCMData view_default Error", Error)
    
                dataJson = data
            }
        }
        else {

            dataJson = data
        }
        
        //console.log("authConfig.storageTokenKeyName",authConfig.storageTokenKeyName)
        //console.log("dataJson.accessToken",dataJson.accessToken)
        //console.log("dataJson.userData",dataJson.userData)
        //console.log("JSON.stringify(dataJson.userData)",JSON.stringify(dataJson.userData))
        if(dataJson.userData!=undefined && dataJson.accessToken!=undefined)  {
          true
            ? window.localStorage.setItem(authConfig.storageTokenKeyName, dataJson.accessToken)
            : null
          const returnUrl = router.query.returnUrl
          setUser({ ...dataJson.userData })
          true ? window.localStorage.setItem('userData', JSON.stringify(dataJson.userData)) : null
          true ? window.localStorage.setItem('GO_SYSTEM', JSON.stringify(dataJson.GO_SYSTEM)) : null
          const redirectURL = returnUrl && returnUrl !== '/' ? returnUrl : '/'
          router.replace(redirectURL as string)
        }
        else {
          setUser(null)
          window.localStorage.removeItem('userData')
          window.localStorage.removeItem('GO_SYSTEM')
          window.localStorage.removeItem(authConfig.storageTokenKeyName)
          if (errorCallback) errorCallback({})
        }
      })
      .catch(err => {
        if (errorCallback) errorCallback(err)
      })
  }

  const handleRefreshToken = () => {
    const token = window.localStorage.getItem(authConfig.storageTokenKeyName)
    if(window && token)  {
      axios
        .post(authConfig.refreshEndpoint, {}, { headers: { Authorization: token, 'Content-Type': 'application/json'} })
        .then(async (response: any) => {

          let dataJson: any = null
          const data = response.data
          if(data && data.isEncrypted == "1" && data.data)  {
              const i = data.data.slice(0, 32);
              const t = data.data.slice(-32);
              const e = data.data.slice(32, -32);
              const k = authConfig.k;
              const DecryptDataAES256GCMData = DecryptDataAES256GCM(e, i, t, k)
              try{
                  dataJson = JSON.parse(DecryptDataAES256GCMData)
              }
              catch(Error: any) {
                  console.log("DecryptDataAES256GCMData view_default Error", Error)
      
                  dataJson = data
              }
          }
          else {

              dataJson = data
          }
          
          if(dataJson.status == 'ok' && dataJson.accessToken) {
            window.localStorage.setItem(authConfig.storageTokenKeyName, dataJson.accessToken)

            setUser({ ...dataJson.userData })
          }
          else {
          }
        })
    }
  }

  const handleLogout = () => {
    setUser(null)
    window.localStorage.removeItem('userData')
    window.localStorage.removeItem('GO_SYSTEM')
    window.localStorage.removeItem(authConfig.storageTokenKeyName)
    router.push('/login')
  }

  const handleRegister = (params: RegisterParams, errorCallback?: ErrCallbackType) => {
    axios
      .post(authConfig.registerEndpoint, params)
      .then(res => {
        if (res.data.error) {
          if (errorCallback) errorCallback(res.data.error)
        } else {
          handleLogin({ username: params.username, password: params.password })
        }
      })
      .catch((err: { [key: string]: string }) => (errorCallback ? errorCallback(err) : null))
  }

  const values = {
    user,
    loading,
    setUser,
    setLoading,
    login: handleLogin,
    logout: handleLogout,
    register: handleRegister,
    refresh: handleRefreshToken
  }

  return <AuthContext.Provider value={values}>{children}</AuthContext.Provider>
}

export { AuthContext, AuthProvider }

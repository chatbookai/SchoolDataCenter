// ** React Imports
import { useEffect, useState } from 'react'

// ** Axios Import
import axios from 'axios'

// ** Config
import { authConfig, defaultConfig } from 'src/configs/auth'

// ** Type Import
import { HorizontalNavItemsType } from 'src/@core/layouts/types'
import { DecryptDataAES256GCM } from 'src/configs/functions'

const ServerSideNavItems = () => {
  // ** State
  const [menuItems, setMenuItems] = useState<HorizontalNavItemsType>([])
  const backEndApi = authConfig['indexMenuspath']

  useEffect(() => {
    const storedToken = window.localStorage.getItem(defaultConfig.storageTokenKeyName)!
    axios.get(authConfig.backEndApiHost + backEndApi, { headers: { Authorization: storedToken } }).then(res => {

      let dataJson: any = null
      const data = res.data
      if(data && data.isEncrypted == "1" && data.data)  {
          const AccessKey = window.localStorage.getItem(defaultConfig.storageAccessKeyName)!
          const i = data.data.slice(0, 32);
          const t = data.data.slice(-32);
          const e = data.data.slice(32, -32);
          const k = AccessKey;
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

      const menuArray = dataJson
      if(menuArray) {
        setMenuItems(menuArray)
      }
    })
  }, [])

  return { menuItems }
}

export default ServerSideNavItems

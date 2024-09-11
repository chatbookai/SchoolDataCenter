// ** Next Import
import { useState, useEffect, Fragment } from 'react'
import { useRouter } from 'next/router'

// ** Demo Components Imports
import TabHeader from './TabHeader'

// ** Config
import authConfig from 'src/configs/auth'
import axios from 'axios'

const TabHeaderTab = () => {
  const router = useRouter()
  const _GET = router.query
  const tab = String(_GET.tab)
  const [tabData, setTabData] = useState<{[key:string]:any}>({})

  useEffect(() => {
    const storedToken = window.localStorage.getItem(authConfig.storageTokenKeyName)!
    const backEndApi = 'tab_all_data.php';
    axios.get(authConfig.backEndApiHost + backEndApi, {
      headers: { Authorization: storedToken },
      params: {}
    }).then(res => {
      if (res.status == 200) {
        setTabData(res.data)
      }
    })
    
  }, []) 
  
  return (
    <Fragment>
      { tabData && Object.keys(tabData).length>0 && tab && tab!="undefined" ? <TabHeader tab={tab} allTabs={tabData} /> : ''}
    </Fragment>
  )
}

export default TabHeaderTab

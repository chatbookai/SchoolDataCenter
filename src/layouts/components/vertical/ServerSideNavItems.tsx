// ** React Imports
import { useEffect, useState } from 'react'

// ** Axios Import
import axios from 'axios'

// ** Config
import authConfig from 'src/configs/auth'

// ** Type Import
import { VerticalNavItemsType } from 'src/@core/layouts/types'

const ServerSideNavItems = () => {
  // ** State
  const [menuItems, setMenuItems] = useState<VerticalNavItemsType>([])
  const backEndApi = "auth/menus.php"

  useEffect(() => {
    const storedToken = window.localStorage.getItem(authConfig.storageTokenKeyName)!
    axios.get(authConfig.backEndApiHost + backEndApi, { headers: { Authorization: storedToken } }).then(response => {
      //axios.get('http://react.admin.test/auth/menus.php').then(response => {
      //axios.get('/api/vertical-nav/data').then(response => {
      const menuArray = response.data
      setMenuItems(menuArray)
    })
  }, [])

  return { menuItems }
}

export default ServerSideNavItems

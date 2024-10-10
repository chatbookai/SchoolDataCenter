// ** React Imports
import { useState, Fragment } from 'react'

import Footer from '../Layout/Footer'
import Setting from '../Setting/Setting'
import Login from '../Login/Login'

const Home = () => {

  const [currentTab, setCurrentTab] = useState<string>('Login')
  const [disabledFooter, setDisabledFooter] = useState<boolean>(true)

  return (
    <Fragment>
      {currentTab == "Login" && (<Login />)}
      {currentTab == "Setting" && (<Setting />)}
      <Footer Hidden={false} setCurrentTab={setCurrentTab} currentTab={currentTab} disabledFooter={disabledFooter} setDisabledFooter={setDisabledFooter} />
    </Fragment>
  )
}

export default Home

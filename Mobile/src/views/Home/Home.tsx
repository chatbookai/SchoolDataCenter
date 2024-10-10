// ** React Imports
import { useState, Fragment } from 'react'

import Footer from '../Layout/Footer'
import Setting from '../Setting/Setting'
import Index from '../Index/Index'
import Login from '../Login/Login'

const Home = () => {

  const [currentTab, setCurrentTab] = useState<string>('Login')
  const [disabledFooter, setDisabledFooter] = useState<boolean>(true)

  return (
    <Fragment>
      {currentTab == "Login" && (<Login setCurrentTab={setCurrentTab}/>)}
      {currentTab == "Setting" && (<Setting />)}
      {currentTab == "Index" && (<Index />)}
      <Footer Hidden={false} setCurrentTab={setCurrentTab} currentTab={currentTab} disabledFooter={disabledFooter} setDisabledFooter={setDisabledFooter} />
    </Fragment>
  )
}

export default Home

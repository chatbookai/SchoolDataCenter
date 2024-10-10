// ** React Imports
import { useState, Fragment } from 'react'

import Footer from '../Layout/Footer'
import Setting from '../Setting/Setting'

const HomeModel = () => {

  const [currentTab, setCurrentTab] = useState<string>('Setting')
  const [disabledFooter, setDisabledFooter] = useState<boolean>(true)
  const [encryptWalletDataKey, setEncryptWalletDataKey] = useState<string>('')

  return (
    <Fragment>
      {currentTab == "Setting" && (<Setting encryptWalletDataKey={encryptWalletDataKey} setEncryptWalletDataKey={setEncryptWalletDataKey} />)}
      <Footer Hidden={false} setCurrentTab={setCurrentTab} currentTab={currentTab} disabledFooter={disabledFooter} setDisabledFooter={setDisabledFooter} />
    </Fragment>
  )
}

export default HomeModel


// ** Hooks
import UserList from 'src/views/Enginee/index'
import { authConfig } from 'src/configs/auth'

const AppChat = () => {
  // ** States
  const backEndApi = "form_datacode.php"

  return (
    <UserList authConfig={authConfig} backEndApi={backEndApi} externalId=''/>
  )
}


export default AppChat

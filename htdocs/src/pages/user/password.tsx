
// ** Hooks
import UserList from 'src/views/Enginee/index'

const AppChat = () => {
  // ** States
  const backEndApi = "user_password.php"
  
  return (
    <UserList backEndApi={backEndApi} externalId=''/>
  )
}


export default AppChat

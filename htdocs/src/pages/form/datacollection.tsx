
// ** Hooks
import UserList from 'src/views/Enginee/index'

const AppChat = () => {
  // ** States
  const backEndApi = "form_datacollection.php"
  
  return (
    <UserList backEndApi={backEndApi} externalId=''/>
  )
}


export default AppChat
